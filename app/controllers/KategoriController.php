<?php
/**
 * Controller Kategori (Master Data)
 */

require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../models/Kategori.php';

class KategoriController {
    private Kategori $kategoriModel;

    public function __construct() {
        require_role('admin');
        $this->kategoriModel = new Kategori();
    }

    public function index() {
        $kategoriList = $this->kategoriModel->getAll();
        require __DIR__ . '/../views/kategori/index.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama = trim($_POST['nama_kategori'] ?? '');
            if (empty($nama)) {
                set_flash('danger', 'Nama kategori tidak boleh kosong.');
            } else {
                if ($this->kategoriModel->create($nama)) {
                    set_flash('success', "Kategori '{$nama}' berhasil ditambahkan.");
                } else {
                    set_flash('danger', 'Gagal menambahkan kategori.');
                }
            }
        }
        header('Location: ' . BASE_URL . 'index.php?page=kategori');
        exit;
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id_kategori'] ?? 0);
            $nama = trim($_POST['nama_kategori'] ?? '');
            if ($id <= 0 || empty($nama)) {
                set_flash('danger', 'Data kategori tidak valid.');
            } else {
                if ($this->kategoriModel->update($id, $nama)) {
                    set_flash('success', "Kategori berhasil diperbarui.");
                } else {
                    set_flash('danger', 'Gagal memperbarui kategori.');
                }
            }
        }
        header('Location: ' . BASE_URL . 'index.php?page=kategori');
        exit;
    }

    public function delete() {
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            try {
                if ($this->kategoriModel->delete($id)) {
                    set_flash('success', 'Kategori berhasil dihapus.');
                } else {
                    set_flash('danger', 'Gagal menghapus kategori.');
                }
            } catch (Exception $e) {
                set_flash('danger', 'Kategori tidak dapat dihapus karena masih digunakan oleh produk barang.');
            }
        }
        header('Location: ' . BASE_URL . 'index.php?page=kategori');
        exit;
    }
}

