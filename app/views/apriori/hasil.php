<?php
$pageTitle = 'Hasil Analisis Apriori';
require __DIR__ . '/../layouts/header.php';
?>

<!-- Header Info & Actions -->
<div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-success px-2 py-1"><i class="bi bi-check-circle me-1"></i> Selesai Diproses</span>
            <h4 class="fw-bold text-dark mb-0">Hasil Analisis Market Basket (Apriori)</h4>
        </div>
        <p class="text-muted small mb-0">
            Periode Transaksi: <strong><?= date('d M Y', strtotime($result['periode_awal'])) ?></strong> s/d <strong><?= date('d M Y', strtotime($result['periode_akhir'])) ?></strong>
            &bull; Waktu Eksekusi: <span class="badge bg-light text-dark border"><?= $result['execution_time_ms'] ?> ms</span>
        </p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="<?= BASE_URL ?>index.php?page=apriori" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Analisis Baru
        </a>
        <a href="<?= BASE_URL ?>index.php?page=apriori&action=cetak<?= isset($savedId) ? '&id=' . $savedId : '' ?>" target="_blank" class="btn btn-outline-success btn-sm">
            <i class="bi bi-printer me-1"></i> Cetak Laporan
        </a>
        <?php if (empty($isSaved)): ?>
            <a href="<?= BASE_URL ?>index.php?page=apriori&action=simpan" class="btn btn-success btn-sm shadow-sm">
                <i class="bi bi-save me-1"></i> Simpan Hasil ke Database
            </a>
        <?php else: ?>
            <span class="badge bg-soft-info text-primary border d-flex align-items-center px-3">
                <i class="bi bi-database-check me-1"></i> Tersimpan (ID #<?= $savedId ?>)
            </span>
        <?php endif; ?>
    </div>
</div>

<!-- Metrics Cards Row -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card p-3 text-center border-0 shadow-sm h-100">
            <small class="text-muted fw-semibold">Total Transaksi</small>
            <h4 class="fw-bold text-dark mt-1 mb-0"><?= $result['total_transactions'] ?></h4>
            <span class="text-muted" style="font-size: 0.7rem;">keranjang belanja</span>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card p-3 text-center border-0 shadow-sm h-100">
            <small class="text-muted fw-semibold">Min. Support</small>
            <h4 class="fw-bold text-primary mt-1 mb-0"><?= $result['min_support_percent'] ?>%</h4>
            <span class="text-muted" style="font-size: 0.7rem;">&ge; <?= $result['min_support_count'] ?> transaksi</span>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card p-3 text-center border-0 shadow-sm h-100">
            <small class="text-muted fw-semibold">Min. Confidence</small>
            <h4 class="fw-bold text-success mt-1 mb-0"><?= $result['min_confidence_percent'] ?>%</h4>
            <span class="text-muted" style="font-size: 0.7rem;">ambang kepastian</span>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card p-3 text-center border-0 shadow-sm h-100">
            <small class="text-muted fw-semibold">Frequent Itemsets</small>
            <h4 class="fw-bold text-info mt-1 mb-0"><?= $result['total_frequent_itemsets'] ?></h4>
            <span class="text-muted" style="font-size: 0.7rem;">kombinasi lolos</span>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card p-3 text-center border-0 shadow-sm h-100">
            <small class="text-muted fw-semibold">Strong Rules</small>
            <h4 class="fw-bold text-warning mt-1 mb-0"><?= $result['total_rules'] ?></h4>
            <span class="text-muted" style="font-size: 0.7rem;">aturan terbentuk</span>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card p-3 text-center border-0 shadow-sm h-100">
            <small class="text-muted fw-semibold">Kecepatan Hitung</small>
            <h4 class="fw-bold text-dark mt-1 mb-0"><?= $result['execution_time_ms'] ?> ms</h4>
            <span class="text-muted" style="font-size: 0.7rem;">sangat responsif</span>
        </div>
    </div>
</div>

<!-- Tabs Navigation -->
<ul class="nav nav-pills nav-fill bg-white p-2 rounded-3 shadow-sm mb-4" id="aprioriTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active fw-semibold" id="rules-tab" data-bs-toggle="tab" data-bs-target="#tabRules" type="button" role="tab" aria-controls="tabRules" aria-selected="true">
            <i class="bi bi-trophy me-1 text-warning"></i> 1. Aturan Asosiasi Final (<?= $result['total_rules'] ?> Rules)
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-semibold" id="insights-tab" data-bs-toggle="tab" data-bs-target="#tabInsights" type="button" role="tab" aria-controls="tabInsights" aria-selected="false">
            <i class="bi bi-lightbulb me-1 text-success"></i> 2. Rekomendasi Bisnis Cerdas
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-semibold" id="steps-tab" data-bs-toggle="tab" data-bs-target="#tabSteps" type="button" role="tab" aria-controls="tabSteps" aria-selected="false">
            <i class="bi bi-calculator me-1 text-primary"></i> 3. Langkah Perhitungan Apriori (Step-by-Step)
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-semibold" id="dataset-tab" data-bs-toggle="tab" data-bs-target="#tabDataset" type="button" role="tab" aria-controls="tabDataset" aria-selected="false">
            <i class="bi bi-file-earmark-spreadsheet me-1 text-secondary"></i> 4. Dataset Transaksi (<?= $result['total_transactions'] ?>)
        </button>
    </li>
</ul>

<!-- Tab Contents -->
<div class="tab-content" id="aprioriTabContent">
    
    <!-- ========================================== -->
    <!-- TAB 1: STRONG ASSOCIATION RULES -->
    <!-- ========================================== -->
    <div class="tab-pane fade show active" id="tabRules" role="tabpanel" aria-labelledby="rules-tab">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold mb-0 text-dark">Tabel Aturan Asosiasi Kuat (Strong Rules)</h6>
                    <small class="text-muted">Aturan yang memenuhi ambang batas Minimum Support &ge; <?= $result['min_support_percent'] ?>% dan Minimum Confidence &ge; <?= $result['min_confidence_percent'] ?>%</small>
                </div>
                <span class="badge bg-success"><?= $result['total_rules'] ?> Aturan Ditemukan</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;" class="text-center">#</th>
                                <th>Antecedent (Jika Pelanggan Membeli...)</th>
                                <th class="text-center" style="width: 40px;"></th>
                                <th>Consequent (Maka Ia Juga Membeli...)</th>
                                <th class="text-center">Support (A &cup; B)</th>
                                <th class="text-center">Confidence</th>
                                <th class="text-center">Lift Ratio</th>
                                <th class="text-center">Korelasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($result['association_rules'])): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="bi bi-exclamation-circle fs-2 d-block mb-2 text-warning"></i>
                                        Tidak ada aturan asosiasi yang memenuhi ambang batas Minimum Support (<?= $result['min_support_percent'] ?>%) dan Minimum Confidence (<?= $result['min_confidence_percent'] ?>%).<br>
                                        <small>Cobalah untuk menurunkan persentase Minimum Support atau Minimum Confidence.</small>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($result['association_rules'] as $i => $rule): ?>
                                    <tr>
                                        <td class="text-center text-muted fw-semibold"><?= $i + 1 ?></td>
                                        <td>
                                            <span class="badge bg-light text-dark border p-2 fw-semibold">
                                                <?= htmlspecialchars($rule['antecedent_str']) ?>
                                            </span>
                                            <div class="text-muted" style="font-size: 0.7rem;">Supp: <?= $rule['antecedent_support'] ?>%</div>
                                        </td>
                                        <td class="text-center text-success fw-bold fs-5">
                                            &rarr;
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-success border border-success p-2 fw-semibold">
                                                <?= htmlspecialchars($rule['consequent_str']) ?>
                                            </span>
                                            <div class="text-muted" style="font-size: 0.7rem;">Supp: <?= $rule['consequent_support'] ?>%</div>
                                        </td>
                                        <td class="text-center fw-bold">
                                            <?= $rule['itemset_support'] ?>%
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-success fs-6 px-2 py-1">
                                                <?= $rule['confidence'] ?>%
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge <?= $rule['lift_ratio'] > 1.0 ? 'bg-primary' : 'bg-secondary' ?> fs-6 px-2 py-1 font-monospace">
                                                <?= number_format($rule['lift_ratio'], 2) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($rule['lift_ratio'] > 1.0): ?>
                                                <span class="badge bg-soft-success text-success border border-success">
                                                    <i class="bi bi-check-circle me-1"></i> Positif (Valid)
                                                </span>
                                            <?php elseif ($rule['lift_ratio'] == 1.0): ?>
                                                <span class="badge bg-secondary">Independen</span>
                                            <?php else: ?>
                                                <span class="badge bg-soft-danger text-danger border border-danger">Negatif</span>
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

    <!-- ========================================== -->
    <!-- TAB 2: REKOMENDASI BISNIS STRATEGIS -->
    <!-- ========================================== -->
    <div class="tab-pane fade" id="tabInsights" role="tabpanel" aria-labelledby="insights-tab">
        <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-4">
            <i class="bi bi-info-circle-fill fs-4 me-3"></i>
            <div>
                <strong>Penerapan Nyata Market Basket Analysis untuk Toko Sembako:</strong>
                <p class="mb-0 small">Berdasarkan pola transaksi yang ditemukan, sistem secara otomatis merumuskan 3 strategi bisnis: tata letak penempatan barang, bundling promo potongan harga, dan pengelolaan persediaan stok antisipatif.</p>
            </div>
        </div>

        <?php if (empty($result['business_insights'])): ?>
            <div class="card p-5 text-center text-muted">
                Belum ada rekomendasi bisnis karena tidak ada aturan asosiasi yang terbentuk pada ambang batas parameter ini.
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($result['business_insights'] as $idx => $ins): ?>
                    <div class="col-12 col-lg-6">
                        <div class="card h-100 shadow-sm border-0 border-top border-4 border-success">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <span class="badge bg-light text-dark border mb-1">Rekomendasi #<?= $idx + 1 ?></span>
                                        <h5 class="fw-bold text-dark mb-0">
                                            <?= htmlspecialchars($ins['antecedent']) ?> &rarr; <?= htmlspecialchars($ins['consequent']) ?>
                                        </h5>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-success mb-1">Conf: <?= $ins['confidence'] ?>%</span>
                                        <div class="small text-muted font-monospace">Lift: <?= number_format($ins['lift_ratio'], 2) ?></div>
                                    </div>
                                </div>

                                <p class="text-secondary small mb-3 bg-light p-2 rounded border">
                                    <?= $ins['narasi'] ?>
                                </p>

                                <div class="mb-3">
                                    <div class="fw-bold small text-success mb-1 d-flex align-items-center gap-1">
                                        <i class="bi bi-geo-alt-fill"></i> 1. Strategi Penempatan Rak (Product Layout):
                                    </div>
                                    <div class="small text-dark ps-3 border-start border-2 border-success">
                                        <?= $ins['strategi_penempatan'] ?>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="fw-bold small text-warning mb-1 d-flex align-items-center gap-1">
                                        <i class="bi bi-gift-fill"></i> 2. Paket Promo Bundling Diskon:
                                    </div>
                                    <div class="small text-dark ps-3 border-start border-2 border-warning">
                                        <?= $ins['promo_bundling'] ?>
                                    </div>
                                </div>

                                <div class="mb-0">
                                    <div class="fw-bold small text-info mb-1 d-flex align-items-center gap-1">
                                        <i class="bi bi-box-seam-fill"></i> 3. Manajemen Stok Antisipatif (Inventory Planning):
                                    </div>
                                    <div class="small text-dark ps-3 border-start border-2 border-info">
                                        <?= $ins['manajemen_stok'] ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- ========================================== -->
    <!-- TAB 3: LANGKAH PERHITUNGAN APRIORI -->
    <!-- ========================================== -->
    <div class="tab-pane fade" id="tabSteps" role="tabpanel" aria-labelledby="steps-tab">
        <!-- Accordion Steps -->
        <div class="accordion" id="accordionAprioriSteps">
            
            <!-- Step 1: 1-Itemset (C1 & L1) -->
            <div class="accordion-item shadow-sm border-0 mb-3 rounded overflow-hidden">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        <span class="apriori-step-badge">1</span>
                        <span>Iterasi 1: Pembentukan Candidate 1-Itemset (C1) & Frequent 1-Itemset (L1)</span>
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionAprioriSteps">
                    <div class="accordion-body p-0">
                        <div class="p-3 bg-light border-bottom small text-muted">
                            Rumus: $\text{Support}(A) = \frac{\text{Jumlah Transaksi Mengandung } A}{\text{Total Transaksi }(N)} \times 100\%$. 
                            Item lolos jika Support &ge; <?= $result['min_support_percent'] ?>%.
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;" class="text-center">#</th>
                                        <th>Nama Barang (1-Itemset)</th>
                                        <th class="text-center">Jumlah Kemunculan</th>
                                        <th class="text-center">Support (%)</th>
                                        <th class="text-center">Status Ambang Batas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($result['candidate_itemsets'][1])): ?>
                                        <?php foreach ($result['candidate_itemsets'][1] as $i => $item): ?>
                                            <tr>
                                                <td class="text-center text-muted"><?= $i + 1 ?></td>
                                                <td class="fw-semibold text-dark"><?= htmlspecialchars($item['items_str']) ?></td>
                                                <td class="text-center"><?= $item['count'] ?></td>
                                                <td class="text-center fw-bold"><?= $item['support'] ?>%</td>
                                                <td class="text-center">
                                                    <?php if ($item['passed']): ?>
                                                        <span class="badge bg-success"><i class="bi bi-check-lg me-1"></i> Lolos ke L1</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-light text-muted border">Tidak Lolos</span>
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

            <!-- Step 2: 2-Itemset (C2 & L2) -->
            <div class="accordion-item shadow-sm border-0 mb-3 rounded overflow-hidden">
                <h2 class="accordion-header" id="headingTwo">
                    <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        <span class="apriori-step-badge bg-primary">2</span>
                        <span>Iterasi 2: Pembentukan Candidate 2-Itemset (C2) & Frequent 2-Itemset (L2)</span>
                    </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionAprioriSteps">
                    <div class="accordion-body p-0">
                        <div class="p-3 bg-light border-bottom small text-muted">
                            Kombinasi pasangan barang dari item yang lolos di $L_1$. Rumus: $\text{Support}(A, B) = \frac{\text{Kemunculan } (A \cap B)}{N} \times 100\%$.
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;" class="text-center">#</th>
                                        <th>Kombinasi Pasangan Barang (2-Itemset)</th>
                                        <th class="text-center">Jumlah Kemunculan Bersama</th>
                                        <th class="text-center">Support (%)</th>
                                        <th class="text-center">Status Ambang Batas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($result['candidate_itemsets'][2])): ?>
                                        <?php foreach ($result['candidate_itemsets'][2] as $i => $item): ?>
                                            <tr>
                                                <td class="text-center text-muted"><?= $i + 1 ?></td>
                                                <td class="fw-semibold text-dark">
                                                    <span class="badge bg-light text-dark border p-2"><?= htmlspecialchars($item['items_str']) ?></span>
                                                </td>
                                                <td class="text-center"><?= $item['count'] ?></td>
                                                <td class="text-center fw-bold"><?= $item['support'] ?>%</td>
                                                <td class="text-center">
                                                    <?php if ($item['passed']): ?>
                                                        <span class="badge bg-success"><i class="bi bi-check-lg me-1"></i> Lolos ke L2</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-light text-muted border">Tidak Lolos</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">Tidak ada kombinasi 2-itemset yang terbentuk.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 3: 3-Itemset (C3 & L3) -->
            <?php if (!empty($result['candidate_itemsets'][3])): ?>
                <div class="accordion-item shadow-sm border-0 mb-3 rounded overflow-hidden">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            <span class="apriori-step-badge bg-info">3</span>
                            <span>Iterasi 3: Pembentukan Candidate 3-Itemset (C3) & Frequent 3-Itemset (L3)</span>
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionAprioriSteps">
                        <div class="accordion-body p-0">
                            <div class="p-3 bg-light border-bottom small text-muted">
                                Pembentukan kombinasi 3 barang dengan pemangkasan Apriori (setiap subset 2 barang harus lolos di $L_2$).
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 50px;" class="text-center">#</th>
                                            <th>Kombinasi 3 Barang (3-Itemset)</th>
                                            <th class="text-center">Jumlah Kemunculan Bersama</th>
                                            <th class="text-center">Support (%)</th>
                                            <th class="text-center">Status Ambang Batas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($result['candidate_itemsets'][3] as $i => $item): ?>
                                            <tr>
                                                <td class="text-center text-muted"><?= $i + 1 ?></td>
                                                <td class="fw-semibold text-dark">
                                                    <span class="badge bg-light text-dark border p-2"><?= htmlspecialchars($item['items_str']) ?></span>
                                                </td>
                                                <td class="text-center"><?= $item['count'] ?></td>
                                                <td class="text-center fw-bold"><?= $item['support'] ?>%</td>
                                                <td class="text-center">
                                                    <?php if ($item['passed']): ?>
                                                        <span class="badge bg-success"><i class="bi bi-check-lg me-1"></i> Lolos ke L3</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-light text-muted border">Tidak Lolos</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Step 4: Kalkulasi Confidence & Lift Ratio Formula -->
            <div class="accordion-item shadow-sm border-0 rounded overflow-hidden">
                <h2 class="accordion-header" id="headingFour">
                    <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                        <span class="apriori-step-badge bg-warning">4</span>
                        <span>Rumus & Penjelasan Metrik Asosiasi (Confidence & Lift Ratio)</span>
                    </button>
                </h2>
                <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#accordionAprioriSteps">
                    <div class="accordion-body p-4">
                        <div class="row g-4">
                            <div class="col-12 col-md-4">
                                <div class="p-3 bg-light rounded-3 border h-100">
                                    <h6 class="fw-bold text-success"><i class="bi bi-percent me-1"></i> Support $(A \cup B)$</h6>
                                    <p class="small text-muted mb-2">Mengukur seberapa sering kedua item atau kelompok item dibeli bersamaan dari seluruh total transaksi.</p>
                                    <code>Support(A U B) = (Count(A U B) / N) * 100%</code>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="p-3 bg-light rounded-3 border h-100">
                                    <h6 class="fw-bold text-primary"><i class="bi bi-bullseye me-1"></i> Confidence $(A \implies B)$</h6>
                                    <p class="small text-muted mb-2">Mengukur kepastian seberapa sering item B dibeli oleh konsumen saat item A sudah dibeli.</p>
                                    <code>Confidence(A => B) = (Count(A U B) / Count(A)) * 100%</code>
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="p-3 bg-light rounded-3 border h-100">
                                    <h6 class="fw-bold text-warning"><i class="bi bi-speedometer2 me-1"></i> Lift Ratio $(A \implies B)$</h6>
                                    <p class="small text-muted mb-2">Mengukur kekuatan asosiasi dibanding kejadian acak. Nilai &gt; 1 mengindikasikan adanya aturan asosiasi yang sangat valid dan kuat.</p>
                                    <code>Lift Ratio = Confidence(A => B) / Support(B)</code>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- ========================================== -->
    <!-- TAB 4: DATASET TRANSAKSI SUMBER -->
    <!-- ========================================== -->
    <div class="tab-pane fade" id="tabDataset" role="tabpanel" aria-labelledby="dataset-tab">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold mb-0 text-dark">Data Transaksional Kasir yang Diproses</h6>
                    <small class="text-muted">Setiap transaksi telah melalui proses deduplikasi barang unik per nota.</small>
                </div>
                <span class="badge bg-light text-muted border"><?= count($result['transactions_dataset']) ?> Transaksi</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th style="width: 60px;" class="text-center">#</th>
                                <th>No. Invoice</th>
                                <th>Tanggal & Waktu</th>
                                <th>Kumpulan Barang Belanja (Item Basket)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($result['transactions_dataset'] as $i => $tr): ?>
                                <tr>
                                    <td class="text-center text-muted"><?= $i + 1 ?></td>
                                    <td><span class="fw-semibold font-monospace text-primary"><?= htmlspecialchars($tr['id_transaksi']) ?></span></td>
                                    <td class="small text-muted"><?= date('d/m/Y H:i', strtotime($tr['tanggal_waktu'])) ?></td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            <?php foreach ($tr['items'] as $it): ?>
                                                <span class="badge bg-light text-dark border"><?= htmlspecialchars($it) ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

