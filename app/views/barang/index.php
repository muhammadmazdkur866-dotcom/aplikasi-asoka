<?php
$pageTitle = 'Master Data Barang Sembako';
require __DIR__ . '/../layouts/header.php';
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h4 class="fw-bold text-dark mb-1">Master Data Barang Sembako</h4>
        <p class="text-muted small mb-0">Kelola katalog produk sembako, barcode, harga beli, harga jual, dan stok.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= BASE_URL ?>index.php?page=barang&action=create" class="btn btn-success d-flex align-items-center gap-2 shadow-sm">
            <i class="bi bi-plus-lg"></i> Tambah Barang Baru
        </a>
    </div>
</div>

<!-- Filter Card -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?= BASE_URL ?>index.php" class="row g-3">
            <input type="hidden" name="page" value="barang">
            
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" class="form-control" placeholder="Cari nama barang atau kode barcode..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                </div>
            </div>

            <div class="col-12 col-md-4">
                <select name="kategori" class="form-select">
                    <option value="">-- Semua Kategori --</option>
                    <?php foreach ($kategoriList as $kat): ?>
                        <option value="<?= $kat['id_kategori'] ?>" <?= (isset($_GET['kategori']) && $_GET['kategori'] == $kat['id_kategori']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($kat['nama_kategori']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-12 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-funnel me-1"></i> Filter</button>
                <a href="<?= BASE_URL ?>index.php?page=barang" class="btn btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i> Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Table Card -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;" class="text-center">#</th>
                        <th>Kode / Barcode</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th class="text-end">Harga Beli</th>
                        <th class="text-end">Harga Jual</th>
                        <th class="text-center">Stok Saat Ini</th>
                        <th class="text-center">Status</th>
                        <th style="width: 130px;" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($barangList)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                Tidak ada data barang yang sesuai dengan pencarian atau filter.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($barangList as $i => $b): ?>
                            <tr>
                                <td class="text-center text-muted"><?= $i + 1 ?></td>
                                <td>
                                    <span class="badge bg-light text-dark border font-monospace"><?= htmlspecialchars($b['id_barang']) ?></span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($b['nama_barang']) ?></div>
                                    <small class="text-muted">Satuan: <?= htmlspecialchars($b['satuan']) ?> | Min. Stok: <?= $b['stok_minimum'] ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-soft-info text-primary border"><?= htmlspecialchars($b['nama_kategori']) ?></span>
                                </td>
                                <td class="text-end text-muted"><?= format_rupiah($b['harga_beli']) ?></td>
                                <td class="text-end fw-bold text-success"><?= format_rupiah($b['harga_jual']) ?></td>
                                <td class="text-center">
                                    <span class="fw-bold fs-6 <?= $b['stok'] <= $b['stok_minimum'] ? 'text-danger' : 'text-dark' ?>">
                                        <?= $b['stok'] ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if ($b['stok'] <= 0): ?>
                                        <span class="badge bg-danger">Habis</span>
                                    <?php elseif ($b['stok'] <= $b['stok_minimum']): ?>
                                        <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle me-1"></i>Menipis</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Aman</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?= BASE_URL ?>index.php?page=barang&action=edit&id=<?= urlencode($b['id_barang']) ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="<?= BASE_URL ?>index.php?page=barang&action=delete&id=<?= urlencode($b['id_barang']) ?>" class="btn btn-sm btn-outline-danger" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus barang <?= htmlspecialchars(addslashes($b['nama_barang'])) ?>?');">
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

<?php require __DIR__ . '/../layouts/footer.php'; ?>

