<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Stok Persediaan - <?= STORE_NAME ?></title>
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
            padding-bottom: 10px;
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
            padding: 5px 7px;
            font-size: 11px;
        }
        .table-laporan th {
            background-color: #f2f2f2 !important;
            text-align: center;
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
    <p><strong>LAPORAN MONITORING STOK & VALUASI ASET BARANG</strong></p>
    <p style="font-size: 12px;">Dicetak per tanggal: <?= date('d F Y, H:i') ?> WIB</p>
</div>

<table class="table-laporan">
    <thead>
        <tr>
            <th style="width: 30px;">No</th>
            <th>Kode</th>
            <th>Nama Produk Sembako</th>
            <th>Kategori</th>
            <th>Harga Beli</th>
            <th>Harga Jual</th>
            <th style="width: 45px;">Stok</th>
            <th style="width: 45px;">Min</th>
            <th>Total Aset (Modal)</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $grandStok = 0;
        $grandAset = 0;
        ?>
        <?php if (empty($barangList)): ?>
            <tr>
                <td colspan="10" class="text-center">Tidak ada data stok barang.</td>
            </tr>
        <?php else: ?>
            <?php foreach (array_values($barangList) as $i => $b): ?>
                <?php 
                $subAset = $b['stok'] * $b['harga_beli'];
                $grandStok += $b['stok'];
                $grandAset += $subAset;
                ?>
                <tr>
                    <td style="text-align: center;"><?= $i + 1 ?></td>
                    <td style="font-family: monospace;"><?= htmlspecialchars($b['id_barang']) ?></td>
                    <td><?= htmlspecialchars($b['nama_barang']) ?></td>
                    <td><?= htmlspecialchars($b['nama_kategori']) ?></td>
                    <td style="text-align: right;"><?= number_format($b['harga_beli'], 0, ',', '.') ?></td>
                    <td style="text-align: right;"><?= number_format($b['harga_jual'], 0, ',', '.') ?></td>
                    <td style="text-align: center; font-weight: bold;"><?= $b['stok'] ?></td>
                    <td style="text-align: center;"><?= $b['stok_minimum'] ?></td>
                    <td style="text-align: right;"><?= number_format($subAset, 0, ',', '.') ?></td>
                    <td style="text-align: center;">
                        <?= $b['stok'] <= 0 ? 'Habis' : ($b['stok'] <= $b['stok_minimum'] ? 'Menipis' : 'Aman') ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
    <tfoot>
        <tr style="background-color: #f2f2f2; font-weight: bold;">
            <td colspan="6" style="text-align: right;">TOTAL PERSIMPULAN:</td>
            <td style="text-align: center;"><?= $grandStok ?></td>
            <td></td>
            <td style="text-align: right;">Rp <?= number_format($grandAset, 0, ',', '.') ?></td>
            <td></td>
        </tr>
    </tfoot>
</table>

<div style="margin-top: 40px; float: right; width: 220px; text-align: center; font-size: 13px;">
    <div>Jakarta, <?= date('d F Y') ?></div>
    <div style="margin-top: 5px;">Kepala Bagian Gudang / Kasir,</div>
    <div style="height: 60px;"></div>
    <div><strong>( Administrator )</strong></div>
    <div><?= STORE_NAME ?></div>
</div>

</body>
</html>

