<?php
$pageTitle    = 'Kelola Kategori Barang';
$pageSubtitle = 'Tambah, ubah, atau hapus kategori kostum dan properti teater.';
$activeMenu   = 'inventaris';
$kategoris    = $kategoris ?? [];
$errorMsg     = $errorMsg ?? '';

require __DIR__ . '/../layouts/header.php';
?>

<section class="section-block">
    <?php if ($errorMsg !== ''): ?>
        <div class="alert alert-danger" style="margin-bottom: 1rem;">
            <?= htmlspecialchars($errorMsg, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <div class="card" style="padding: 1.25rem; margin-bottom: 1.5rem;">
        <form method="post" action="<?= $baseUrl ?>/index.php?action=kategori-store" style="display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center;">
            <input type="text" name="nama_kategori" class="form-control" style="flex: 1; min-width: 180px;" placeholder="Nama kategori (mis. Pakaian)" required>
            <input type="text" name="deskripsi" class="form-control" style="flex: 2; min-width: 220px;" placeholder="Deskripsi singkat kategori..." required>
            <button type="submit" class="btn btn-primary">Tambah Kategori</button>
            <a href="<?= $baseUrl ?>/index.php?page=inventaris" class="btn btn-secondary">Kembali ke Inventaris</a>
        </form>
    </div>

    <div class="card table-card">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama & Deskripsi Kategori</th>
                        <th>Jumlah Barang</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($kategoris as $kat): ?>
                        <tr>
                            <td><?= htmlspecialchars($kat['id_kategori'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <form method="post" action="<?= $baseUrl ?>/index.php?action=kategori-update" style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                                    <input type="hidden" name="id_kategori" value="<?= htmlspecialchars($kat['id_kategori'], ENT_QUOTES, 'UTF-8') ?>">
                                    <input type="text" name="nama_kategori" class="form-control" style="flex: 1; min-width: 140px;" value="<?= htmlspecialchars($kat['nama_kategori'], ENT_QUOTES, 'UTF-8') ?>" required>
                                    <input type="text" name="deskripsi" class="form-control" style="flex: 2; min-width: 180px;" value="<?= htmlspecialchars((string)($kat['deskripsi'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Deskripsi kategori" required>
                                    <button type="submit" class="btn btn-secondary">Simpan</button>
                                </form>
                            </td>
                            <td><?= (int)($kat['jumlah_barang'] ?? 0) ?> item</td>
                            <td>
                                <a href="<?= $baseUrl ?>/index.php?action=kategori-delete&id=<?= urlencode($kat['id_kategori']) ?>"
                                    class="btn btn-danger"
                                    onclick="return confirm('Hapus kategori ini?');">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>