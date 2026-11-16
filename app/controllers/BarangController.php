<?php
/**
 * Controller Barang (Master Data Sembako)
 */

require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../models/Barang.php';
require_once __DIR__ . '/../models/Kategori.php';

class BarangController {
    private Barang $barangModel;
    private Kategori $kategoriModel;

    public function __construct() {
        $this->barangModel = new Barang();
        $this->kategoriModel = new Kategori();
    }

    public function index() {
        require_role('admin');
        $idKategori = !empty($_GET['kategori']) ? (int)$_GET['kategori'] : null;
        $keyword = !empty($_GET['q']) ? trim($_GET['q']) : null;

        $barangList = $this->barangModel->getAll($idKategori, $keyword);
        $kategoriList = $this->kategoriModel->getAll();
        $nextId = $this->barangModel->generateNextId();

        require __DIR__ . '/../views/barang/index.php';
    }

    public function create() {
        require_role('admin');
        $kategoriList = $this->kategoriModel->getAll();
        $nextId = $this->barangModel->generateNextId();
        require __DIR__ . '/../views/barang/create.php';
    }

    public function store() {
        require_role('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idBarang = trim($_POST['id_barang'] ?? '');
            $idKategori = (int)($_POST['id_kategori'] ?? 0);
            $namaBarang = trim($_POST['nama_barang'] ?? '');
            $hargaBeli = (float)($_POST['harga_beli'] ?? 0);
            $hargaJual = (float)($_POST['harga_jual'] ?? 0);
            $stok = (int)($_POST['stok'] ?? 0);
            $stokMinimum = (int)($_POST['stok_minimum'] ?? 5);
            $satuan = trim($_POST['satuan'] ?? 'pcs');

            if (empty($idBarang) || empty($namaBarang) || $idKategori <= 0) {
                set_flash('danger', 'Semua kolom bertanda bintang (*) wajib diisi.');
                header('Location: ' . BASE_URL . 'index.php?page=barang&action=create');
                exit;
            }

            // Cek apakah ID Barang sudah ada
            $existing = $this->barangModel->findById($idBarang);
            if ($existing) {
                set_flash('danger', "Kode / Barcode '{$idBarang}' sudah digunakan oleh produk lain.");
                header('Location: ' . BASE_URL . 'index.php?page=barang&action=create');
                exit;
            }

            $success = $this->barangModel->create([
                'id_barang' => $idBarang,
                'id_kategori' => $idKategori,
                'nama_barang' => $namaBarang,
                'harga_beli' => $hargaBeli,
                'harga_jual' => $hargaJual,
                'stok' => $stok,
                'stok_minimum' => $stokMinimum,
                'satuan' => $satuan
            ]);

            if ($success) {
                set_flash('success', "Barang '{$namaBarang}' berhasil ditambahkan ke katalog.");
                header('Location: ' . BASE_URL . 'index.php?page=barang');
            } else {
                set_flash('danger', 'Gagal menambahkan barang.');
                header('Location: ' . BASE_URL . 'index.php?page=barang&action=create');
            }
            exit;
        }
    }

    public function edit() {
        require_role('admin');
        $id = trim($_GET['id'] ?? '');
        $barang = $this->barangModel->findById($id);

        if (!$barang) {
            set_flash('danger', 'Data barang tidak ditemukan.');
            header('Location: ' . BASE_URL . 'index.php?page=barang');
            exit;
        }

        $kategoriList = $this->kategoriModel->getAll();
        require __DIR__ . '/../views/barang/edit.php';
    }

    public function update() {
        require_role('admin');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = trim($_POST['id_barang'] ?? '');
            $idKategori = (int)($_POST['id_kategori'] ?? 0);
            $namaBarang = trim($_POST['nama_barang'] ?? '');
            $hargaBeli = (float)($_POST['harga_beli'] ?? 0);
            $hargaJual = (float)($_POST['harga_jual'] ?? 0);
            $stok = (int)($_POST['stok'] ?? 0);
            $stokMinimum = (int)($_POST['stok_minimum'] ?? 5);
            $satuan = trim($_POST['satuan'] ?? 'pcs');

            if (empty($id) || empty($namaBarang) || $idKategori <= 0) {
                set_flash('danger', 'Semua kolom bertanda bintang (*) wajib diisi.');
                header('Location: ' . BASE_URL . 'index.php?page=barang&action=edit&id=' . urlencode($id));
                exit;
            }

            $success = $this->barangModel->update($id, [
                'id_kategori' => $idKategori,
                'nama_barang' => $namaBarang,
                'harga_beli' => $hargaBeli,
                'harga_jual' => $hargaJual,
                'stok' => $stok,
                'stok_minimum' => $stokMinimum,
                'satuan' => $satuan
            ]);

            if ($success) {
                set_flash('success', "Data barang '{$namaBarang}' berhasil diperbarui.");
                header('Location: ' . BASE_URL . 'index.php?page=barang');
            } else {
                set_flash('danger', 'Gagal memperbarui data barang.');
                header('Location: ' . BASE_URL . 'index.php?page=barang&action=edit&id=' . urlencode($id));
            }
            exit;
        }
    }

    public function delete() {
        require_role('admin');
        $id = trim($_GET['id'] ?? '');
        if (!empty($id)) {
            try {
                if ($this->barangModel->delete($id)) {
                    set_flash('success', 'Data barang berhasil dihapus.');
                } else {
                    set_flash('danger', 'Gagal menghapus barang.');
                }
            } catch (Exception $e) {
                set_flash('danger', 'Barang tidak dapat dihapus karena sudah memiliki riwayat transaksi.');
            }
        }
        header('Location: ' . BASE_URL . 'index.php?page=barang');
        exit;
    }

    /**
     * Endpoint JSON AJAX untuk pencarian produk di Kasir POS (Barcode / Nama)
     */
    public function searchAjax() {
        require_login();
        header('Content-Type: application/json');

        $keyword = trim($_GET['q'] ?? '');
        $idKategori = !empty($_GET['kategori']) ? (int)$_GET['kategori'] : null;

        $results = $this->barangModel->getAll($idKategori, $keyword);
        echo json_encode([
            'status' => 'success',
            'data' => $results
        ]);
        exit;
    }
}

