<!-- views/user/form.php -->
<?php
/** @var string $baseUrl */
/** @var array|null $user */

$isEdit     = isset($user) && is_array($user);
$pageTitle  = $isEdit ? 'Edit Pengguna' : 'Tambah Pengguna';
$activeMenu = 'pengguna';

require __DIR__ . '/../layouts/header.php';
?>

<section class="section-block">
    <a href="<?= $baseUrl ?>/index.php?page=pengguna" class="back-link">&lsaquo; Kembali ke data pengguna</a>

    <form action="<?= $baseUrl ?>/index.php?action=<?= $isEdit ? 'user-update' : 'user-store' ?>"
        method="post" class="card form-card">
        <?php if ($isEdit): ?>
            <input type="hidden" name="id_pengguna" value="<?= htmlspecialchars($user['id_pengguna'], ENT_QUOTES, 'UTF-8') ?>">
        <?php endif; ?>

        <div class="form-row">
            <label for="nama" class="form-label">Nama Lengkap</label>
            <input type="text" id="nama" name="nama" class="form-control"
                value="<?= htmlspecialchars($user['nama'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
        </div>

        <div class="form-row">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-control"
                value="<?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
        </div>

        <div class="form-row">
            <label for="password" class="form-label">Password <?= $isEdit ? '(Opsional)' : '' ?></label>
            <input type="password" id="password" name="password" class="form-control" <?= $isEdit ? '' : 'required' ?>>
        </div>

        <div class="form-row">
            <label for="no_telp" class="form-label">No. Telepon</label>
            <input type="tel" id="no_telp" name="no_telp" class="form-control"
                value="<?= htmlspecialchars($user['no_telp'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
        </div>

        <div class="form-row">
            <label for="no_id" class="form-label">No. Identitas</label>
            <input type="text" id="no_id" name="no_id" class="form-control"
                value="<?= htmlspecialchars($user['no_id'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
        </div>

        <div class="form-row">
            <label for="role" class="form-label">Role</label>
            <select id="role" name="role" class="form-control" required>
                <option value="peminjam" <?= ($user['role'] ?? '') === 'peminjam' ? 'selected' : '' ?>>Peminjam</option>
                <option value="admin" <?= ($user['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
        </div>

        <div class="form-actions">
            <a href="<?= $baseUrl ?>/index.php?page=pengguna" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>