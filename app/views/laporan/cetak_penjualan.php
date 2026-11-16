<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan - <?= STORE_NAME ?></title>
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
    <p><strong>LAPORAN REKAPITULASI PENJUALAN & LABA KOTOR</strong></p>
    <p style="font-size: 12px;">Periode: <?= date('d/m/Y', strtotime($startDate)) ?> s/d <?= date('d/m/Y', strtotime($endDate)) ?></p>
</div>

<table class="table-laporan">
    <thead>
        <tr>
            <th style="width: 30px;">No</th>
            <th>Invoice</th>
            <th>Tanggal</th>
            <th>Kasir</th>
            <th>Nama Produk</th>
            <th style="width: 40px;">Qty</th>
            <th>Harga Jual</th>
            <th>Subtotal</th>
            <th>Modal</th>
            <th>Laba</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($laporanList)): ?>
            <tr>
                <td colspan="10" class="text-center">Tidak ada data transaksi pada rentang periode ini.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($laporanList as $i => $row): ?>
                <tr>
                    <td style="text-align: center;"><?= $i + 1 ?></td>
                    <td><?= htmlspecialchars($row['id_transaksi']) ?></td>
                    <td style="text-align: center;"><?= date('d/m/Y H:i', strtotime($row['tanggal_waktu'])) ?></td>
                    <td><?= htmlspecialchars($row['nama_kasir']) ?></td>
                    <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                    <td style="text-align: center;"><?= $row['kuantitas'] ?></td>
                    <td style="text-align: right;"><?= number_format($row['harga_satuan'], 0, ',', '.') ?></td>
                    <td style="text-align: right; font-weight: bold;"><?= number_format($row['subtotal'], 0, ',', '.') ?></td>
                    <td style="text-align: right;"><?= number_format($row['total_modal'], 0, ',', '.') ?></td>
                    <td style="text-align: right; font-weight: bold;"><?= number_format($row['laba'], 0, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
    <tfoot>
        <tr style="background-color: #f2f2f2; font-weight: bold;">
            <td colspan="5" style="text-align: right;">TOTAL:</td>
            <td style="text-align: center;"><?= $totalQty ?></td>
            <td></td>
            <td style="text-align: right;">Rp <?= number_format($totalOmset, 0, ',', '.') ?></td>
            <td style="text-align: right;">Rp <?= number_format($totalModal, 0, ',', '.') ?></td>
            <td style="text-align: right;">Rp <?= number_format($totalLaba, 0, ',', '.') ?></td>
        </tr>
    </tfoot>
</table>

<div style="margin-top: 40px; float: right; width: 220px; text-align: center; font-size: 13px;">
    <div>Jakarta, <?= date('d F Y') ?></div>
    <div style="margin-top: 5px;">Pemilik Toko / Penanggung Jawab,</div>
    <div style="height: 60px;"></div>
    <div><strong>( Administrator )</strong></div>
    <div><?= STORE_NAME ?></div>
</div>

</body>
</html>

