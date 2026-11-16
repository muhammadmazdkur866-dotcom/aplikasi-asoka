<?php
$pageTitle = 'Analisis Apriori (Market Basket Analysis)';
require __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold text-dark mb-1">Market Basket Analysis (Algoritma Apriori)</h4>
        <p class="text-muted small mb-0">Temukan pola keterkaitan dan aturan asosiasi antar barang sembako yang sering dibeli bersamaan oleh konsumen.</p>
    </div>
</div>

<!-- Parameter Form Card -->
<div class="card mb-4 shadow-sm border-0">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center">
        <i class="bi bi-sliders text-success fs-5 me-2"></i>
        <h6 class="fw-bold mb-0 text-dark">Penentuan Parameter Analisis Data Mining</h6>
    </div>
    <div class="card-body p-4">
        <form action="<?= BASE_URL ?>index.php?page=apriori&action=proses" method="POST" class="row g-3">
            <div class="col-12 col-md-6 col-lg-3">
                <label for="start_date" class="form-label fw-semibold small">Periode Tanggal Awal <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="<?= htmlspecialchars($defaultStartDate) ?>" required>
                <div class="form-text">Batas awal riwayat transaksi.</div>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <label for="end_date" class="form-label fw-semibold small">Periode Tanggal Akhir <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="<?= htmlspecialchars($defaultEndDate) ?>" required>
                <div class="form-text">Batas akhir riwayat transaksi.</div>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <label for="min_support" class="form-label fw-semibold small">Minimum Support (%) <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="number" class="form-control" id="min_support" name="min_support" min="1" max="100" step="1" value="<?= $defaultMinSupport ?>" required>
                    <span class="input-group-text">%</span>
                </div>
                <div class="form-text">Ambang batas frekuensi kemunculan (default: 15%).</div>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <label for="min_confidence" class="form-label fw-semibold small">Minimum Confidence (%) <span class="text-danger">*</span></label>
                <div class="input-group">
                    <input type="number" class="form-control" id="min_confidence" name="min_confidence" min="1" max="100" step="1" value="<?= $defaultMinConfidence ?>" required>
                    <span class="input-group-text">%</span>
                </div>
                <div class="form-text">Ambang batas kepastian aturan asosiasi (default: 50%).</div>
            </div>

            <div class="col-12 mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                <div class="small text-muted">
                    <i class="bi bi-info-circle me-1 text-primary"></i> Algoritma akan mengekstraksi keranjang belanja kasir dan melakukan iterasi pembentukan C1, L1, C2, L2, C3, dst.
                </div>
                <button type="submit" class="btn btn-success fw-bold px-4 py-2 shadow-sm d-flex align-items-center gap-2">
                    <i class="bi bi-cpu fs-5"></i> Proses Analisis Apriori
                </button>
            </div>
        </form>
    </div>
</div>

<!-- History Card -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <div>
            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history me-2 text-primary"></i>Riwayat Hasil Analisis Apriori Tersimpan</h6>
        </div>
        <span class="badge bg-light text-muted border"><?= count($historyList) ?> Riwayat</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;" class="text-center">#</th>
                        <th>Waktu Analisis</th>
                        <th>Periode Transaksi</th>
                        <th class="text-center">Min. Support</th>
                        <th class="text-center">Min. Confidence</th>
                        <th class="text-center">Total Transaksi</th>
                        <th class="text-center">Strong Rules</th>
                        <th>Dianalisis Oleh</th>
                        <th style="width: 140px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($historyList)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-folder-x fs-1 d-block mb-2 text-secondary"></i>
                                Belum ada riwayat analisis yang disimpan. Silakan jalankan analisis di atas.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($historyList as $i => $h): ?>
                            <tr>
                                <td class="text-center text-muted"><?= $i + 1 ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= date('d/m/Y H:i', strtotime($h['tanggal_analisis'])) ?></div>
                                    <small class="text-muted">ID: #<?= $h['id_analisis'] ?></small>
                                </td>
                                <td class="small text-muted">
                                    <?= date('d/m/Y', strtotime($h['periode_transaksi_mulai'])) ?> s/d <?= date('d/m/Y', strtotime($h['periode_transaksi_akhir'])) ?>
                                </td>
                                <td class="text-center fw-semibold"><?= $h['min_support'] ?>%</td>
                                <td class="text-center fw-semibold"><?= $h['min_confidence'] ?>%</td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border"><?= $h['total_transaksi'] ?> Transaksi</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success"><?= $h['total_rules'] ?> Aturan</span>
                                </td>
                                <td class="small text-muted"><?= htmlspecialchars($h['nama_user'] ?? 'Sistem') ?></td>
                                <td class="text-center">
                                    <a href="<?= BASE_URL ?>index.php?page=apriori&action=detail&id=<?= $h['id_analisis'] ?>" class="btn btn-sm btn-outline-info me-1" title="Lihat Hasil Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?= BASE_URL ?>index.php?page=apriori&action=cetak&id=<?= $h['id_analisis'] ?>" target="_blank" class="btn btn-sm btn-outline-success me-1" title="Cetak Laporan">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                    <a href="<?= BASE_URL ?>index.php?page=apriori&action=delete&id=<?= $h['id_analisis'] ?>" class="btn btn-sm btn-outline-danger" title="Hapus Riwayat" onclick="return confirm('Apakah Anda yakin ingin menghapus arsip analisis ini?');">
                                        <i class="bi bi-trash"></i>
                                    </a>
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

