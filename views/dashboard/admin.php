<?php
$pageTitle    = 'Dashboard';
$pageSubtitle = 'Selamat datang, ' . ($_SESSION['nama'] ?? 'Admin') . '!';
$activeMenu   = 'dashboard';
$stats        = $stats ?? ['total' => 120, 'tersedia' => 85, 'dipinjam' => 18, 'perhatian' => 17];
$recentLoans  = $recentLoans ?? [];

require __DIR__ . '/../layouts/header.php';
?>

<section class="stats-grid" aria-label="Ringkasan Inventaris">
    <article class="stat-card">
        <div class="stat-icon stat-icon-purple" aria-hidden="true">&#128230;</div>
        <div class="stat-info">
            <span class="stat-value"><?= (int)$stats['total'] ?></span>
            <span class="stat-label">Total Barang</span>
        </div>
    </article>

    <article class="stat-card">
        <div class="stat-icon stat-icon-green" aria-hidden="true">&#9989;</div>
        <div class="stat-info">
            <span class="stat-value"><?= (int)$stats['tersedia'] ?></span>
            <span class="stat-label">Tersedia</span>
        </div>
    </article>

    <article class="stat-card">
        <div class="stat-icon stat-icon-blue" aria-hidden="true">&#128203;</div>
        <div class="stat-info">
            <span class="stat-value"><?= (int)$stats['dipinjam'] ?></span>
            <span class="stat-label">Dipinjam</span>
        </div>
    </article>

    <article class="stat-card">
        <div class="stat-icon stat-icon-red" aria-hidden="true">&#9888;</div>
        <div class="stat-info">
            <span class="stat-value"><?= (int)$stats['perhatian'] ?></span>
            <span class="stat-label">Perlu Perhatian</span>
        </div>
    </article>
</section>

<section class="dashboard-grid">
    <article class="card chart-card">
        <div class="card-header">
            <h2 class="card-title">Grafik Peminjaman (6 bulan terakhir)</h2>
        </div>
        <div class="chart-container" id="chartPeminjaman">
            <!-- Placeholder bar chart CSS / Canvas -->
            <div class="bar-chart-placeholder">
                <div class="bar-item" data-month="Jan"><span>Jan</span></div>
                <div class="bar-item" data-month="Feb"><span>Feb</span></div>
                <div class="bar-item" data-month="Mar"><span>Mar</span></div>
                <div class="bar-item" data-month="Apr"><span>Apr</span></div>
                <div class="bar-item" data-month="Mei"><span>Mei</span></div>
                <div class="bar-item" data-month="Jun"><span>Jun</span></div>
            </div>
        </div>
    </article>

    <article class="card activity-card">
        <div class="card-header">
            <h2 class="card-title">Peminjaman Terbaru</h2>
            <a href="<?= $baseUrl ?>/index.php?page=peminjaman" class="card-link">Lihat semua</a>
        </div>
        <ul class="activity-list">
            <?php foreach ($recentLoans as $loan): ?>
                <li class="activity-item">
                    <div class="activity-details">
                        <strong class="activity-title"><?= htmlspecialchars($loan['nama_barang'], ENT_QUOTES, 'UTF-8') ?></strong>
                        <span class="activity-meta">Dipinjam oleh <?= htmlspecialchars($loan['nama_peminjam'], ENT_QUOTES, 'UTF-8') ?></span>
                        <span class="activity-date"><?= htmlspecialchars($loan['tgl_rencana_pinjam'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <span class="badge badge-status"><?= htmlspecialchars($loan['status_peminjaman'], ENT_QUOTES, 'UTF-8') ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </article>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>