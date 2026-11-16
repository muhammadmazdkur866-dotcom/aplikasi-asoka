<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran - <?= htmlspecialchars($transaksi['id_transaksi']) ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/receipt.css">
    <style>
        .actions-bar {
            text-align: center;
            margin: 15px auto;
            max-width: 320px;
        }
        .btn-act {
            display: inline-block;
            padding: 8px 16px;
            font-size: 13px;
            font-family: sans-serif;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
            margin: 0 4px;
        }
        .btn-print {
            background: #475569;
            color: #fff;
            border: none;
        }
        .btn-close-win {
            background: #64748b;
            color: #fff;
            border: none;
        }
    </style>
</head>
<body>

<div class="actions-bar no-print">
    <button class="btn-act btn-print" onclick="window.print()">🖨️ Cetak Struk</button>
    <button class="btn-act btn-close-win" onclick="window.close()">Tutup Jendela</button>
</div>

<div class="receipt-wrapper">
    <div class="receipt-header">
        <h4><?= STORE_NAME ?></h4>
        <p><?= STORE_ADDRESS ?></p>
        <p>Telp: <?= STORE_PHONE ?></p>
    </div>

    <div class="receipt-meta">
        <div>
            <span>No. Struk:</span>
            <span><strong><?= htmlspecialchars($transaksi['id_transaksi']) ?></strong></span>
        </div>
        <div>
            <span>Tanggal:</span>
            <span><?= date('d/m/Y H:i', strtotime($transaksi['tanggal_waktu'])) ?></span>
        </div>
        <div>
            <span>Kasir:</span>
            <span><?= htmlspecialchars($transaksi['nama_kasir']) ?></span>
        </div>
        <?php if (!empty($transaksi['catatan'])): ?>
            <div>
                <span>Ket:</span>
                <span><?= htmlspecialchars($transaksi['catatan']) ?></span>
            </div>
        <?php endif; ?>
    </div>

    <table class="receipt-table">
        <thead>
            <tr>
                <th>Produk</th>
                <th style="text-align: center;">Qty</th>
                <th style="text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td colspan="3">
                        <?= htmlspecialchars($item['nama_barang']) ?>
                    </td>
                </tr>
                <tr>
                    <td style="color:#555; padding-left: 10px;">
                        <?= (int)$item['kuantitas'] ?> x <?= number_format($item['harga_satuan'], 0, ',', '.') ?>
                    </td>
                    <td style="text-align: center;"></td>
                    <td style="text-align: right;">
                        <?= number_format($item['subtotal'], 0, ',', '.') ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="receipt-totals">
        <div class="grand-total">
            <span>TOTAL:</span>
            <span>Rp <?= number_format($transaksi['total_bayar'], 0, ',', '.') ?></span>
        </div>
        <div>
            <span>TUNAI:</span>
            <span>Rp <?= number_format($transaksi['uang_tunai'], 0, ',', '.') ?></span>
        </div>
        <div>
            <span>KEMBALI:</span>
            <span>Rp <?= number_format($transaksi['kembalian'], 0, ',', '.') ?></span>
        </div>
    </div>

    <div class="receipt-footer">
        <p><?= RECEIPT_FOOTER ?></p>
        <p>Barang yang sudah dibeli tidak dapat ditukar/dikembalikan.</p>
    </div>
</div>

<script>
    // Otomatis buka dialog print saat halaman dimuat
    window.addEventListener('load', function() {
        // window.print();
    });
</script>

</body>
</html>

