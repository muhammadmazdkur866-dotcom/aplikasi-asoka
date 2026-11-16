<?php
/**
 * Konfigurasi Utama Aplikasi POS Toko Sembako Asoka & Apriori MBA
 */

// Set Timezone Indonesia (WIB)
date_default_timezone_set('Asia/Jakarta');

// Nama dan Identitas Toko
define('APP_NAME', 'Asoka');
define('APP_TAGLINE', 'Sistem Kasir POS & Market Basket Analysis (Algoritma Apriori)');
define('APP_VERSION', '2.0.0');
define('STORE_NAME', 'ASOKA');
define('STORE_ADDRESS', 'Jl. Asoka Raya No. 12, Jakarta');
define('STORE_PHONE', '0812-3456-7890');
define('RECEIPT_FOOTER', 'Terima kasih telah berbelanja di Asoka! Semoga berkah.');

// Konfigurasi Database MySQL
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_kasir_apriori');
define('DB_PORT', 3306);

// Deteksi Otomatis Base URL
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$baseUrl = rtrim($protocol . $host . $scriptDir, '/');
if (substr($baseUrl, -1) !== '/') {
    $baseUrl .= '/';
}
define('BASE_URL', $baseUrl);

/**
 * Format Angka ke Format Mata Uang Rupiah
 */
function format_rupiah($angka) {
    return 'Rp ' . number_format((float)$angka, 0, ',', '.');
}

/**
 * Format Angka biasa dengan pemisah ribuan
 */
function format_angka($angka, $desimal = 0) {
    return number_format((float)$angka, $desimal, ',', '.');
}

/**
 * Format Tanggal Indonesia
 */
function format_tanggal_indo($datetime) {
    if (!$datetime || $datetime === '0000-00-00' || $datetime === '0000-00-00 00:00:00') {
        return '-';
    }
    $timestamp = strtotime($datetime);
    $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    $d = date('j', $timestamp);
    $m = $bulan[(int)date('n', $timestamp)];
    $y = date('Y', $timestamp);
    $time = date('H:i', $timestamp);
    
    return "$d $m $y, $time WIB";
}

function format_tgl_pendek($date) {
    if (!$date) return '-';
    return date('d/m/Y', strtotime($date));
}

/**
 * Helper Flash Messages
 */
function set_flash($type, $message) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['flash_message'] = [
        'type' => $type, // 'success', 'danger', 'warning', 'info'
        'text' => $message
    ];
}

function get_flash() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['flash_message'])) {
        $flash = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $flash;
    }
    return null;
}

