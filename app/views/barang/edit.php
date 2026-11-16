<?php
$pageTitle = 'Edit Barang - ' . htmlspecialchars($barang['nama_barang']);
require __DIR__ . '/../layouts/header.php';
?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold text-dark mb-1">Edit Data Barang</h4>
                <p class="text-muted small mb-0">Perbarui detail produk, harga, dan stok barang sembako.</p>
            </div>
            <a href="<?= BASE_URL ?>index.php?page=barang" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Katalog
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form action="<?= BASE_URL ?>index.php?page=barang&action=update" method="POST">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label for="id_barang" class="form-label fw-semibold small">Kode Barang / Barcode</label>
                            <input type="text" class="form-control font-monospace bg-light" id="id_barang" name="id_barang" value="<?= htmlspecialchars($barang['id_barang']) ?>" readonly>
                            <div class="form-text">Kode barang merupakan Primary Key dan tidak dapat diubah.</div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="id_kategori" class="form-label fw-semibold small">Kategori Produk <span class="text-danger">*</span></label>
                            <select class="form-select" id="id_kategori" name="id_kategori" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php foreach ($kategoriList as $kat): ?>
                                    <option value="<?= $kat['id_kategori'] ?>" <?= $barang['id_kategori'] == $kat['id_kategori'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($kat['nama_kategori']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <label for="nama_barang" class="form-label fw-semibold small">Nama Produk Sembako <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_barang" name="nama_barang" value="<?= htmlspecialchars($barang['nama_barang']) ?>" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="harga_beli" class="form-label fw-semibold small">Harga Beli / Modal (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="harga_beli" name="harga_beli" min="0" step="500" value="<?= (int)$barang['harga_beli'] ?>" required>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="harga_jual" class="form-label fw-semibold small">Harga Jual Kasir (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="harga_jual" name="harga_jual" min="0" step="500" value="<?= (int)$barang['harga_jual'] ?>" required>
                            </div>
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="stok" class="form-label fw-semibold small">Stok Saat Ini <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="stok" name="stok" min="0" value="<?= $barang['stok'] ?>" required>
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="stok_minimum" class="form-label fw-semibold small">Stok Minimum Warning <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="stok_minimum" name="stok_minimum" min="1" value="<?= $barang['stok_minimum'] ?>" required>
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="satuan" class="form-label fw-semibold small">Satuan Barang <span class="text-danger">*</span></label>
                            <select class="form-select" id="satuan" name="satuan">
                                <?php foreach (['pcs', 'kg', 'sak', 'pouch', 'botol', 'bks', 'rtg', 'dus'] as $s): ?>
                                    <option value="<?= $s ?>" <?= $barang['satuan'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12 mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                            <a href="<?= BASE_URL ?>index.php?page=barang" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Perbarui Barang</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

