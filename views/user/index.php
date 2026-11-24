<?php
$pageTitle    = 'Data Pengguna';
$pageSubtitle = 'Kelola akun admin dan peminjam.';
$activeMenu   = 'pengguna';
$users        = $users ?? [];

require __DIR__ . '/../layouts/header.php';
?>

<section class="section-block">
    <div class="section-actions">
        <a href="<?= $baseUrl ?>/index.php?page=user-create" class="btn btn-primary">+ Tambah Pengguna</a>
    </div>

    <div class="card table-card">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>No. Identitas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($users as $u): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($u['nama'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars(ucfirst($u['role']), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($u['no_id'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <div class="action-group">
                                    <a href="<?= $baseUrl ?>/index.php?page=user-edit&id=<?= urlencode($u['id_pengguna']) ?>"
                                        class="btn-icon btn-icon-primary">Edit</a>
                                    <a href="<?= $baseUrl ?>/index.php?action=user-delete&id=<?= urlencode($u['id_pengguna']) ?>"
                                        class="btn-icon btn-icon-danger"
                                        onclick="return confirm('Hapus pengguna ini?')">Hapus</a>
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