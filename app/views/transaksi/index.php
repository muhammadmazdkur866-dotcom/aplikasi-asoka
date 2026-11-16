<?php
$pageTitle = 'Riwayat Transaksi Penjualan';
require __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold text-dark mb-1">Riwayat Transaksi Penjualan</h4>
        <p class="text-muted small mb-0">Daftar rekaman transaksi kasir, detail item belanja, dan cetak ulang struk.</p>
    </div>
    <div>
        <a href="<?= BASE_URL ?>index.php?page=pos" class="btn btn-success d-flex align-items-center gap-2 shadow-sm">
            <i class="bi bi-cart-plus"></i> Transaksi Kasir Baru
        </a>
    </div>
</div>

<!-- Filter Box -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?= BASE_URL ?>index.php" class="row g-3 align-items-end">
            <input type="hidden" name="page" value="transaksi">

            <div class="col-12 col-md-3">
                <label class="form-label small fw-semibold text-muted">Tanggal Awal</label>
                <input type="date" name="start_date" class="form-control" value="<?= htmlspecialchars($_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'))) ?>">
            </div>

            <div class="col-12 col-md-3">
                <label class="form-label small fw-semibold text-muted">Tanggal Akhir</label>
                <input type="date" name="end_date" class="form-control" value="<?= htmlspecialchars($_GET['end_date'] ?? date('Y-m-d')) ?>">
            </div>

            <div class="col-12 col-md-4">
                <label class="form-label small fw-semibold text-muted">Pencarian</label>
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" class="form-control" placeholder="No. Invoice atau nama kasir..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                </div>
            </div>

            <div class="col-12 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-funnel"></i> Filter</button>
                <a href="<?= BASE_URL ?>index.php?page=transaksi" class="btn btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Table Card -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;" class="text-center">#</th>
                        <th>No. Invoice</th>
                        <th>Tanggal & Waktu</th>
                        <th>Kasir Bertugas</th>
                        <th class="text-center">Total Item</th>
                        <th class="text-end">Total Belanja</th>
                        <th class="text-end">Uang Tunai</th>
                        <th class="text-end">Kembalian</th>
                        <th style="width: 150px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transaksiList)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-receipt fs-1 d-block mb-2 text-secondary"></i>
                                Tidak ditemukan riwayat transaksi pada filter rentang tanggal ini.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($transaksiList as $i => $tr): ?>
                            <tr>
                                <td class="text-center text-muted"><?= $i + 1 ?></td>
                                <td>
                                    <span class="fw-bold font-monospace text-primary"><?= htmlspecialchars($tr['id_transaksi']) ?></span>
                                </td>
                                <td class="small text-muted">
                                    <i class="bi bi-clock me-1"></i><?= date('d/m/Y H:i', strtotime($tr['tanggal_waktu'])) ?>
                                </td>
                                <td>
                                    <span class="small fw-semibold"><?= htmlspecialchars($tr['nama_kasir']) ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border"><?= $tr['total_qty'] ?> Item</span>
                                </td>
                                <td class="text-end fw-bold text-success">
                                    <?= format_rupiah($tr['total_bayar']) ?>
                                </td>
                                <td class="text-end text-muted small">
                                    <?= format_rupiah($tr['uang_tunai']) ?>
                                </td>
                                <td class="text-end text-muted small">
                                    <?= format_rupiah($tr['kembalian']) ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?= BASE_URL ?>index.php?page=transaksi&action=detail&id=<?= urlencode($tr['id_transaksi']) ?>" class="btn btn-sm btn-outline-info me-1" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?= BASE_URL ?>index.php?page=pos&action=cetak_struk&id=<?= urlencode($tr['id_transaksi']) ?>" target="_blank" class="btn btn-sm btn-outline-success me-1" title="Cetak Ulang Struk">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                    <?php if ($currentUser['role'] === 'admin'): ?>
                                        <a href="<?= BASE_URL ?>index.php?page=transaksi&action=delete&id=<?= urlencode($tr['id_transaksi']) ?>" class="btn btn-sm btn-outline-danger" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus transaksi <?= htmlspecialchars(addslashes($tr['id_transaksi'])) ?>?');">
                                            <i class="bi bi-trash"></i>
                                        </a>
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

