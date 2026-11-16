<?php
$pageTitle = 'Detail Transaksi - ' . htmlspecialchars($transaksi['id_transaksi']);
require __DIR__ . '/../layouts/header.php';
?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-9">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 gap-2">
            <div>
                <h4 class="fw-bold text-dark mb-1">Detail Transaksi Penjualan</h4>
                <p class="text-muted small mb-0">Rincian item produk yang dibeli pada invoice <span class="font-monospace fw-semibold"><?= htmlspecialchars($transaksi['id_transaksi']) ?></span>.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= BASE_URL ?>index.php?page=transaksi" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Riwayat
                </a>
                <a href="<?= BASE_URL ?>index.php?page=pos&action=cetak_struk&id=<?= urlencode($transaksi['id_transaksi']) ?>" target="_blank" class="btn btn-success btn-sm">
                    <i class="bi bi-printer me-1"></i> Cetak Struk
                </a>
            </div>
        </div>

        <!-- Info Box Card -->
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <small class="text-muted d-block">Nomor Invoice</small>
                        <span class="fw-bold font-monospace text-primary fs-6"><?= htmlspecialchars($transaksi['id_transaksi']) ?></span>
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-muted d-block">Waktu Transaksi</small>
                        <span class="fw-semibold text-dark"><?= date('d/m/Y H:i', strtotime($transaksi['tanggal_waktu'])) ?> WIB</span>
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-muted d-block">Kasir Bertugas</small>
                        <span class="fw-semibold text-dark"><?= htmlspecialchars($transaksi['nama_kasir']) ?></span>
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-muted d-block">Status Pembayaran</small>
                        <span class="badge bg-success">Lunas (Tunai)</span>
                    </div>
                    <?php if (!empty($transaksi['catatan'])): ?>
                        <div class="col-12 pt-2 border-top">
                            <small class="text-muted d-block">Catatan:</small>
                            <span class="text-dark small"><?= htmlspecialchars($transaksi['catatan']) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Table Items Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-basket3 me-2 text-success"></i>Daftar Item Belanja</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;" class="text-center">#</th>
                                <th>Produk Sembako</th>
                                <th>Kategori</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Harga Satuan</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $totalQty = 0; ?>
                            <?php foreach ($items as $i => $item): ?>
                                <?php $totalQty += $item['kuantitas']; ?>
                                <tr>
                                    <td class="text-center text-muted"><?= $i + 1 ?></td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($item['nama_barang']) ?></div>
                                        <small class="text-muted font-monospace"><?= htmlspecialchars($item['id_barang']) ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border"><?= htmlspecialchars($item['nama_kategori']) ?></span>
                                    </td>
                                    <td class="text-center fw-semibold">
                                        <?= $item['kuantitas'] ?> <?= htmlspecialchars($item['satuan']) ?>
                                    </td>
                                    <td class="text-end text-muted"><?= format_rupiah($item['harga_satuan']) ?></td>
                                    <td class="text-end fw-bold text-success"><?= format_rupiah($item['subtotal']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="3" class="text-end">Total Kuantitas:</th>
                                <th class="text-center"><?= $totalQty ?></th>
                                <th class="text-end">Total Belanja:</th>
                                <th class="text-end fs-6 text-success fw-bold"><?= format_rupiah($transaksi['total_bayar']) ?></th>
                            </tr>
                            <tr>
                                <th colspan="5" class="text-end text-muted">Nominal Uang Tunai:</th>
                                <th class="text-end text-muted"><?= format_rupiah($transaksi['uang_tunai']) ?></th>
                            </tr>
                            <tr>
                                <th colspan="5" class="text-end text-muted">Kembalian:</th>
                                <th class="text-end text-muted"><?= format_rupiah($transaksi['kembalian']) ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

