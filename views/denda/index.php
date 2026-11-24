<?php
$isAdmin      = ($_SESSION['role'] ?? '') === 'admin';
$pageTitle    = $isAdmin ? 'Manajemen Denda' : 'Denda Saya';
$pageSubtitle = $isAdmin ? 'Pantau tagihan denda keterlambatan/kerusakan dan konfirmasi pelunasan.' : 'Daftar rincian denda terkait peminjaman Anda.';
$activeMenu   = 'denda';
$fines        = $fines ?? [];
$ringkasan    = $ringkasan ?? ['total_belum' => 0, 'total_lunas' => 0, 'kasus_aktif' => 0];
$activeStatus = $activeStatus ?? '';
$items        = $items ?? [];

require __DIR__ . '/../layouts/header.php';
?>

<section class="section-block">
    <?php if ($isAdmin): ?>
        <div class="stats-grid" style="margin-bottom: 1.5rem;">
            <article class="card stat-card">
                <span class="stat-label">Kasus Belum Dibayar</span>
                <strong class="stat-value"><?= (int)$ringkasan['kasus_aktif'] ?></strong>
            </article>
            <article class="card stat-card">
                <span class="stat-label">Total Tagihan Belum Lunas</span>
                <strong class="stat-value">Rp <?= number_format((float)$ringkasan['total_belum'], 0, ',', '.') ?></strong>
            </article>
            <article class="card stat-card">
                <span class="stat-label">Total Denda Terkumpul</span>
                <strong class="stat-value">Rp <?= number_format((float)$ringkasan['total_lunas'], 0, ',', '.') ?></strong>
            </article>
        </div>

        <div class="card" style="padding: 1.25rem; margin-bottom: 1.5rem;">
            <h3 style="margin-bottom: 0.75rem;">Catat Denda Manual</h3>
            <form method="post" action="<?= $baseUrl ?>/index.php?action=denda-store" style="display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: center;">
                <select name="id_barang" class="form-control" style="flex: 1; min-width: 200px;" required>
                    <option value="">-- Pilih Barang --</option>
                    <?php foreach ($items as $item): ?>
                        <option value="<?= htmlspecialchars($item['id_barang'], ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($item['nama_barang'] . ' (' . $item['id_barang'] . ')', ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="alasan" class="form-control" style="flex: 2; min-width: 220px;" placeholder="Alasan & nama peminjam (mis. Kostum sobek - Budi)" required>
                <input type="number" name="jml_biaya" class="form-control" style="width: 160px;" min="1000" step="500" placeholder="Nominal (Rp)" required>
                <button type="submit" class="btn btn-primary">Simpan Denda</button>
            </form>
        </div>
    <?php endif; ?>

    <nav class="status-tabs" aria-label="Filter Status Denda">
        <a href="<?= $baseUrl ?>/index.php?page=denda&status=<?= urlencode('Belum dibayar') ?>"
            class="tab-pill <?= $activeStatus === 'Belum dibayar' ? 'is-active' : '' ?>">Belum Dibayar</a>
        <a href="<?= $baseUrl ?>/index.php?page=denda&status=<?= urlencode('Sudah dibayar') ?>"
            class="tab-pill <?= $activeStatus === 'Sudah dibayar' ? 'is-active' : '' ?>">Sudah Dibayar</a>
        <a href="<?= $baseUrl ?>/index.php?page=denda"
            class="tab-pill <?= $activeStatus === '' ? 'is-active' : '' ?>">Semua</a>
    </nav>

    <div class="card table-card">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID Denda</th>
                        <th>Nama Barang</th>
                        <th>Keterangan / Alasan</th>
                        <th>Jumlah Biaya</th>
                        <th>Status Pembayaran</th>
                        <?php if ($isAdmin): ?>
                            <th>Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($fines)): ?>
                        <tr>
                            <td colspan="<?= $isAdmin ? 6 : 5 ?>" style="text-align: center;">Tidak ada data denda.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($fines as $fine): ?>
                            <?php $belumLunas = ($fine['status_pembayaran'] === 'Belum dibayar'); ?>
                            <tr>
                                <td><?= htmlspecialchars($fine['id_denda'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($fine['nama_barang'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($fine['alasan'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>Rp <?= number_format((float)$fine['jml_biaya'], 0, ',', '.') ?></td>
                                <td>
                                    <span class="badge <?= $belumLunas ? 'badge-danger' : 'badge-success' ?>">
                                        <?= htmlspecialchars($fine['status_pembayaran'], ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <?php if ($isAdmin): ?>
                                    <td>
                                        <?php if ($belumLunas): ?>
                                            <form method="post" action="<?= $baseUrl ?>/index.php?action=denda-lunasi" style="display:inline;">
                                                <input type="hidden" name="id_denda" value="<?= htmlspecialchars($fine['id_denda'], ENT_QUOTES, 'UTF-8') ?>">
                                                <button type="submit" class="btn-icon btn-icon-primary" onclick="return confirm('Tandai denda ini sudah dibayar?')">Tandai Lunas</button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-muted">Lunas</span>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>