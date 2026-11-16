<?php
$currentUser = get_logged_user();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' . APP_NAME : APP_NAME . ' | POS & Apriori MBA' ?></title>
    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/custom.css">
    <!-- Chart.js (untuk visualisasi) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
</head>
<body>
<div class="d-flex" id="wrapper">
    <!-- Sidebar -->
    <?php require __DIR__ . '/sidebar.php'; ?>

    <!-- Page Content Wrapper -->
    <div id="page-content-wrapper" class="flex-grow-1 d-flex flex-column min-vh-100">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg top-navbar sticky-top">
            <div class="container-fluid">
                <button class="btn btn-light d-lg-none me-2" id="sidebarToggle">
                    <i class="bi bi-list fs-5"></i>
                </button>

                <div class="d-none d-md-block">
                    <span class="text-muted small fw-medium">
                        <i class="bi bi-shop me-1 text-secondary"></i> <?= STORE_NAME ?>
                    </span>
                    <span class="mx-2 text-muted">|</span>
                    <span class="badge bg-light text-dark border">
                        <i class="bi bi-calendar3 me-1"></i> <?= date('d M Y') ?>
                    </span>
                    <span id="liveClock" class="badge bg-light text-muted border ms-1">
                        <i class="bi bi-clock me-1"></i> <span id="clockTime"><?= date('H:i:s') ?> WIB</span>
                    </span>
                </div>

                <div class="ms-auto d-flex align-items-center gap-3">
                    <?php if ($currentUser['role'] === 'admin'): ?>
                        <a href="<?= BASE_URL ?>index.php?page=pos" class="btn btn-sm btn-outline-secondary d-none d-sm-inline-flex align-items-center gap-1">
                            <i class="bi bi-calculator"></i> Buka Kasir
                        </a>
                        <a href="<?= BASE_URL ?>index.php?page=apriori" class="btn btn-sm btn-dark text-white d-none d-sm-inline-flex align-items-center gap-1 shadow-sm">
                            <i class="bi bi-diagram-3"></i> Analisis Apriori
                        </a>
                    <?php endif; ?>

                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="me-2 small fw-semibold text-end d-none d-sm-inline-block">
                                <?= htmlspecialchars($currentUser['nama_lengkap']) ?>
                                <br><small class="text-muted fw-normal text-capitalize"><?= htmlspecialchars($currentUser['role']) ?></small>
                            </span>
                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <i class="bi bi-person-fill"></i>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" aria-labelledby="userMenu">
                            <li class="px-3 py-2 border-bottom">
                                <div class="fw-bold small"><?= htmlspecialchars($currentUser['nama_lengkap']) ?></div>
                                <div class="text-muted small">@<?= htmlspecialchars($currentUser['username']) ?></div>
                            </li>
                            <li>
                                <a class="dropdown-item py-2 text-danger" href="<?= BASE_URL ?>index.php?page=login&action=logout">
                                    <i class="bi bi-box-arrow-right me-2"></i> Keluar
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content Area -->
        <main class="container-fluid p-4 flex-grow-1">
            <?php require __DIR__ . '/alerts.php'; ?>

