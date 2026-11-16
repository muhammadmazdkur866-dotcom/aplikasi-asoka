<?php
$pageTitle = 'Dashboard Utama';
require __DIR__ . '/../layouts/header.php';
?>

<!-- Header Banner -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold text-dark mb-1">Dashboard Ikhtisar</h3>
        <p class="text-muted small mb-0">Selamat datang di Panel Manajemen Point of Sales & Market Basket Analysis Asoka.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= BASE_URL ?>index.php?page=pos" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm">
            <i class="bi bi-cart-plus"></i> Kasir POS
        </a>
        <a href="<?= BASE_URL ?>index.php?page=apriori" class="btn btn-success d-flex align-items-center gap-2 shadow-sm">
            <i class="bi bi-diagram-3-fill"></i> Analisis Apriori
        </a>
    </div>
</div>

<!-- Stat Cards Row -->
<div class="row g-3 mb-4">
    <!-- Penjualan Hari Ini -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card stat-card p-3 h-100">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-secondary bg-opacity-10 text-secondary me-3">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium">Omset Hari Ini</div>
                    <h5 class="fw-bold mb-0 text-dark"><?= format_rupiah($todaySummary['total_omset']) ?></h5>
                    <small class="text-muted"><?= $todaySummary['total_transaksi'] ?> transaksi sukses</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Penjualan Bulan Ini -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card stat-card p-3 h-100">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                    <i class="bi bi-wallet2"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium">Omset Bulan Ini</div>
                    <h5 class="fw-bold mb-0 text-dark"><?= format_rupiah($monthSummary['total_omset']) ?></h5>
                    <small class="text-success fw-medium">+<?= format_rupiah($monthSummary['total_laba_kotor']) ?> laba kotor</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Produk & Aset -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card stat-card p-3 h-100">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-info bg-opacity-10 text-info me-3">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium">Total Produk Sembako</div>
                    <h5 class="fw-bold mb-0 text-dark"><?= $totalBarang ?> Produk</h5>
                    <small class="text-muted">Aset Stok: <?= format_rupiah($stockValues['total_modal']) ?></small>
                </div>
            </div>
        </div>
    </div>

    <!-- Peringatan Stok Menipis -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card stat-card p-3 h-100 border-start border-4 <?= $lowStockCount > 0 ? 'border-warning' : 'border-success' ?>">
            <div class="d-flex align-items-center">
                <div class="stat-icon <?= $lowStockCount > 0 ? 'bg-warning bg-opacity-10 text-warning' : 'bg-success bg-opacity-10 text-success' ?> me-3">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div>
                    <div class="text-muted small fw-medium">Peringatan Stok</div>
                    <h5 class="fw-bold mb-0 <?= $lowStockCount > 0 ? 'text-danger' : 'text-success' ?>"><?= $lowStockCount ?> Barang Menipis</h5>
                    <small class="text-muted"><?= $lowStockCount > 0 ? 'Perlu segera restok gudang' : 'Semua stok dalam batas aman' ?></small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Row: Chart & Apriori Highlight -->
<div class="row g-4 mb-4">
    <!-- Sales Chart -->
    <div class="col-12 col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="text-dark fw-bold"><i class="bi bi-graph-up me-2 text-primary"></i>Tren Omset Penjualan (7 Hari Terakhir)</span>
                <span class="badge bg-light text-muted border">Real-time Data</span>
            </div>
            <div class="card-body">
                <canvas id="salesChart" height="110"></canvas>
            </div>
        </div>
    </div>

    <!-- Apriori Quick Summary / Insight Highlight -->
    <div class="col-12 col-lg-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="text-dark fw-bold"><i class="bi bi-lightbulb me-2 text-warning"></i>Kecerdasan Apriori</span>
                <a href="<?= BASE_URL ?>index.php?page=apriori" class="btn btn-sm btn-outline-success">Buka Modul</a>
            </div>
            <div class="card-body">
                <?php if ($latestApriori): ?>
                    <div class="p-3 bg-light rounded-3 mb-3 border">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-success">Analisis Terakhir</span>
                            <small class="text-muted"><?= date('d/m/Y', strtotime($latestApriori['tanggal_analisis'])) ?></small>
                        </div>
                        <div class="small text-secondary mb-1">
                            <strong><?= $latestApriori['total_rules'] ?> Aturan Asosiasi</strong> ditemukan dari <?= $latestApriori['total_transaksi'] ?> riwayat transaksi.
                        </div>
                        <div class="small text-muted">
                            Min. Support: <strong><?= $latestApriori['min_support'] ?>%</strong> | Min. Confidence: <strong><?= $latestApriori['min_confidence'] ?>%</strong>
                        </div>
                    </div>

                    <h6 class="fw-bold small text-muted text-uppercase mb-2">Contoh Rekomendasi Terbentuk:</h6>
                    <div class="alert alert-success border-0 small mb-0 py-2">
                        <i class="bi bi-check2-circle me-1"></i>
                        Penempatan rak <strong>Beras Pandan Wangi</strong> dan <strong>Minyak Bimoli</strong> berdekatan berpotensi meningkatkan omset pembelian berulang hingga 90%!
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-diagram-3 text-muted display-4"></i>
                        <p class="text-muted small mt-2 mb-3">Belum ada hasil analisis Apriori yang tersimpan di sistem.</p>
                        <a href="<?= BASE_URL ?>index.php?page=apriori" class="btn btn-sm btn-success">
                            <i class="bi bi-play-fill me-1"></i> Mulai Analisis Apriori Sekarang
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Second Row: Low Stock Alert & Recent Transactions -->
<div class="row g-4">
    <!-- Low Stock Table -->
    <div class="col-12 col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="text-dark fw-bold"><i class="bi bi-bell me-2 text-danger"></i>Barang Hampir Habis (Low Stock Warning)</span>
                <a href="<?= BASE_URL ?>index.php?page=barang" class="btn btn-sm btn-outline-secondary">Kelola Stok</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Produk</th>
                                <th>Kategori</th>
                                <th class="text-center">Sisa Stok</th>
                                <th class="text-center">Min. Stok</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($lowStockList)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-3 text-muted">
                                        <i class="bi bi-check-circle text-success me-1"></i> Semua stok barang dalam kondisi aman.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach (array_slice($lowStockList, 0, 5) as $b): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold text-dark"><?= htmlspecialchars($b['nama_barang']) ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($b['id_barang']) ?></small>
                                        </td>
                                        <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($b['nama_kategori']) ?></span></td>
                                        <td class="text-center fw-bold text-danger"><?= $b['stok'] ?> <?= htmlspecialchars($b['satuan']) ?></td>
                                        <td class="text-center text-muted"><?= $b['stok_minimum'] ?> <?= htmlspecialchars($b['satuan']) ?></td>
                                        <td>
                                            <?php if ($b['stok'] <= 0): ?>
                                                <span class="badge bg-danger">Habis</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">Menipis</span>
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
    </div>

    <!-- Recent Transactions -->
    <div class="col-12 col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="text-dark fw-bold"><i class="bi bi-clock-history me-2 text-info"></i>5 Transaksi Kasir Terbaru</span>
                <a href="<?= BASE_URL ?>index.php?page=transaksi" class="btn btn-sm btn-outline-secondary">Semua Transaksi</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No. Invoice</th>
                                <th>Waktu</th>
                                <th>Kasir</th>
                                <th class="text-end">Total Belanja</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentTransactions)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-3 text-muted">Belum ada data transaksi.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentTransactions as $tr): ?>
                                    <tr>
                                        <td>
                                            <a href="<?= BASE_URL ?>index.php?page=transaksi&action=detail&id=<?= urlencode($tr['id_transaksi']) ?>" class="fw-semibold text-primary text-decoration-none">
                                                <?= htmlspecialchars($tr['id_transaksi']) ?>
                                            </a>
                                        </td>
                                        <td class="small text-muted"><?= date('d/m/Y H:i', strtotime($tr['tanggal_waktu'])) ?></td>
                                        <td class="small"><?= htmlspecialchars($tr['nama_kasir']) ?></td>
                                        <td class="text-end fw-bold text-success"><?= format_rupiah($tr['total_bayar']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart initialization script -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const chartData = <?= json_encode($chartData) ?>;
        const labels = chartData.map(d => d.tanggal);
        const omsetValues = chartData.map(d => d.total_omset);
        const countValues = chartData.map(d => d.jml_transaksi);

        const ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Omset Penjualan (Rp)',
                        data: omsetValues,
                        borderColor: '#475569',
                        backgroundColor: 'rgba(71, 85, 105, 0.12)',
                        fill: true,
                        tension: 0.3,
                        pointBackgroundColor: '#334155',
                        pointRadius: 4,
                        yAxisID: 'y'
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Omset: Rp ' + context.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + (value / 1000).toLocaleString('id-ID') + 'k';
                            }
                        }
                    }
                }
            }
        });
    });
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

