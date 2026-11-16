<?php
/**
 * Controller Data Mining - Algoritma Apriori (Market Basket Analysis)
 */

require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../models/Apriori.php';

class AprioriController {
    private Apriori $aprioriModel;

    public function __construct() {
        require_role('admin');
        $this->aprioriModel = new Apriori();
    }

    public function index() {
        // Ambil riwayat analisis tersimpan
        $historyList = $this->aprioriModel->getHistory(20);

        // Nilai default tanggal & parameter
        $defaultStartDate = date('Y-m-d', strtotime('-30 days'));
        $defaultEndDate = date('Y-m-d');
        $defaultMinSupport = 15;
        $defaultMinConfidence = 50;

        require __DIR__ . '/../views/apriori/index.php';
    }

    public function proses() {
        $startDate = $_REQUEST['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $_REQUEST['end_date'] ?? date('Y-m-d');
        $minSupport = (float)($_REQUEST['min_support'] ?? 15);
        $minConfidence = (float)($_REQUEST['min_confidence'] ?? 50);

        if (empty($startDate) || empty($endDate)) {
            set_flash('danger', 'Rentang tanggal awal dan akhir wajib diisi.');
            header('Location: ' . BASE_URL . 'index.php?page=apriori');
            exit;
        }

        if ($minSupport <= 0 || $minSupport > 100) {
            set_flash('danger', 'Nilai Minimum Support harus berada di antara 1% sampai 100%.');
            header('Location: ' . BASE_URL . 'index.php?page=apriori');
            exit;
        }

        if ($minConfidence <= 0 || $minConfidence > 100) {
            set_flash('danger', 'Nilai Minimum Confidence harus berada di antara 1% sampai 100%.');
            header('Location: ' . BASE_URL . 'index.php?page=apriori');
            exit;
        }

        $result = $this->aprioriModel->runApriori($startDate, $endDate, $minSupport, $minConfidence);

        if ($result['status'] === 'error') {
            set_flash('warning', $result['message']);
            header('Location: ' . BASE_URL . 'index.php?page=apriori');
            exit;
        }

        // Simpan sementara di session untuk kemudahan simpan ke database jika ditekan tombol simpan
        start_session_if_none();
        $_SESSION['last_apriori_result'] = $result;

        $isSaved = false;
        $savedId = null;

        require __DIR__ . '/../views/apriori/hasil.php';
    }

    public function simpan() {
        start_session_if_none();
        if (empty($_SESSION['last_apriori_result'])) {
            set_flash('danger', 'Tidak ada hasil analisis yang aktif untuk disimpan.');
            header('Location: ' . BASE_URL . 'index.php?page=apriori');
            exit;
        }

        $user = get_logged_user();
        $result = $_SESSION['last_apriori_result'];

        $id = $this->aprioriModel->saveAnalysis($result, (int)$user['id_user']);

        if ($id) {
            set_flash('success', "Hasil analisis Apriori berhasil disimpan ke database (ID Analisis: #$id).");
            header('Location: ' . BASE_URL . 'index.php?page=apriori&action=detail&id=' . $id);
        } else {
            set_flash('danger', 'Gagal menyimpan hasil analisis.');
            header('Location: ' . BASE_URL . 'index.php?page=apriori');
        }
        exit;
    }

    public function detail() {
        $id = (int)($_GET['id'] ?? 0);
        $savedAnalysis = $this->aprioriModel->getHistoryById($id);

        if (!$savedAnalysis || empty($savedAnalysis['data_decoded'])) {
            set_flash('danger', 'Data analisis Apriori tidak ditemukan.');
            header('Location: ' . BASE_URL . 'index.php?page=apriori');
            exit;
        }

        $result = $savedAnalysis['data_decoded'];
        $isSaved = true;
        $savedId = $id;

        require __DIR__ . '/../views/apriori/hasil.php';
    }

    public function cetak() {
        $id = (int)($_GET['id'] ?? 0);
        $result = null;

        if ($id > 0) {
            $saved = $this->aprioriModel->getHistoryById($id);
            if ($saved && !empty($saved['data_decoded'])) {
                $result = $saved['data_decoded'];
                $result['id_analisis'] = $id;
                $result['tanggal_analisis'] = $saved['tanggal_analisis'];
                $result['analyst_name'] = $saved['nama_user'];
            }
        }

        if (!$result) {
            start_session_if_none();
            if (!empty($_SESSION['last_apriori_result'])) {
                $result = $_SESSION['last_apriori_result'];
                $user = get_logged_user();
                $result['tanggal_analisis'] = date('Y-m-d H:i:s');
                $result['analyst_name'] = $user['nama_lengkap'];
            }
        }

        if (!$result) {
            die("Tidak ada data analisis yang dapat dicetak.");
        }

        require __DIR__ . '/../views/apriori/print.php';
    }

    public function delete() {
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            if ($this->aprioriModel->deleteHistory($id)) {
                set_flash('success', 'Riwayat analisis berhasil dihapus.');
            } else {
                set_flash('danger', 'Gagal menghapus riwayat analisis.');
            }
        }
        header('Location: ' . BASE_URL . 'index.php?page=apriori');
        exit;
    }
}

