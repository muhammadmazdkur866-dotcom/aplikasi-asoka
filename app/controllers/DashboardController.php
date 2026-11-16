<?php
/**
 * Controller Dashboard
 */

require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../models/Barang.php';
require_once __DIR__ . '/../models/Transaksi.php';
require_once __DIR__ . '/../models/Apriori.php';

class DashboardController {
    private Barang $barangModel;
    private Transaksi $transaksiModel;
    private Apriori $aprioriModel;

    public function __construct() {
        require_login();
        $this->barangModel = new Barang();
        $this->transaksiModel = new Transaksi();
        $this->aprioriModel = new Apriori();
    }

    public function index() {
        $todaySummary = $this->transaksiModel->getTodaySummary();
        $monthSummary = $this->transaksiModel->getMonthSummary();
        $totalBarang = $this->barangModel->countAll();
        $lowStockList = $this->barangModel->getLowStock();
        $lowStockCount = count($lowStockList);
        $stockValues = $this->barangModel->getTotalStockValue();

        $recentTransactions = $this->transaksiModel->getRecentTransactions(5);
        $chartData = $this->transaksiModel->getSalesChartData(7);
        $topProducts = $this->transaksiModel->getTopSellingProducts(5);
        $aprioriHistory = $this->aprioriModel->getHistory(1);
        $latestApriori = !empty($aprioriHistory) ? $aprioriHistory[0] : null;

        require __DIR__ . '/../views/dashboard/index.php';
    }
}

