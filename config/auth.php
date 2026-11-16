<?php
/**
 * Authentication and Role Authorization Helper
 */

require_once __DIR__ . '/config.php';

function start_session_if_none() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function is_logged_in(): bool {
    start_session_if_none();
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function get_logged_user(): ?array {
    start_session_if_none();
    if (!is_logged_in()) {
        return null;
    }
    return [
        'id_user'      => $_SESSION['user_id'],
        'nama_lengkap' => $_SESSION['nama_lengkap'] ?? '',
        'username'     => $_SESSION['username'] ?? '',
        'role'         => $_SESSION['role'] ?? 'kasir'
    ];
}

function require_login() {
    if (!is_logged_in()) {
        set_flash('warning', 'Silakan login terlebih dahulu untuk mengakses sistem.');
        header('Location: ' . BASE_URL . 'index.php?page=login');
        exit;
    }
}

function require_role($allowedRoles) {
    require_login();
    $user = get_logged_user();
    if (!is_array($allowedRoles)) {
        $allowedRoles = [$allowedRoles];
    }
    if (!in_array($user['role'], $allowedRoles)) {
        set_flash('danger', 'Akses ditolak! Anda tidak memiliki izin untuk membuka halaman tersebut.');
        if ($user['role'] === 'kasir') {
            header('Location: ' . BASE_URL . 'index.php?page=pos');
        } else {
            header('Location: ' . BASE_URL . 'index.php?page=dashboard');
        }
        exit;
    }
}

