<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Analisis Apriori - <?= STORE_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            background: #fff;
            padding: 20px;
        }
        .header-kop {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header-kop h2 {
            margin: 0;
            font-size: 22px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .header-kop p {
            margin: 2px 0;
            font-size: 13px;
        }
        .table-laporan {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table-laporan th, .table-laporan td {
            border: 1px solid #000;
            padding: 6px 8px;
            font-size: 12px;
        }
        .table-laporan th {
            background-color: #f2f2f2 !important;
            text-align: center;
        }
        .meta-box {
            margin-bottom: 15px;
            font-size: 13px;
        }
        .insight-box {
            border: 1px solid #333;
            padding: 10px;
            margin-bottom: 12px;
            font-size: 12px;
            page-break-inside: avoid;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>

<div class="no-print text-center mb-4">
    <button class="btn btn-success fw-bold px-4" onclick="window.print()">🖨️ Cetak Dokumen / Simpan PDF</button>
    <button class="btn btn-secondary px-4 ms-2" onclick="window.close()">Tutup Jendela</button>
</div>

<div class="header-kop">
    <h2><?= STORE_NAME ?></h2>
    <p><?= STORE_ADDRESS ?> | Telepon: <?= STORE_PHONE ?></p>
    <p><strong>LAPORAN ANALISIS MARKET BASKET (ALGORITMA APRIORI)</strong></p>
</div>

<div class="meta-box">
    <table style="width: 100%; font-size: 13px;">
        <tr>
            <td style="width: 25%;"><strong>Periode Transaksi</strong></td>
            <td style="width: 2%;">:</td>
            <td><?= date('d/m/Y', strtotime($result['periode_awal'])) ?> s/d <?= date('d/m/Y', strtotime($result['periode_akhir'])) ?></td>
            <td style="width: 25%;"><strong>Tanggal Analisis</strong></td>
            <td style="width: 2%;">:</td>
            <td><?= date('d/m/Y H:i', strtotime($result['tanggal_analisis'] ?? date('Y-m-d H:i:s'))) ?> WIB</td>
        </tr>
        <tr>
            <td><strong>Total Transaksi (N)</strong></td>
            <td>:</td>
            <td><?= $result['total_transactions'] ?> Transaksi</td>
            <td><strong>Dianalisis Oleh</strong></td>
            <td>:</td>
            <td><?= htmlspecialchars($result['analyst_name'] ?? 'Administrator') ?></td>
        </tr>
        <tr>
            <td><strong>Minimum Support</strong></td>
            <td>:</td>
            <td><?= $result['min_support_percent'] ?>% (&ge; <?= $result['min_support_count'] ?> kemunculan)</td>
            <td><strong>Minimum Confidence</strong></td>
            <td>:</td>
            <td><?= $result['min_confidence_percent'] ?>%</td>
        </tr>
        <tr>
            <td><strong>Total Rules Terbentuk</strong></td>
            <td>:</td>
            <td><strong><?= $result['total_rules'] ?> Aturan Asosiasi</strong></td>
            <td><strong>Waktu Komputasi</strong></td>
            <td>:</td>
            <td><?= $result['execution_time_ms'] ?> milidetik</td>
        </tr>
    </table>
</div>

<h5 style="font-size: 14px; font-weight: bold; margin-top: 20px; margin-bottom: 8px;">A. TABEL ATURAN ASOSIASI (STRONG RULES)</h5>
<table class="table-laporan">
    <thead>
        <tr>
            <th style="width: 40px;">No</th>
            <th>Antecedent (Jika Membeli)</th>
            <th style="width: 30px;">&rarr;</th>
            <th>Consequent (Maka Membeli)</th>
            <th style="width: 80px;">Support (%)</th>
            <th style="width: 80px;">Confidence (%)</th>
            <th style="width: 70px;">Lift Ratio</th>
            <th style="width: 80px;">Korelasi</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($result['association_rules'])): ?>
            <tr>
                <td colspan="8" class="text-center">Tidak ada aturan asosiasi yang memenuhi syarat ambang batas.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($result['association_rules'] as $i => $r): ?>
                <tr>
                    <td style="text-align: center;"><?= $i + 1 ?></td>
                    <td><strong><?= htmlspecialchars($r['antecedent_str']) ?></strong></td>
                    <td style="text-align: center;">&rarr;</td>
                    <td><strong><?= htmlspecialchars($r['consequent_str']) ?></strong></td>
                    <td style="text-align: center;"><?= $r['itemset_support'] ?>%</td>
                    <td style="text-align: center;"><strong><?= $r['confidence'] ?>%</strong></td>
                    <td style="text-align: center; font-family: monospace;"><?= number_format($r['lift_ratio'], 2) ?></td>
                    <td style="text-align: center;"><?= $r['correlation'] ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<h5 style="font-size: 14px; font-weight: bold; margin-top: 25px; margin-bottom: 8px;">B. REKOMENDASI STRATEGIS & KEPUTUSAN BISNIS</h5>
<p style="font-size: 12px; margin-bottom: 10px;">Berdasarkan pola aturan asosiasi yang ditemukan, berikut arahan strategi praktis untuk toko sembako:</p>

<?php if (!empty($result['business_insights'])): ?>
    <?php foreach (array_slice($result['business_insights'], 0, 5) as $idx => $ins): ?>
        <div class="insight-box">
            <div style="font-weight: bold; margin-bottom: 4px;">
                Rekomendasi #<?= $idx + 1 ?>: <?= htmlspecialchars($ins['antecedent']) ?> &rarr; <?= htmlspecialchars($ins['consequent']) ?>
                (Confidence: <?= $ins['confidence'] ?>%, Lift: <?= number_format($ins['lift_ratio'], 2) ?>)
            </div>
            <div>&bull; <strong>Penataan Letak Rak:</strong> <?= strip_tags($ins['strategi_penempatan']) ?></div>
            <div>&bull; <strong>Promo Bundling:</strong> <?= strip_tags($ins['promo_bundling']) ?></div>
            <div>&bull; <strong>Antisipasi Persediaan:</strong> <?= strip_tags($ins['manajemen_stok']) ?></div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<div style="margin-top: 40px; float: right; width: 220px; text-align: center; font-size: 13px;">
    <div>Jakarta, <?= date('d F Y') ?></div>
    <div style="margin-top: 5px;">Penanggung Jawab / Analis,</div>
    <div style="height: 60px;"></div>
    <div><strong>( <?= htmlspecialchars($result['analyst_name'] ?? 'Administrator') ?> )</strong></div>
    <div><?= STORE_NAME ?></div>
</div>

</body>
</html>

