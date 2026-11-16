<?php
$pageTitle = 'Master Kategori Barang';
require __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Kategori Produk Sembako</h4>
        <p class="text-muted small mb-0">Kelola kelompok produk (misal: Sembako Utama, Bumbu Dapur, Minuman, Kebersihan).</p>
    </div>
    <button type="button" class="btn btn-success d-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahKategori">
        <i class="bi bi-plus-lg"></i> Tambah Kategori
    </button>
</div>

<div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-4">
    <i class="bi bi-info-circle-fill fs-5 text-primary me-2"></i>
    <div class="small">
        Kolom <strong>Jumlah Produk Terdaftar</strong> dihitung otomatis oleh sistem. Untuk menambah produk ke dalam kategori tertentu, klik tombol <strong>+ Tambah Produk</strong> di sebelah kanan atau buka menu <strong>Data Barang Sembako</strong>.
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 70px;" class="text-center">#</th>
                        <th>Nama Kategori</th>
                        <th class="text-center">Jumlah Produk Terdaftar</th>
                        <th style="width: 260px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($kategoriList)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">Belum ada data kategori.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($kategoriList as $i => $kat): ?>
                            <tr>
                                <td class="text-center text-muted"><?= $i + 1 ?></td>
                                <td>
                                    <span class="fw-semibold text-dark"><?= htmlspecialchars($kat['nama_kategori']) ?></span>
                                </td>
                                <td class="text-center">
                                    <a href="<?= BASE_URL ?>index.php?page=barang&kategori=<?= $kat['id_kategori'] ?>" 
                                       class="badge bg-soft-info text-primary border text-decoration-none fw-semibold px-2 py-1" 
                                       title="Klik untuk melihat daftar produk kategori ini">
                                        <?= $kat['total_barang'] ?> Barang <i class="bi bi-arrow-right-short"></i>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <a href="<?= BASE_URL ?>index.php?page=barang&action=create&kategori=<?= $kat['id_kategori'] ?>" 
                                       class="btn btn-sm btn-outline-success me-1" 
                                       title="Tambah Produk Baru ke Kategori ini">
                                        <i class="bi bi-plus-circle me-1"></i> + Produk
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-primary me-1" 
                                            onclick="editKategori(<?= $kat['id_kategori'] ?>, '<?= htmlspecialchars(addslashes($kat['nama_kategori'])) ?>')">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>
                                    <a href="<?= BASE_URL ?>index.php?page=kategori&action=delete&id=<?= $kat['id_kategori'] ?>" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Kategori -->
<div class="modal fade" id="modalTambahKategori" tabindex="-1" aria-labelledby="modalTambahLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= BASE_URL ?>index.php?page=kategori&action=store" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalTambahLabel">Tambah Kategori Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nama_kategori" class="form-label fw-semibold small">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nama_kategori" name="nama_kategori" placeholder="Contoh: Bumbu & Bahan Dapur" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-save me-1"></i> Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Kategori -->
<div class="modal fade" id="modalEditKategori" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= BASE_URL ?>index.php?page=kategori&action=update" method="POST">
                <input type="hidden" name="id_kategori" id="edit_id_kategori">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalEditLabel">Edit Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_nama_kategori" class="form-label fw-semibold small">Nama Kategori <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_nama_kategori" name="nama_kategori" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Update Kategori</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function editKategori(id, nama) {
        document.getElementById('edit_id_kategori').value = id;
        document.getElementById('edit_nama_kategori').value = nama;
        const modal = new bootstrap.Modal(document.getElementById('modalEditKategori'));
        modal.show();
    }
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

