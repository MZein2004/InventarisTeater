<?php
$isAdmin      = ($_SESSION['role'] ?? 'admin') === 'admin';
$pageTitle    = $isAdmin ? 'Data Peminjaman' : 'Peminjaman Saya';
$pageSubtitle = $isAdmin ? 'Verifikasi dan kelola permohonan peminjaman.' : 'Daftar pengajuan dan status peminjaman Anda.';
$activeMenu   = $isAdmin ? 'peminjaman' : 'peminjaman-saya';
$activeStatus = $activeStatus ?? '';
$loans        = $loans ?? [];

require __DIR__ . '/../layouts/header.php';
?>

<section class="section-block">
    <nav class="status-tabs" aria-label="Filter Status Peminjaman">
        <a href="<?= $baseUrl ?>/index.php?page=<?= $activeMenu ?>&status=Menunggu"
            class="tab-pill <?= $activeStatus === 'Menunggu' ? 'is-active' : '' ?>">Menunggu</a>
        <a href="<?= $baseUrl ?>/index.php?page=<?= $activeMenu ?>&status=Disetujui"
            class="tab-pill <?= $activeStatus === 'Disetujui' ? 'is-active' : '' ?>">Disetujui</a>
        <a href="<?= $baseUrl ?>/index.php?page=<?= $activeMenu ?>&status=Dipinjam"
            class="tab-pill <?= $activeStatus === 'Dipinjam' ? 'is-active' : '' ?>">Dipinjam</a>
        <a href="<?= $baseUrl ?>/index.php?page=<?= $activeMenu ?>&status=Selesai"
            class="tab-pill <?= $activeStatus === 'Selesai' ? 'is-active' : '' ?>">Selesai</a>
        <a href="<?= $baseUrl ?>/index.php?page=<?= $activeMenu ?>&status=Ditolak"
            class="tab-pill <?= $activeStatus === 'Ditolak' ? 'is-active' : '' ?>">Ditolak</a>
        <a href="<?= $baseUrl ?>/index.php?page=<?= $activeMenu ?>"
            class="tab-pill <?= $activeStatus === '' ? 'is-active' : '' ?>">Semua</a>
    </nav>

    <div class="card table-card">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <?php if ($isAdmin): ?>
                            <th>Nama Peminjam</th>
                        <?php endif; ?>
                        <th>Barang</th>
                        <th>Jumlah</th>
                        <th>Tanggal Pinjam</th>
                        <th>Tanggal Kembali</th>
                        <th>Status</th>
                        <?php if ($isAdmin): ?>
                            <th>Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($loans as $row): ?>
                        <?php $status = $row['status_peminjaman'] ?? ''; ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <?php if ($isAdmin): ?>
                                <td><?= htmlspecialchars($row['nama_peminjam'], ENT_QUOTES, 'UTF-8') ?></td>
                            <?php endif; ?>
                            <td><?= htmlspecialchars($row['nama_barang'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= (int)$row['jml_dipinjam'] ?></td>
                            <td><?= htmlspecialchars($row['tgl_rencana_pinjam'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['tgl_rencana_kembali'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <span class="badge badge-status"><?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?></span>
                            </td>
                            <?php if ($isAdmin): ?>
                                <td>
                                    <div class="action-group">
                                        <?php if ($status === 'Menunggu'): ?>
                                            <form method="post" action="<?= $baseUrl ?>/index.php?action=peminjaman-verifikasi" style="display:inline;">
                                                <input type="hidden" name="id_peminjaman" value="<?= htmlspecialchars($row['id_peminjaman'], ENT_QUOTES, 'UTF-8') ?>">
                                                <input type="hidden" name="aksi" value="Disetujui">
                                                <button type="submit" class="btn-icon btn-icon-primary">Setujui</button>
                                            </form>
                                            <form method="post" action="<?= $baseUrl ?>/index.php?action=peminjaman-verifikasi" style="display:inline;">
                                                <input type="hidden" name="id_peminjaman" value="<?= htmlspecialchars($row['id_peminjaman'], ENT_QUOTES, 'UTF-8') ?>">
                                                <input type="hidden" name="aksi" value="Ditolak">
                                                <button type="submit" class="btn-icon btn-icon-danger" onclick="return confirm('Tolak permohonan ini?')">Tolak</button>
                                            </form>
                                        <?php elseif ($status === 'Disetujui'): ?>
                                            <form method="post" action="<?= $baseUrl ?>/index.php?action=peminjaman-serahkan" style="display:inline;">
                                                <input type="hidden" name="id_peminjaman" value="<?= htmlspecialchars($row['id_peminjaman'], ENT_QUOTES, 'UTF-8') ?>">
                                                <button type="submit" class="btn-icon btn-icon-primary">Serahkan Barang</button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>