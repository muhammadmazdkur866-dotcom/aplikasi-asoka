<?php
/**
 * Controller Autentikasi
 */

require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function login() {
        if (is_logged_in()) {
            $user = get_logged_user();
            if ($user['role'] === 'kasir') {
                header('Location: ' . BASE_URL . 'index.php?page=pos');
            } else {
                header('Location: ' . BASE_URL . 'index.php?page=dashboard');
            }
            exit;
        }

        require __DIR__ . '/../views/auth/login.php';
    }

    public function processLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'index.php?page=login');
            exit;
        }

        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($username) || empty($password)) {
            set_flash('danger', 'Username dan password wajib diisi.');
            header('Location: ' . BASE_URL . 'index.php?page=login');
            exit;
        }

        $user = $this->userModel->findByUsername($username);

        if ($user && password_verify($password, $user['password'])) {
            start_session_if_none();
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['role'] = $user['role'];

            set_flash('success', "Selamat datang kembali, {$user['nama_lengkap']}!");

            if ($user['role'] === 'kasir') {
                header('Location: ' . BASE_URL . 'index.php?page=pos');
            } else {
                header('Location: ' . BASE_URL . 'index.php?page=dashboard');
            }
            exit;
        } else {
            set_flash('danger', 'Username atau password yang Anda masukkan salah.');
            header('Location: ' . BASE_URL . 'index.php?page=login');
            exit;
        }
    }

    public function logout() {
        start_session_if_none();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();

        start_session_if_none();
        set_flash('info', 'Anda telah berhasil keluar dari sistem.');
        header('Location: ' . BASE_URL . 'index.php?page=login');
        exit;
    }
}

