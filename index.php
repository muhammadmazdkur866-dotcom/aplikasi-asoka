<?php
/**
 * Router Utama / Front Controller
 * Sistem Kasir POS & Market Basket Analysis (Apriori) - Toko Sembako Asoka
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth.php';

// Ambil parameter rute
$page = $_GET['page'] ?? '';
$action = $_GET['action'] ?? 'index';

// Jika belum login dan bukan request halaman login, redirect ke login
if (!is_logged_in() && $page !== 'login') {
    $page = 'login';
}

// Jika sudah login tapi page kosong, arahkan sesuai role
if (empty($page)) {
    $user = get_logged_user();
    if ($user && $user['role'] === 'kasir') {
        $page = 'pos';
    } else {
        $page = 'dashboard';
    }
}

// Dispatching rute
switch ($page) {
    case 'login':
        require_once __DIR__ . '/app/controllers/AuthController.php';
        $auth = new AuthController();
        if ($action === 'process') {
            $auth->processLogin();
        } elseif ($action === 'logout') {
            $auth->logout();
        } else {
            $auth->login();
        }
        break;

    case 'dashboard':
        require_once __DIR__ . '/app/controllers/DashboardController.php';
        $controller = new DashboardController();
        $controller->index();
        break;

    case 'kategori':
        require_once __DIR__ . '/app/controllers/KategoriController.php';
        $controller = new KategoriController();
        if ($action === 'store') {
            $controller->store();
        } elseif ($action === 'update') {
            $controller->update();
        } elseif ($action === 'delete') {
            $controller->delete();
        } else {
            $controller->index();
        }
        break;

    case 'barang':
        require_once __DIR__ . '/app/controllers/BarangController.php';
        $controller = new BarangController();
        if ($action === 'create') {
            $controller->create();
        } elseif ($action === 'store') {
            $controller->store();
        } elseif ($action === 'edit') {
            $controller->edit();
        } elseif ($action === 'update') {
            $controller->update();
        } elseif ($action === 'delete') {
            $controller->delete();
        } elseif ($action === 'search_ajax') {
            $controller->searchAjax();
        } else {
            $controller->index();
        }
        break;

    case 'users':
        require_once __DIR__ . '/app/controllers/UserController.php';
        $controller = new UserController();
        if ($action === 'create') {
            $controller->create();
        } elseif ($action === 'store') {
            $controller->store();
        } elseif ($action === 'edit') {
            $controller->edit();
        } elseif ($action === 'update') {
            $controller->update();
        } elseif ($action === 'delete') {
            $controller->delete();
        } else {
            $controller->index();
        }
        break;

    case 'pos':
        require_once __DIR__ . '/app/controllers/PosController.php';
        $controller = new PosController();
        if ($action === 'simpan_transaksi') {
            $controller->simpanTransaksi();
        } elseif ($action === 'cetak_struk') {
            $controller->cetakStruk();
        } else {
            $controller->index();
        }
        break;

    case 'transaksi':
        require_once __DIR__ . '/app/controllers/TransaksiController.php';
        $controller = new TransaksiController();
        if ($action === 'detail') {
            $controller->detail();
        } elseif ($action === 'delete') {
            $controller->delete();
        } else {
            $controller->index();
        }
        break;

    case 'apriori':
        require_once __DIR__ . '/app/controllers/AprioriController.php';
        $controller = new AprioriController();
        if ($action === 'proses') {
            $controller->proses();
        } elseif ($action === 'simpan') {
            $controller->simpan();
        } elseif ($action === 'detail') {
            $controller->detail();
        } elseif ($action === 'cetak') {
            $controller->cetak();
        } elseif ($action === 'delete') {
            $controller->delete();
        } else {
            $controller->index();
        }
        break;

    case 'laporan':
        require_once __DIR__ . '/app/controllers/LaporanController.php';
        $controller = new LaporanController();
        if ($action === 'penjualan') {
            $controller->penjualan();
        } elseif ($action === 'cetak_penjualan') {
            $controller->cetakPenjualan();
        } elseif ($action === 'stok') {
            $controller->stok();
        } elseif ($action === 'cetak_stok') {
            $controller->cetakStok();
        } else {
            $controller->penjualan();
        }
        break;

    default:
        http_response_code(404);
        echo "<h2 style='text-align:center; margin-top:50px;'>404 - Halaman Tidak Ditemukan</h2>";
        echo "<p style='text-align:center;'><a href='" . BASE_URL . "'>Kembali ke Beranda</a></p>";
        break;
}

