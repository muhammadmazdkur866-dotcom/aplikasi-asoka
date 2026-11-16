<?php
/**
 * Controller Riwayat Transaksi
 */

require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../models/Transaksi.php';

class TransaksiController {
    private Transaksi $transaksiModel;

    public function __construct() {
        require_login();
        $this->transaksiModel = new Transaksi();
    }

    public function index() {
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        $search = $_GET['q'] ?? '';

        $transaksiList = $this->transaksiModel->getAll($startDate, $endDate, $search, 200);

        require __DIR__ . '/../views/transaksi/index.php';
    }

    public function detail() {
        $id = trim($_GET['id'] ?? '');
        $transaksi = $this->transaksiModel->findById($id);

        if (!$transaksi) {
            set_flash('danger', 'Transaksi tidak ditemukan.');
            header('Location: ' . BASE_URL . 'index.php?page=transaksi');
            exit;
        }

        $items = $this->transaksiModel->getItemsByTransaksiId($id);

        // Jika request via AJAX JSON untuk modal
        if (!empty($_GET['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success',
                'transaksi' => $transaksi,
                'items' => $items
            ]);
            exit;
        }

        require __DIR__ . '/../views/transaksi/detail.php';
    }

    public function delete() {
        require_role('admin');
        $id = trim($_GET['id'] ?? '');

        if (!empty($id)) {
            if ($this->transaksiModel->delete($id)) {
                set_flash('success', "Transaksi '{$id}' berhasil dihapus.");
            } else {
                set_flash('danger', 'Gagal menghapus transaksi.');
            }
        }

        header('Location: ' . BASE_URL . 'index.php?page=transaksi');
        exit;
    }
}

