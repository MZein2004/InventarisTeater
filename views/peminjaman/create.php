<?php
$pageTitle  = 'Ajukan Peminjaman';
$activeMenu = 'katalog';
$barang     = $barang ?? [
    'id_barang' => 'BRG00001',
    'nama_barang' => 'Baju Kerajaan',
    'nama_kategori' => 'Kostum',
    'jumlah_tersedia' => 5,
    'foto' => 'default.png'
];

require __DIR__ . '/../layouts/header.php';
?>

<section class="section-block">
    <a href="<?= $baseUrl ?>/index.php?page=katalog" class="back-link">&lsaquo; Kembali ke katalog</a>

    <div class="loan-layout">
        <aside class="card item-summary-card">
            <img src="<?= $baseUrl ?>/assets/uploads/<?= htmlspecialchars($barang['foto'] ?: 'default.png', ENT_QUOTES, 'UTF-8') ?>"
                alt="<?= htmlspecialchars($barang['nama_barang'], ENT_QUOTES, 'UTF-8') ?>"
                class="summary-img">
            <h2 class="summary-title"><?= htmlspecialchars($barang['nama_barang'], ENT_QUOTES, 'UTF-8') ?></h2>
            <p class="summary-meta">Kategori: <?= htmlspecialchars($barang['nama_kategori'], ENT_QUOTES, 'UTF-8') ?></p>
            <p class="summary-meta">Stok tersedia: <?= (int)$barang['jumlah_tersedia'] ?></p>
        </aside>

        <form action="<?= $baseUrl ?>/index.php?action=peminjaman-store" method="post" class="card form-card" id="formPeminjaman">
            <input type="hidden" name="id_barang" value="<?= htmlspecialchars($barang['id_barang'], ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-row">
                <label for="jml_dipinjam" class="form-label">Jumlah yang dipinjam</label>
                <input type="number" id="jml_dipinjam" name="jml_dipinjam" class="form-control"
                    min="1" max="<?= (int)$barang['jumlah_tersedia'] ?>" value="1" required>
            </div>

            <div class="form-row">
                <label for="tgl_rencana_pinjam" class="form-label">Tanggal Pinjam</label>
                <input type="date" id="tgl_rencana_pinjam" name="tgl_rencana_pinjam" class="form-control" required>
            </div>

            <div class="form-row">
                <label for="tgl_rencana_kembali" class="form-label">Tanggal Kembali</label>
                <input type="date" id="tgl_rencana_kembali" name="tgl_rencana_kembali" class="form-control" required>
            </div>

            <div class="form-row">
                <label for="keperluan" class="form-label">Keperluan</label>
                <textarea id="keperluan" name="keperluan" class="form-control" rows="3" placeholder="Contoh: Latihan pementasan drama." required></textarea>
            </div>

            <div class="form-actions">
                <a href="<?= $baseUrl ?>/index.php?page=katalog" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Ajukan Peminjaman</button>
            </div>
        </form>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>