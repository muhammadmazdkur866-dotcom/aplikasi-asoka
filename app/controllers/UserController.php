<?php
/**
 * Controller Manajemen Pengguna (Admin & Kasir)
 */

require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../models/User.php';

class UserController {
    private User $userModel;

    public function __construct() {
        require_role('admin');
        $this->userModel = new User();
    }

    public function index() {
        $usersList = $this->userModel->getAll();
        require __DIR__ . '/../views/users/index.php';
    }

    public function create() {
        require __DIR__ . '/../views/users/create.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama = trim($_POST['nama_lengkap'] ?? '');
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $role = trim($_POST['role'] ?? 'kasir');

            if (empty($nama) || empty($username) || empty($password)) {
                set_flash('danger', 'Nama lengkap, username, dan password wajib diisi.');
                header('Location: ' . BASE_URL . 'index.php?page=users&action=create');
                exit;
            }

            // Cek apakah username sudah ada
            $existing = $this->userModel->findByUsername($username);
            if ($existing) {
                set_flash('danger', "Username '{$username}' sudah terdaftar.");
                header('Location: ' . BASE_URL . 'index.php?page=users&action=create');
                exit;
            }

            $success = $this->userModel->create([
                'nama_lengkap' => $nama,
                'username' => $username,
                'password' => $password,
                'role' => $role
            ]);

            if ($success) {
                set_flash('success', "Pengguna '{$nama}' ({$role}) berhasil ditambahkan.");
                header('Location: ' . BASE_URL . 'index.php?page=users');
            } else {
                set_flash('danger', 'Gagal menambahkan pengguna.');
                header('Location: ' . BASE_URL . 'index.php?page=users&action=create');
            }
            exit;
        }
    }

    public function edit() {
        $id = (int)($_GET['id'] ?? 0);
        $user = $this->userModel->findById($id);

        if (!$user) {
            set_flash('danger', 'Data pengguna tidak ditemukan.');
            header('Location: ' . BASE_URL . 'index.php?page=users');
            exit;
        }

        require __DIR__ . '/../views/users/edit.php';
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id_user'] ?? 0);
            $nama = trim($_POST['nama_lengkap'] ?? '');
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $role = trim($_POST['role'] ?? 'kasir');

            if ($id <= 0 || empty($nama) || empty($username)) {
                set_flash('danger', 'Data pengguna tidak valid.');
                header('Location: ' . BASE_URL . 'index.php?page=users&action=edit&id=' . $id);
                exit;
            }

            // Cek username duplicate jika berubah
            $existing = $this->userModel->findByUsername($username);
            if ($existing && $existing['id_user'] != $id) {
                set_flash('danger', "Username '{$username}' sudah digunakan oleh pengguna lain.");
                header('Location: ' . BASE_URL . 'index.php?page=users&action=edit&id=' . $id);
                exit;
            }

            $success = $this->userModel->update($id, [
                'nama_lengkap' => $nama,
                'username' => $username,
                'password' => $password,
                'role' => $role
            ]);

            if ($success) {
                set_flash('success', "Data pengguna '{$nama}' berhasil diperbarui.");
                header('Location: ' . BASE_URL . 'index.php?page=users');
            } else {
                set_flash('danger', 'Gagal memperbarui pengguna.');
                header('Location: ' . BASE_URL . 'index.php?page=users&action=edit&id=' . $id);
            }
            exit;
        }
    }

    public function delete() {
        $id = (int)($_GET['id'] ?? 0);
        $currentUser = get_logged_user();

        if ($id === (int)$currentUser['id_user']) {
            set_flash('danger', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
            header('Location: ' . BASE_URL . 'index.php?page=users');
            exit;
        }

        if ($id > 0) {
            try {
                if ($this->userModel->delete($id)) {
                    set_flash('success', 'Pengguna berhasil dihapus.');
                } else {
                    set_flash('danger', 'Gagal menghapus pengguna.');
                }
            } catch (Exception $e) {
                set_flash('danger', 'Pengguna tidak dapat dihapus karena tercatat pada riwayat transaksi penjualan.');
            }
        }
        header('Location: ' . BASE_URL . 'index.php?page=users');
        exit;
    }
}

