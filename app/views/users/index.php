<?php
$pageTitle = 'Manajemen Pengguna';
require __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Manajemen Pengguna & Kasir</h4>
        <p class="text-muted small mb-0">Kelola akun staf, hak akses Admin dan Kasir toko sembako.</p>
    </div>
    <a href="<?= BASE_URL ?>index.php?page=users&action=create" class="btn btn-success d-flex align-items-center gap-2 shadow-sm">
        <i class="bi bi-person-plus-fill"></i> Tambah Pengguna Baru
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;" class="text-center">#</th>
                        <th>Nama Lengkap</th>
                        <th>Username</th>
                        <th class="text-center">Hak Akses (Role)</th>
                        <th>Waktu Dibuat</th>
                        <th style="width: 140px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usersList as $i => $u): ?>
                        <tr>
                            <td class="text-center text-muted"><?= $i + 1 ?></td>
                            <td>
                                <div class="fw-bold text-dark"><?= htmlspecialchars($u['nama_lengkap']) ?></div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border font-monospace">@<?= htmlspecialchars($u['username']) ?></span>
                            </td>
                            <td class="text-center">
                                <?php if ($u['role'] === 'admin'): ?>
                                    <span class="badge bg-danger text-uppercase px-2 py-1"><i class="bi bi-shield-lock me-1"></i> Admin</span>
                                <?php else: ?>
                                    <span class="badge bg-success text-uppercase px-2 py-1"><i class="bi bi-cart-check me-1"></i> Kasir</span>
                                <?php endif; ?>
                            </td>
                            <td class="small text-muted"><?= date('d/m/Y H:i', strtotime($u['created_at'])) ?></td>
                            <td class="text-center">
                                <a href="<?= BASE_URL ?>index.php?page=users&action=edit&id=<?= $u['id_user'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <?php if ($u['id_user'] != $currentUser['id_user']): ?>
                                    <a href="<?= BASE_URL ?>index.php?page=users&action=delete&id=<?= $u['id_user'] ?>" class="btn btn-sm btn-outline-danger" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus akun <?= htmlspecialchars(addslashes($u['nama_lengkap'])) ?>?');">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

