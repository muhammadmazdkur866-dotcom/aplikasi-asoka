<?php
$currentPage = $_GET['page'] ?? 'dashboard';
$currentAction = $_GET['action'] ?? 'index';
$currentUser = get_logged_user();
$userRole = $currentUser['role'] ?? 'kasir';
?>
<!-- Sidebar -->
<div id="sidebar-wrapper" class="d-flex flex-column flex-shrink-0">
    <div class="sidebar-brand text-truncate">
        <i class="bi bi-cart4 text-light fs-3"></i>
        <div>
            <div class="fs-6 fw-bold lh-1 text-white">ASOKA</div>
            <small class="text-secondary" style="font-size: 0.65rem; letter-spacing: 0.5px;">SEMBAKO & POS</small>
        </div>
    </div>

    <div class="px-3 py-3 border-bottom border-secondary border-opacity-25 d-flex align-items-center gap-2">
        <div class="rounded-circle bg-secondary bg-opacity-25 p-2 text-light d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
            <i class="bi bi-person-fill fs-5"></i>
        </div>
        <div class="text-truncate">
            <div class="text-white fw-semibold small text-truncate"><?= htmlspecialchars($currentUser['nama_lengkap']) ?></div>
            <span class="badge <?= $userRole === 'admin' ? 'bg-secondary' : 'bg-dark' ?> text-uppercase border border-secondary" style="font-size: 0.65rem;">
                <?= $userRole ?>
            </span>
        </div>
    </div>

    <ul class="nav nav-pills flex-column mb-auto py-2">
        <?php if ($userRole === 'admin'): ?>
            <li class="nav-item">
                <a href="<?= BASE_URL ?>index.php?page=dashboard" class="nav-link <?= ($currentPage === 'dashboard') ? 'active' : '' ?>">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <div class="sidebar-heading">Master Data</div>

            <li class="nav-item">
                <a href="<?= BASE_URL ?>index.php?page=kategori" class="nav-link <?= ($currentPage === 'kategori') ? 'active' : '' ?>">
                    <i class="bi bi-tags"></i>
                    <span>Kategori Barang</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= BASE_URL ?>index.php?page=barang" class="nav-link <?= ($currentPage === 'barang') ? 'active' : '' ?>">
                    <i class="bi bi-box-seam"></i>
                    <span>Data Barang Sembako</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= BASE_URL ?>index.php?page=users" class="nav-link <?= ($currentPage === 'users') ? 'active' : '' ?>">
                    <i class="bi bi-people"></i>
                    <span>Manajemen Pengguna</span>
                </a>
            </li>
        <?php endif; ?>

        <div class="sidebar-heading">Transaksi & POS</div>

        <li class="nav-item">
            <a href="<?= BASE_URL ?>index.php?page=pos" class="nav-link <?= ($currentPage === 'pos') ? 'active' : '' ?>">
                <i class="bi bi-calculator"></i>
                <span>Kasir (Point of Sale)</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="<?= BASE_URL ?>index.php?page=transaksi" class="nav-link <?= ($currentPage === 'transaksi') ? 'active' : '' ?>">
                <i class="bi bi-receipt"></i>
                <span>Riwayat Transaksi</span>
            </a>
        </li>

        <?php if ($userRole === 'admin'): ?>
            <div class="sidebar-heading">Data Mining (AI)</div>

            <li class="nav-item">
                <a href="<?= BASE_URL ?>index.php?page=apriori" class="nav-link <?= ($currentPage === 'apriori') ? 'active' : '' ?>">
                    <i class="bi bi-diagram-3"></i>
                    <span>Analisis Apriori MBA</span>
                </a>
            </li>

            <div class="sidebar-heading">Laporan & Rekap</div>

            <li class="nav-item">
                <a href="<?= BASE_URL ?>index.php?page=laporan&action=penjualan" class="nav-link <?= ($currentPage === 'laporan' && $currentAction === 'penjualan') ? 'active' : '' ?>">
                    <i class="bi bi-graph-up-arrow"></i>
                    <span>Laporan Penjualan</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= BASE_URL ?>index.php?page=laporan&action=stok" class="nav-link <?= ($currentPage === 'laporan' && $currentAction === 'stok') ? 'active' : '' ?>">
                    <i class="bi bi-boxes"></i>
                    <span>Laporan Stok Barang</span>
                </a>
            </li>
        <?php endif; ?>
    </ul>

    <div class="p-3 border-top border-secondary border-opacity-25">
        <a href="<?= BASE_URL ?>index.php?page=login&action=logout" class="btn btn-outline-danger w-100 btn-sm d-flex align-items-center justify-content-center gap-2">
            <i class="bi bi-box-arrow-right"></i>
            <span>Keluar (Logout)</span>
        </a>
    </div>
</div>

