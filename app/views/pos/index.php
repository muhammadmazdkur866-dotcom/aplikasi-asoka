<?php
$pageTitle = 'Kasir Point of Sale (POS)';
require __DIR__ . '/../layouts/header.php';
?>

<div class="row g-3">
    <!-- Kolom Kiri: Katalog Produk Sembako -->
    <div class="col-12 col-xl-7">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header bg-white pb-0 border-0">
                <!-- Search & Barcode Bar -->
                <div class="input-group input-group-lg mb-3 shadow-sm">
                    <span class="input-group-text bg-white border-end-0 text-secondary">
                        <i class="bi bi-upc-scan fs-4"></i>
                    </span>
                    <input type="text" class="form-control border-start-0 ps-0" id="searchProductInput" 
                           placeholder="Scan Barcode atau ketik nama barang... [Tekan F2]" autofocus>
                    <button class="btn btn-outline-secondary" type="button" onclick="document.getElementById('searchProductInput').value = ''; filterProducts();">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <!-- Kategori Filter Tabs -->
                <div class="d-flex gap-2 overflow-auto pb-3 text-nowrap" id="categoryFilterContainer">
                    <button type="button" class="btn btn-sm btn-dark cat-pill fw-semibold" onclick="setCategoryFilter('', this)">
                        Semua Kategori
                    </button>
                    <?php foreach ($kategoriList as $kat): ?>
                        <button type="button" class="btn btn-sm btn-light border cat-pill text-dark" onclick="setCategoryFilter('<?= $kat['id_kategori'] ?>', this)">
                            <?= htmlspecialchars($kat['nama_kategori']) ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Grid Produk -->
            <div class="card-body pt-1" style="max-height: calc(100vh - 290px); overflow-y: auto;">
                <div class="row g-3" id="productGrid">
                    <?php foreach ($barangList as $p): ?>
                        <div class="col-6 col-sm-4 col-md-3 col-xl-4 product-item-col" 
                             data-category="<?= $p['id_kategori'] ?>" 
                             data-name="<?= htmlspecialchars(strtolower($p['nama_barang'])) ?>" 
                             data-code="<?= htmlspecialchars(strtolower($p['id_barang'])) ?>">
                            
                            <div class="card pos-product-card h-100 p-2 position-relative" onclick='addToCart(<?= json_encode($p) ?>)'>
                                <?php if ($p['stok'] <= 0): ?>
                                    <span class="position-absolute top-0 start-0 translate-middle-y badge bg-danger ms-2">Habis</span>
                                <?php elseif ($p['stok'] <= $p['stok_minimum']): ?>
                                    <span class="position-absolute top-0 start-0 translate-middle-y badge bg-warning text-dark ms-2">Sisa <?= $p['stok'] ?></span>
                                <?php endif; ?>

                                <div class="text-truncate fw-bold text-dark mb-1 small" title="<?= htmlspecialchars($p['nama_barang']) ?>">
                                    <?= htmlspecialchars($p['nama_barang']) ?>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="badge bg-light text-secondary border font-monospace" style="font-size: 0.65rem;">
                                        <?= htmlspecialchars($p['id_barang']) ?>
                                    </span>
                                    <small class="text-muted" style="font-size: 0.7rem;">Stok: <?= $p['stok'] ?> <?= $p['satuan'] ?></small>
                                </div>
                                <div class="mt-auto pt-1 d-flex justify-content-between align-items-center">
                                    <span class="price-tag small"><?= format_rupiah($p['harga_jual']) ?></span>
                                    <button type="button" class="btn btn-sm btn-light border rounded-circle text-secondary p-1" style="width: 26px; height: 26px;">
                                        <i class="bi bi-plus-lg"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Keranjang Kasir & Pembayaran -->
    <div class="col-12 col-xl-5">
        <div class="card h-100 shadow-sm border-0 d-flex flex-column">
            <!-- Header Keranjang -->
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-cart3 me-2 text-secondary"></i>Keranjang Kasir</h5>
                    <small class="text-muted font-monospace"><?= htmlspecialchars($nextInvoice) ?> &bull; Kasir: <?= htmlspecialchars($currentUser['nama_lengkap']) ?></small>
                </div>
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="clearCart()" title="Kosongkan Keranjang">
                    <i class="bi bi-trash"></i> Reset
                </button>
            </div>

            <!-- Tabel Daftar Belanja -->
            <div class="card-body p-0 flex-grow-1" style="max-height: calc(100vh - 460px); min-height: 250px; overflow-y: auto;">
                <div class="table-responsive">
                    <table class="table cart-table align-middle mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th class="ps-3">Produk</th>
                                <th class="text-center" style="width: 140px;">Qty</th>
                                <th class="text-end">Subtotal</th>
                                <th class="text-center pe-3" style="width: 40px;"></th>
                            </tr>
                        </thead>
                        <tbody id="cartTableBody">
                            <!-- Di-render oleh JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer Kasir: Total & Tombol Bayar -->
            <div class="card-footer bg-white border-top p-3 mt-auto">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted small">Total Barang</span>
                    <span class="fw-semibold small" id="cartTotalItems">0 Item</span>
                </div>
                <div class="d-flex justify-content-between align-items-baseline mb-3">
                    <span class="fs-5 fw-bold text-dark">Grand Total</span>
                    <span class="fs-3 fw-bold text-dark" id="cartGrandTotal">Rp 0</span>
                </div>

                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-dark btn-lg fw-bold py-3 shadow" id="btnBayarModal" onclick="openPaymentModal()" disabled>
                        <i class="bi bi-cash-coin me-2"></i> BAYAR SEKARANG [F8]
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pembayaran Kasir -->
<div class="modal fade" id="modalPembayaran" tabindex="-1" aria-labelledby="modalPembayaranLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold" id="modalPembayaranLabel"><i class="bi bi-wallet2 me-2"></i>Pembayaran Transaksi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="modalTotalBayarRaw" value="0">

                <div class="text-center mb-4 p-3 bg-light rounded-3 border">
                    <div class="text-muted small fw-medium">TOTAL TAGIHAN</div>
                    <div class="display-6 fw-bold text-dark" id="modalTotalBayarText">Rp 0</div>
                </div>

                <div class="mb-3">
                    <label for="inputUangTunai" class="form-label fw-bold">Nominal Uang Tunai Diterima (Rp) <span class="text-danger">*</span></label>
                    <div class="input-group input-group-lg">
                        <span class="input-group-text fw-bold">Rp</span>
                        <input type="number" class="form-control form-control-lg fw-bold text-end" id="inputUangTunai" placeholder="0" min="0" step="500">
                    </div>
                    <div id="kembalianAlert" class="text-danger small mt-1 d-none fw-semibold"></div>
                </div>

                <!-- Tombol Pecahan Uang Cepat -->
                <div class="mb-3">
                    <label class="form-label small text-muted fw-semibold">Pecahan Cepat:</label>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-quick-cash" onclick="setUangCepat('pas')">Uang Pas</button>
                        <button type="button" class="btn btn-quick-cash" onclick="setUangCepat(10000)">10.000</button>
                        <button type="button" class="btn btn-quick-cash" onclick="setUangCepat(20000)">20.000</button>
                        <button type="button" class="btn btn-quick-cash" onclick="setUangCepat(50000)">50.000</button>
                        <button type="button" class="btn btn-quick-cash" onclick="setUangCepat(100000)">100.000</button>
                        <button type="button" class="btn btn-quick-cash" onclick="setUangCepat(200000)">200.000</button>
                    </div>
                </div>

                <div class="p-3 bg-light rounded-3 border mb-3 d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-dark">Uang Kembalian:</span>
                    <span class="fs-4 fw-bold text-muted" id="modalKembalianText">Rp 0</span>
                </div>

                <div class="mb-0">
                    <label for="catatanTransaksi" class="form-label small text-muted">Catatan Transaksi (Opsional)</label>
                    <input type="text" class="form-control form-control-sm" id="catatanTransaksi" placeholder="Contoh: Pelanggan Langganan">
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal [Esc]</button>
                <button type="button" class="btn btn-success fw-bold px-4" id="btnSubmitTransaksi" onclick="submitTransaksi()" disabled>
                    <i class="bi bi-printer me-1"></i> Bayar & Cetak Struk
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Inject Data Produk untuk Frontend JS -->
<script>
    window.BASE_URL = '<?= BASE_URL ?>';
    window.allProducts = <?= json_encode($barangList) ?>;
</script>
<script src="<?= BASE_URL ?>assets/js/pos.js"></script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

