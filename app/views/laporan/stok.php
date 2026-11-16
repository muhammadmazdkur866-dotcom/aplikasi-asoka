<?php
$pageTitle = 'Laporan Stok Barang';
require __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold text-dark mb-1">Laporan Persediaan & Stok Barang</h4>
        <p class="text-muted small mb-0">Monitoring stok fisik, peringatan barang menipis (low stock), dan valuasi aset gudang.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= BASE_URL ?>index.php?page=laporan&action=cetak_stok&kategori=<?= urlencode($_GET['kategori'] ?? '') ?>&status=<?= urlencode($_GET['status'] ?? 'all') ?>" target="_blank" class="btn btn-outline-success d-flex align-items-center gap-2 shadow-sm">
            <i class="bi bi-printer"></i> Cetak Laporan Stok
        </a>
    </div>
</div>

<!-- Filter Card -->
<div class="card mb-4 shadow-sm border-0">
    <div class="card-body">
        <form method="GET" action="<?= BASE_URL ?>index.php" class="row g-3">
            <input type="hidden" name="page" value="laporan">
            <input type="hidden" name="action" value="stok">

            <div class="col-12 col-md-5">
                <label class="form-label small fw-semibold text-muted">Kategori Produk</label>
                <select name="kategori" class="form-select">
                    <option value="">-- Semua Kategori --</option>
                    <?php foreach ($kategoriList as $kat): ?>
                        <option value="<?= $kat['id_kategori'] ?>" <?= (isset($_GET['kategori']) && $_GET['kategori'] == $kat['id_kategori']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($kat['nama_kategori']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label small fw-semibold text-muted">Status Persediaan</label>
                <select name="status" class="form-select">
                    <option value="all" <?= ($filterStatus === 'all') ? 'selected' : '' ?>>Semua Status</option>
                    <option value="low" <?= ($filterStatus === 'low') ? 'selected' : '' ?>>Stok Menipis (&le; Min. Stok)</option>
                    <option value="empty" <?= ($filterStatus === 'empty') ? 'selected' : '' ?>>Stok Habis (0)</option>
                    <option value="safe" <?= ($filterStatus === 'safe') ? 'selected' : '' ?>>Stok Aman</option>
                </select>
            </div>

            <div class="col-12 col-md-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-funnel me-1"></i> Filter</button>
                <a href="<?= BASE_URL ?>index.php?page=laporan&action=stok" class="btn btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Valuation Summary -->
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="card p-3 border-0 shadow-sm text-center">
            <small class="text-muted fw-semibold">Total Unit Fisik Barang</small>
            <h4 class="fw-bold text-dark mt-1 mb-0"><?= number_format($stockValues['total_stok_fisik'] ?? 0, 0, ',', '.') ?> Unit</h4>
            <span class="text-muted small">tersimpan di toko/gudang</span>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card p-3 border-0 shadow-sm text-center border-start border-4 border-info">
            <small class="text-muted fw-semibold">Total Nilai Aset Modal (HPP)</small>
            <h4 class="fw-bold text-info mt-1 mb-0"><?= format_rupiah($stockValues['total_modal']) ?></h4>
            <span class="text-muted small">kalkulasi modal persediaan</span>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card p-3 border-0 shadow-sm text-center border-start border-4 border-success">
            <small class="text-muted fw-semibold">Potensi Nilai Penjualan</small>
            <h4 class="fw-bold text-success mt-1 mb-0"><?= format_rupiah($stockValues['total_potensi_jual']) ?></h4>
            <span class="text-muted small">jika seluruh stok terjual</span>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;" class="text-center">#</th>
                        <th>Kode</th>
                        <th>Nama Produk Sembako</th>
                        <th>Kategori</th>
                        <th class="text-end">Harga Beli</th>
                        <th class="text-end">Harga Jual</th>
                        <th class="text-center">Stok</th>
                        <th class="text-center">Min. Stok</th>
                        <th class="text-end">Total Aset (Modal)</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($barangList)): ?>
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                Tidak ada data stok barang yang sesuai dengan filter.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach (array_values($barangList) as $i => $b): ?>
                            <?php $subAset = $b['stok'] * $b['harga_beli']; ?>
                            <tr>
                                <td class="text-center text-muted"><?= $i + 1 ?></td>
                                <td><span class="badge bg-light text-dark border font-monospace"><?= htmlspecialchars($b['id_barang']) ?></span></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($b['nama_barang']) ?></div>
                                    <small class="text-muted">Satuan: <?= htmlspecialchars($b['satuan']) ?></small>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($b['nama_kategori']) ?></span></td>
                                <td class="text-end text-muted small"><?= format_rupiah($b['harga_beli']) ?></td>
                                <td class="text-end fw-semibold text-dark small"><?= format_rupiah($b['harga_jual']) ?></td>
                                <td class="text-center fw-bold fs-6 <?= $b['stok'] <= $b['stok_minimum'] ? 'text-danger' : 'text-dark' ?>">
                                    <?= $b['stok'] ?>
                                </td>
                                <td class="text-center text-muted"><?= $b['stok_minimum'] ?></td>
                                <td class="text-end fw-semibold text-dark"><?= format_rupiah($subAset) ?></td>
                                <td class="text-center">
                                    <?php if ($b['stok'] <= 0): ?>
                                        <span class="badge bg-danger">Habis</span>
                                    <?php elseif ($b['stok'] <= $b['stok_minimum']): ?>
                                        <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle me-1"></i>Menipis</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Aman</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

