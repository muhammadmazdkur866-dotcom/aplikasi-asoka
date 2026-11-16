<?php
/**
 * Controller Laporan (Admin)
 */

require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../models/Transaksi.php';
require_once __DIR__ . '/../models/Barang.php';
require_once __DIR__ . '/../models/Kategori.php';

class LaporanController {
    private Transaksi $transaksiModel;
    private Barang $barangModel;
    private Kategori $kategoriModel;

    public function __construct() {
        require_role('admin');
        $this->transaksiModel = new Transaksi();
        $this->barangModel = new Barang();
        $this->kategoriModel = new Kategori();
    }

    public function penjualan() {
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $_GET['end_date'] ?? date('Y-m-d');

        $laporanList = $this->transaksiModel->getDetailedReport($startDate, $endDate);

        $totalOmset = 0;
        $totalModal = 0;
        $totalLaba = 0;
        $totalQty = 0;

        foreach ($laporanList as $row) {
            $totalOmset += (float)$row['subtotal'];
            $totalModal += (float)$row['total_modal'];
            $totalLaba += (float)$row['laba'];
            $totalQty += (int)$row['kuantitas'];
        }

        require __DIR__ . '/../views/laporan/penjualan.php';
    }

    public function cetakPenjualan() {
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $_GET['end_date'] ?? date('Y-m-d');

        $laporanList = $this->transaksiModel->getDetailedReport($startDate, $endDate);

        $totalOmset = 0;
        $totalModal = 0;
        $totalLaba = 0;
        $totalQty = 0;

        foreach ($laporanList as $row) {
            $totalOmset += (float)$row['subtotal'];
            $totalModal += (float)$row['total_modal'];
            $totalLaba += (float)$row['laba'];
            $totalQty += (int)$row['kuantitas'];
        }

        require __DIR__ . '/../views/laporan/cetak_penjualan.php';
    }

    public function stok() {
        $idKategori = !empty($_GET['kategori']) ? (int)$_GET['kategori'] : null;
        $filterStatus = $_GET['status'] ?? 'all'; // all, low, safe, empty

        $barangList = $this->barangModel->getAll($idKategori);
        $kategoriList = $this->kategoriModel->getAll();
        $stockValues = $this->barangModel->getTotalStockValue();

        // Filter berdasarkan status stok
        if ($filterStatus === 'low') {
            $barangList = array_filter($barangList, fn($b) => $b['stok'] > 0 && $b['stok'] <= $b['stok_minimum']);
        } elseif ($filterStatus === 'empty') {
            $barangList = array_filter($barangList, fn($b) => $b['stok'] <= 0);
        } elseif ($filterStatus === 'safe') {
            $barangList = array_filter($barangList, fn($b) => $b['stok'] > $b['stok_minimum']);
        }

        require __DIR__ . '/../views/laporan/stok.php';
    }

    public function cetakStok() {
        $idKategori = !empty($_GET['kategori']) ? (int)$_GET['kategori'] : null;
        $filterStatus = $_GET['status'] ?? 'all';

        $barangList = $this->barangModel->getAll($idKategori);
        $stockValues = $this->barangModel->getTotalStockValue();

        if ($filterStatus === 'low') {
            $barangList = array_filter($barangList, fn($b) => $b['stok'] > 0 && $b['stok'] <= $b['stok_minimum']);
        } elseif ($filterStatus === 'empty') {
            $barangList = array_filter($barangList, fn($b) => $b['stok'] <= 0);
        } elseif ($filterStatus === 'safe') {
            $barangList = array_filter($barangList, fn($b) => $b['stok'] > $b['stok_minimum']);
        }

        require __DIR__ . '/../views/laporan/cetak_stok.php';
    }
}

