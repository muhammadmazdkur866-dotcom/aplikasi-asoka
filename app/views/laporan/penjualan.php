<?php
$pageTitle = 'Laporan Penjualan';
require __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold text-dark mb-1">Laporan Rekapitulasi Penjualan</h4>
        <p class="text-muted small mb-0">Pantau omset, modal pokok penjualan, laba kotor, dan rincian transaksi kasir.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= BASE_URL ?>index.php?page=laporan&action=cetak_penjualan&start_date=<?= urlencode($startDate) ?>&end_date=<?= urlencode($endDate) ?>" target="_blank" class="btn btn-outline-success d-flex align-items-center gap-2 shadow-sm">
            <i class="bi bi-printer"></i> Cetak Laporan Penjualan
        </a>
    </div>
</div>

<!-- Filter Card -->
<div class="card mb-4 shadow-sm border-0">
    <div class="card-body">
        <form method="GET" action="<?= BASE_URL ?>index.php" class="row g-3 align-items-end">
            <input type="hidden" name="page" value="laporan">
            <input type="hidden" name="action" value="penjualan">

            <div class="col-12 col-md-4">
                <label class="form-label small fw-semibold text-muted">Tanggal Awal</label>
                <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($startDate) ?>">
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label small fw-semibold text-muted">Tanggal Akhir</label>
                <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($endDate) ?>">
            </div>

            <div class="col-12 col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-funnel me-1"></i> Tampilkan Rekap</button>
                <a href="<?= BASE_URL ?>index.php?page=laporan&action=penjualan" class="btn btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card p-3 border-0 shadow-sm text-center">
            <small class="text-muted fw-semibold">Total Omset Penjualan</small>
            <h4 class="fw-bold text-success mt-1 mb-0"><?= format_rupiah($totalOmset) ?></h4>
            <span class="text-muted small"><?= $totalQty ?> total item terjual</span>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card p-3 border-0 shadow-sm text-center">
            <small class="text-muted fw-semibold">Total Modal Barang (HPP)</small>
            <h4 class="fw-bold text-dark mt-1 mb-0"><?= format_rupiah($totalModal) ?></h4>
            <span class="text-muted small">harga beli dari supplier</span>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card p-3 border-0 shadow-sm text-center border-start border-4 border-primary">
            <small class="text-muted fw-semibold">Total Laba Kotor</small>
            <h4 class="fw-bold text-primary mt-1 mb-0"><?= format_rupiah($totalLaba) ?></h4>
            <span class="text-muted small">omset dikurangi modal</span>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card p-3 border-0 shadow-sm text-center">
            <small class="text-muted fw-semibold">Rata-rata Margin Laba</small>
            <h4 class="fw-bold text-warning mt-1 mb-0">
                <?= $totalOmset > 0 ? round(($totalLaba / $totalOmset) * 100, 1) : 0 ?>%
            </h4>
            <span class="text-muted small">margin laba kotor</span>
        </div>
    </div>
</div>

<!-- Table Card -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-list-check me-2 text-success"></i>Rincian Penjualan Barang</h6>
        <span class="badge bg-light text-muted border"><?= count($laporanList) ?> Baris Transaksi</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;" class="text-center">#</th>
                        <th>No. Invoice</th>
                        <th>Waktu</th>
                        <th>Kasir</th>
                        <th>Nama Produk</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end">Harga Jual</th>
                        <th class="text-end">Subtotal</th>
                        <th class="text-end">Modal</th>
                        <th class="text-end">Laba Kotor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($laporanList)): ?>
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                Tidak ada data transaksi penjualan pada rentang tanggal tersebut.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($laporanList as $i => $row): ?>
                            <tr>
                                <td class="text-center text-muted"><?= $i + 1 ?></td>
                                <td>
                                    <a href="<?= BASE_URL ?>index.php?page=transaksi&action=detail&id=<?= urlencode($row['id_transaksi']) ?>" class="font-monospace fw-semibold text-primary text-decoration-none">
                                        <?= htmlspecialchars($row['id_transaksi']) ?>
                                    </a>
                                </td>
                                <td class="small text-muted"><?= date('d/m/Y H:i', strtotime($row['tanggal_waktu'])) ?></td>
                                <td class="small"><?= htmlspecialchars($row['nama_kasir']) ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($row['nama_barang']) ?></div>
                                    <small class="text-muted"><?= htmlspecialchars($row['nama_kategori']) ?></small>
                                </td>
                                <td class="text-center fw-semibold"><?= $row['kuantitas'] ?></td>
                                <td class="text-end text-muted small"><?= format_rupiah($row['harga_satuan']) ?></td>
                                <td class="text-end fw-bold text-success"><?= format_rupiah($row['subtotal']) ?></td>
                                <td class="text-end text-muted small"><?= format_rupiah($row['total_modal']) ?></td>
                                <td class="text-end fw-bold text-primary"><?= format_rupiah($row['laba']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="5" class="text-end">TOTAL REKAPITULASI:</th>
                        <th class="text-center fw-bold"><?= $totalQty ?></th>
                        <th></th>
                        <th class="text-end text-success fw-bold"><?= format_rupiah($totalOmset) ?></th>
                        <th class="text-end text-muted fw-bold"><?= format_rupiah($totalModal) ?></th>
                        <th class="text-end text-primary fw-bold"><?= format_rupiah($totalLaba) ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

