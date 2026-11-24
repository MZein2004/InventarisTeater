<?php
$pageTitle    = 'Laporan';
$pageSubtitle = 'Lihat dan unduh laporan inventaris dan peminjaman.';
$activeMenu   = 'laporan';
$reports      = $reports ?? [];

require __DIR__ . '/../layouts/header.php';
?>

<section class="section-block">
    <div class="toolbar-between">
        <nav class="underline-tabs" aria-label="Jenis Laporan">
            <a href="<?= $baseUrl ?>/index.php?page=laporan&tab=inventaris" class="underline-tab is-active">Inventaris</a>
            <a href="<?= $baseUrl ?>/index.php?page=laporan&tab=peminjaman" class="underline-tab">Peminjaman</a>
        </nav>

        <button type="button" class="btn btn-primary" onclick="window.print()">Unduh Laporan</button>
    </div>

    <div class="card table-card">
        <form method="get" action="<?= $baseUrl ?>/index.php" class="filter-bar">
            <input type="hidden" name="page" value="laporan">
            <div class="filter-date">
                <label for="tgl_mulai" class="sr-only">Dari Tanggal</label>
                <input type="date" id="tgl_mulai" name="mulai" class="form-control">
            </div>
            <div class="filter-date">
                <label for="tgl_selesai" class="sr-only">Sampai Tanggal</label>
                <input type="date" id="tgl_selesai" name="selesai" class="form-control">
            </div>
            <button type="submit" class="btn btn-secondary">Filter</button>
        </form>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Total Peminjaman</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($reports as $rep): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($rep['nama_barang'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($rep['nama_kategori'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= (int)$rep['total_peminjaman'] ?></td>
                            <td><span class="badge badge-success"><?= htmlspecialchars($rep['status'] ?? 'Aktif', ENT_QUOTES, 'UTF-8') ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>