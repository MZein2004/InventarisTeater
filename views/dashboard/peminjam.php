<?php
$pageTitle    = 'Dashboard';
$pageSubtitle = 'Selamat datang, ' . ($_SESSION['nama'] ?? 'Raka') . '!';
$activeMenu   = 'dashboard';
$userStats    = $userStats ?? ['aktif' => 3, 'menunggu' => 1, 'riwayat' => 5];

require __DIR__ . '/../layouts/header.php';
?>

<section class="hero-banner">
    <div class="hero-content">
        <h2 class="hero-title">Temukan Kostum dan Properti untuk Kegiatan Teater Anda</h2>
        <a href="<?= $baseUrl ?>/index.php?page=katalog" class="btn btn-primary">Lihat Katalog</a>
    </div>
</section>

<section class="stats-grid stats-grid-3" aria-label="Statistik Peminjaman Saya">
    <article class="stat-card">
        <div class="stat-icon stat-icon-green" aria-hidden="true">&#128230;</div>
        <div class="stat-info">
            <span class="stat-value"><?= (int)$userStats['aktif'] ?></span>
            <span class="stat-label">Peminjaman Aktif</span>
        </div>
    </article>

    <article class="stat-card">
        <div class="stat-icon stat-icon-blue" aria-hidden="true">&#9203;</div>
        <div class="stat-info">
            <span class="stat-value"><?= (int)$userStats['menunggu'] ?></span>
            <span class="stat-label">Menunggu Persetujuan</span>
        </div>
    </article>

    <article class="stat-card">
        <div class="stat-icon stat-icon-purple" aria-hidden="true">&#128337;</div>
        <div class="stat-info">
            <span class="stat-value"><?= (int)$userStats['riwayat'] ?></span>
            <span class="stat-label">Riwayat Peminjaman</span>
        </div>
    </article>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>