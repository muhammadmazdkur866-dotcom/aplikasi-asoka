<?php
$pageTitle = 'Edit Pengguna - ' . htmlspecialchars($user['nama_lengkap']);
require __DIR__ . '/../layouts/header.php';
?>

<div class="row justify-content-center">
    <div class="col-12 col-md-7 col-lg-6">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold text-dark mb-1">Edit Pengguna</h4>
                <p class="text-muted small mb-0">Perbarui data profil dan hak akses pengguna.</p>
            </div>
            <a href="<?= BASE_URL ?>index.php?page=users" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form action="<?= BASE_URL ?>index.php?page=users&action=update" method="POST">
                    <input type="hidden" name="id_user" value="<?= $user['id_user'] ?>">

                    <div class="mb-3">
                        <label for="nama_lengkap" class="form-label fw-semibold small">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="<?= htmlspecialchars($user['nama_lengkap']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="username" class="form-label fw-semibold small">Username <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold small">Ganti Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah password">
                        <div class="form-text">Biarkan kosong bila password tidak diganti.</div>
                    </div>

                    <div class="mb-4">
                        <label for="role" class="form-label fw-semibold small">Hak Akses (Role) <span class="text-danger">*</span></label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="kasir" <?= $user['role'] === 'kasir' ? 'selected' : '' ?>>Kasir (Hanya akses Point of Sale & Riwayat Transaksi)</option>
                            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin (Akses penuh ke semua master data, Apriori, dan laporan)</option>
                        </select>
                    </div>

                    <div class="pt-3 border-top d-flex justify-content-end gap-2">
                        <a href="<?= BASE_URL ?>index.php?page=users" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Perbarui Pengguna</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

