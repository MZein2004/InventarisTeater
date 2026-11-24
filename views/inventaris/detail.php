<?php
$pageTitle  = 'Detail Barang';
$activeMenu = ($_SESSION['role'] ?? 'admin') === 'admin' ? 'inventaris' : 'katalog';
$barang     = $barang ?? [
    'id_barang' => 'BRG00001',
    'nama_barang' => 'Baju Kerajaan',
    'nama_kategori' => 'Kostum',
    'jumlah_stok' => 5,
    'jumlah_tersedia' => 5,
    'deskripsi' => 'Kostum kerajaan lengkap dengan jubah dan aksesoris.',
    'kondisi' => 'Baik',
    'foto' => 'default.png'
];

require __DIR__ . '/../layouts/header.php';
?>

<section class="section-block">
    <a href="javascript:history.back()" class="back-link">&lsaquo; Kembali ke daftar barang</a>

    <article class="card detail-card">
        <div class="detail-grid">
            <div class="detail-gallery">
                <img src="<?= $baseUrl ?>/assets/uploads/<?= htmlspecialchars($barang['foto'] ?: 'default.png', ENT_QUOTES, 'UTF-8') ?>"
                    alt="<?= htmlspecialchars($barang['nama_barang'], ENT_QUOTES, 'UTF-8') ?>"
                    class="detail-main-img">
            </div>

            <div class="detail-info">
                <div class="detail-header">
                    <h2 class="detail-title"><?= htmlspecialchars($barang['nama_barang'], ENT_QUOTES, 'UTF-8') ?></h2>
                    <span class="badge <?= (int)$barang['jumlah_tersedia'] > 0 ? 'badge-success' : 'badge-danger' ?>">
                        <?= (int)$barang['jumlah_tersedia'] > 0 ? 'Tersedia' : 'Habis' ?>
                    </span>
                </div>

                <dl class="spec-list">
                    <div class="spec-row">
                        <dt>Kategori</dt>
                        <dd>: <?= htmlspecialchars($barang['nama_kategori'], ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                    <div class="spec-row">
                        <dt>Jumlah Stok</dt>
                        <dd>: <?= (int)$barang['jumlah_stok'] ?> (Tersedia: <?= (int)$barang['jumlah_tersedia'] ?>)</dd>
                    </div>
                    <div class="spec-row">
                        <dt>Deskripsi</dt>
                        <dd>: <?= htmlspecialchars($barang['deskripsi'], ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                    <div class="spec-row">
                        <dt>Kondisi</dt>
                        <dd>: <?= htmlspecialchars($barang['kondisi'], ENT_QUOTES, 'UTF-8') ?></dd>
                    </div>
                </dl>

                <div class="detail-actions">
                    <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                        <a href="<?= $baseUrl ?>/index.php?page=inventaris-edit&id=<?= urlencode($barang['id_barang']) ?>"
                            class="btn btn-primary">Edit Barang</a>
                        <a href="<?= $baseUrl ?>/index.php?action=inventaris-delete&id=<?= urlencode($barang['id_barang']) ?>"
                            class="btn btn-outline-danger"
                            onclick="return confirm('Hapus barang ini?')">Hapus Barang</a>
                    <?php elseif ((int)$barang['jumlah_tersedia'] > 0): ?>
                        <a href="<?= $baseUrl ?>/index.php?page=peminjaman-create&id_barang=<?= urlencode($barang['id_barang']) ?>"
                            class="btn btn-primary">Ajukan Peminjaman</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </article>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>