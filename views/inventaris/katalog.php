<?php
$pageTitle      = 'Katalog Barang';
$pageSubtitle   = 'Cari dan ajukan peminjaman barang yang tersedia.';
$activeMenu     = 'katalog';
$items          = $items ?? [];
$kategoris      = $kategoris ?? [];
$saranPencarian = $saranPencarian ?? null;

require __DIR__ . '/../layouts/header.php';
?>

<section class="section-block">
    <form method="get" action="<?= $baseUrl ?>/index.php" class="filter-bar card">
        <input type="hidden" name="page" value="katalog">
        <div class="filter-search">
            <label for="searchKatalog" class="sr-only">Cari barang</label>
            <input type="search" id="searchKatalog" name="q" class="form-control"
                value="<?= htmlspecialchars($_GET['q'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                placeholder="Cari nama barang...">
        </div>
        <div class="filter-select">
            <label for="katalogKategori" class="sr-only">Kategori</label>
            <select id="katalogKategori" name="kategori" class="form-control">
                <option value="">Semua Kategori</option>
                <?php foreach ($kategoris as $kat): ?>
                    <option value="<?= htmlspecialchars($kat['id_kategori'], ENT_QUOTES, 'UTF-8') ?>"
                        <?= (($_GET['kategori'] ?? '') === $kat['id_kategori']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($kat['nama_kategori'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-secondary">Cari</button>
    </form>

    <?php if (empty($items)): ?>
        <div class="card" style="padding: 1.5rem; text-align: center;">
            <?php if (!empty($saranPencarian)): ?>
                <p style="margin-bottom: 0.5rem;">
                    Mungkin maksud Anda:
                    <a href="<?= $baseUrl ?>/index.php?page=katalog&q=<?= urlencode($saranPencarian) ?>">
                        <strong><?= htmlspecialchars($saranPencarian, ENT_QUOTES, 'UTF-8') ?></strong>
                    </a>?
                </p>
            <?php endif; ?>
            <p class="text-muted">Belum ada barang yang ditemukan.</p>
        </div>
    <?php else: ?>
        <div class="catalog-grid" id="catalogGrid">
            <?php foreach ($items as $item): ?>
                <?php $tersedia = (int)($item['jumlah_tersedia'] ?? 0); ?>
                <article class="card product-card">
                    <a href="<?= $baseUrl ?>/index.php?page=inventaris-detail&id=<?= urlencode($item['id_barang']) ?>" class="product-thumb-link">
                        <img src="<?= $baseUrl ?>/assets/uploads/<?= htmlspecialchars($item['foto'] ?: 'default.png', ENT_QUOTES, 'UTF-8') ?>"
                            alt="<?= htmlspecialchars($item['nama_barang'], ENT_QUOTES, 'UTF-8') ?>"
                            class="product-img">
                    </a>
                    <div class="product-body">
                        <h3 class="product-title"><?= htmlspecialchars($item['nama_barang'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <p class="product-meta"><?= htmlspecialchars($item['nama_kategori'], ENT_QUOTES, 'UTF-8') ?></p>
                        <p class="product-stock">Stok: <?= $tersedia ?></p>
                        <?php if ($tersedia > 0): ?>
                            <a href="<?= $baseUrl ?>/index.php?page=peminjaman-create&id_barang=<?= urlencode($item['id_barang']) ?>"
                                class="btn btn-primary btn-block">Ajukan Peminjaman</a>
                        <?php else: ?>
                            <button type="button" class="btn btn-disabled btn-block" disabled>Stok Habis</button>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>