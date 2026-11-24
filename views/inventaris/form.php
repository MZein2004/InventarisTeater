<!-- views/inventaris/form.php -->
<?php
$isEdit     = !empty($barang);
$pageTitle  = $isEdit ? 'Edit Barang' : 'Tambah Barang';
$activeMenu = 'inventaris';
$kategoris  = $kategoris ?? [];

require __DIR__ . '/../layouts/header.php';
?>

<section class="section-block">
    <a href="<?= $baseUrl ?>/index.php?page=inventaris" class="back-link">&lsaquo; Kembali ke inventaris</a>

    <form action="<?= $baseUrl ?>/index.php?action=<?= $isEdit ? 'inventaris-update' : 'inventaris-store' ?>"
        method="post" enctype="multipart/form-data" class="card form-card">
        <?php if ($isEdit): ?>
            <input type="hidden" name="id_barang" value="<?= htmlspecialchars($barang['id_barang'], ENT_QUOTES, 'UTF-8') ?>">
        <?php endif; ?>

        <div class="form-row">
            <label for="nama_barang" class="form-label">Nama Barang</label>
            <input type="text" id="nama_barang" name="nama_barang" class="form-control"
                value="<?= htmlspecialchars($barang['nama_barang'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
        </div>

        <div class="form-row">
            <label for="id_kategori" class="form-label">Kategori</label>
            <select id="id_kategori" name="id_kategori" class="form-control" required>
                <option value="">-- Pilih Kategori --</option>
                <?php foreach ($kategoris as $kat): ?>
                    <option value="<?= htmlspecialchars($kat['id_kategori'], ENT_QUOTES, 'UTF-8') ?>"
                        <?= ($barang['id_kategori'] ?? '') === $kat['id_kategori'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($kat['nama_kategori'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-row">
            <label for="jumlah_stok" class="form-label">Jumlah Stok</label>
            <input type="number" id="jumlah_stok" name="jumlah_stok" class="form-control" min="0"
                value="<?= (int)($barang['jumlah_stok'] ?? 1) ?>" required>
        </div>

        <?php if ($isEdit): ?>
            <div class="form-row">
                <label for="jumlah_tersedia" class="form-label">Jumlah Tersedia</label>
                <input type="number" id="jumlah_tersedia" name="jumlah_tersedia" class="form-control" min="0"
                    value="<?= (int)($barang['jumlah_tersedia'] ?? 0) ?>" required>
            </div>
        <?php endif; ?>

        <div class="form-row">
            <label for="kondisi" class="form-label">Kondisi</label>
            <input type="text" id="kondisi" name="kondisi" class="form-control"
                value="<?= htmlspecialchars($barang['kondisi'] ?? 'Baik', ENT_QUOTES, 'UTF-8') ?>" required>
        </div>

        <div class="form-row">
            <label for="deskripsi" class="form-label">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" class="form-control" rows="3" required><?= htmlspecialchars($barang['deskripsi'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <?php if (!$isEdit): ?>
            <div class="form-row">
                <label for="foto" class="form-label">Foto Barang</label>
                <input type="file" id="foto" name="foto" class="form-control" accept="image/*">
            </div>
        <?php endif; ?>

        <div class="form-actions">
            <a href="<?= $baseUrl ?>/index.php?page=inventaris" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>