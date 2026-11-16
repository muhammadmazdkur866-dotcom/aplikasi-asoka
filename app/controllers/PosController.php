<?php
/**
 * Controller POS (Point of Sale / Kasir)
 */

require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../models/Barang.php';
require_once __DIR__ . '/../models/Kategori.php';
require_once __DIR__ . '/../models/Transaksi.php';

class PosController {
    private Barang $barangModel;
    private Kategori $kategoriModel;
    private Transaksi $transaksiModel;

    public function __construct() {
        require_login();
        $this->barangModel = new Barang();
        $this->kategoriModel = new Kategori();
        $this->transaksiModel = new Transaksi();
    }

    public function index() {
        $kategoriList = $this->kategoriModel->getAll();
        $barangList = $this->barangModel->getAll();
        $currentUser = get_logged_user();
        $nextInvoice = $this->transaksiModel->generateInvoiceNumber();

        require __DIR__ . '/../views/pos/index.php';
    }

    /**
     * Endpoint AJAX untuk menyimpan transaksi POS
     */
    public function simpanTransaksi() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Metode request tidak diizinkan.']);
            exit;
        }

        $inputRaw = file_get_contents('php://input');
        $data = json_decode($inputRaw, true);

        if (!$data || empty($data['items'])) {
            echo json_encode(['status' => 'error', 'message' => 'Keranjang belanja masih kosong.']);
            exit;
        }

        $currentUser = get_logged_user();
        $totalBayar = (float)($data['total_bayar'] ?? 0);
        $uangTunai = (float)($data['uang_tunai'] ?? 0);
        $kembalian = (float)($data['kembalian'] ?? 0);

        if ($uangTunai < $totalBayar) {
            echo json_encode(['status' => 'error', 'message' => 'Uang tunai kurang dari total pembayaran.']);
            exit;
        }

        $header = [
            'id_transaksi' => $this->transaksiModel->generateInvoiceNumber(),
            'tanggal_waktu' => date('Y-m-d H:i:s'),
            'id_user' => (int)$currentUser['id_user'],
            'total_bayar' => $totalBayar,
            'uang_tunai' => $uangTunai,
            'kembalian' => $kembalian,
            'catatan' => !empty($data['catatan']) ? trim($data['catatan']) : 'Transaksi Kasir POS'
        ];

        $items = [];
        foreach ($data['items'] as $it) {
            $items[] = [
                'id_barang' => $it['id_barang'],
                'kuantitas' => (int)$it['kuantitas'],
                'harga_satuan' => (float)$it['harga_jual'],
                'subtotal' => (float)$it['subtotal']
            ];
        }

        $result = $this->transaksiModel->create($header, $items);

        if ($result['success']) {
            echo json_encode([
                'status' => 'success',
                'id_transaksi' => $result['id_transaksi'],
                'message' => 'Transaksi berhasil disimpan!',
                'redirect_print' => BASE_URL . 'index.php?page=pos&action=cetak_struk&id=' . urlencode($result['id_transaksi'])
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => $result['message']
            ]);
        }
        exit;
    }

    /**
     * Halaman Cetak Struk Belanja Kasir (Thermal Printer Friendly)
     */
    public function cetakStruk() {
        $id = trim($_GET['id'] ?? '');
        $transaksi = $this->transaksiModel->findById($id);

        if (!$transaksi) {
            die("Transaksi tidak ditemukan.");
        }

        $items = $this->transaksiModel->getItemsByTransaksiId($id);
        require __DIR__ . '/../views/pos/struk.php';
    }
}

