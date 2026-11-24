<?php
$pageTitle    = 'Data Inventaris';
$pageSubtitle = 'Kelola data kostum dan properti teater.';
$activeMenu   = 'inventaris';
$items        = $items ?? [];
$kategoris    = $kategoris ?? [];

require __DIR__ . '/../layouts/header.php';
?>

<section class="section-block">
    <div class="section-actions">
        <a href="<?= $baseUrl ?>/index.php?page=kategori" class="btn btn-secondary">Kelola Kategori</a>
        <a href="<?= $baseUrl ?>/index.php?page=inventaris-create" class="btn btn-primary">+ Tambah Barang</a>
    </div>

    <div class="card table-card">
        <form method="get" action="<?= $baseUrl ?>/index.php" class="filter-bar">
            <input type="hidden" name="page" value="inventaris">
            <div class="filter-search">
                <label for="searchBarang" class="sr-only">Cari nama barang</label>
                <input type="search" id="searchBarang" name="q" class="form-control"
                    value="<?= htmlspecialchars($_GET['q'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="Cari nama barang...">
            </div>
            <div class="filter-select">
                <label for="filterKategori" class="sr-only">Kategori</label>
                <select id="filterKategori" name="kategori" class="form-control">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($kategoris as $kat): ?>
                        <option value="<?= htmlspecialchars($kat['id_kategori'], ENT_QUOTES, 'UTF-8') ?>"
                            <?= (($_GET['kategori'] ?? '') === $kat['id_kategori']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($kat['nama_kategori'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-select">
                <label for="filterStatus" class="sr-only">Status</label>
                <select id="filterStatus" name="status" class="form-control">
                    <option value="">Semua Status</option>
                    <option value="tersedia" <?= (($_GET['status'] ?? '') === 'tersedia') ? 'selected' : '' ?>>Tersedia</option>
                    <option value="dipinjam" <?= (($_GET['status'] ?? '') === 'dipinjam') ? 'selected' : '' ?>>Dipinjam</option>
                    <option value="perbaikan" <?= (($_GET['status'] ?? '') === 'perbaikan') ? 'selected' : '' ?>>Dalam Perbaikan</option>
                </select>
            </div>
            <button type="submit" class="btn btn-secondary">Filter</button>
        </form>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Foto</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Stok</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="tabelInventarisBody">
                    <?php $no = 1;
                    foreach ($items as $item): ?>
                        <?php
                        $tersedia       = (int)($item['jumlah_tersedia'] ?? 0);
                        $dipinjam       = (int)($item['jumlah_dipinjam'] ?? 0);
                        $dalamPerbaikan = (($item['kondisi'] ?? '') === 'Dalam Perbaikan');
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <img src="<?= $baseUrl ?>/assets/uploads/<?= htmlspecialchars($item['foto'] ?: 'default.png', ENT_QUOTES, 'UTF-8') ?>"
                                    alt="<?= htmlspecialchars($item['nama_barang'], ENT_QUOTES, 'UTF-8') ?>"
                                    class="table-thumb">
                            </td>
                            <td><?= htmlspecialchars($item['nama_barang'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($item['nama_kategori'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= $tersedia ?> / <?= (int)$item['jumlah_stok'] ?></td>
                            <td>
                                <?php if ($dalamPerbaikan): ?>
                                    <span class="badge badge-danger">Dalam Perbaikan</span>
                                <?php elseif ($dipinjam > 0): ?>
                                    <span class="badge badge-warning">Dipinjam (<?= $dipinjam ?>)</span>
                                <?php elseif ($tersedia > 0): ?>
                                    <span class="badge badge-success">Tersedia</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Stok Habis</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-group">
                                    <a href="<?= $baseUrl ?>/index.php?page=inventaris-detail&id=<?= urlencode($item['id_barang']) ?>"
                                        class="btn-icon btn-icon-info" title="Detail">Detail</a>
                                    <a href="<?= $baseUrl ?>/index.php?page=inventaris-edit&id=<?= urlencode($item['id_barang']) ?>"
                                        class="btn-icon btn-icon-primary" title="Edit">Edit</a>
                                    <a href="<?= $baseUrl ?>/index.php?action=inventaris-delete&id=<?= urlencode($item['id_barang']) ?>"
                                        class="btn-icon btn-icon-danger" title="Hapus"
                                        onclick="return confirm('Hapus barang ini?')">Hapus</a>
                                    <?php if ($dalamPerbaikan): ?>
                                        <form method="post" action="<?= $baseUrl ?>/index.php?action=inventaris-perbaikan" style="display:inline-flex; gap:4px; align-items:center;">
                                            <input type="hidden" name="id_barang" value="<?= htmlspecialchars($item['id_barang'], ENT_QUOTES, 'UTF-8') ?>">
                                            <input type="number" name="jumlah_pulih" value="1" min="1" max="<?= max(1, (int)$item['jumlah_stok']) ?>" style="width:52px; padding:2px 4px;" title="Jumlah unit selesai diperbaiki">
                                            <button type="submit" class="btn-icon btn-icon-primary">Selesai Perbaikan</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>