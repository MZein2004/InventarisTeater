<?php
$pageTitle    = 'Profil Saya';
$pageSubtitle = 'Lihat dan ubah data profil.';
$activeMenu   = 'profil';
$profil       = $profil ?? [
    'nama' => 'Raka',
    'email' => 'raka@contoh.com',
    'no_telp' => '08123456789',
    'no_id' => '24416255201001'
];

require __DIR__ . '/../layouts/header.php';
?>

<section class="section-block">
    <form action="<?= $baseUrl ?>/index.php?action=profil-update" method="post" class="card profile-card" id="formProfil">
        <div class="profile-avatar-wrapper">
            <div class="profile-avatar" aria-hidden="true">&#128100;</div>
        </div>

        <div class="form-row">
            <label for="profil_nama" class="form-label">Nama</label>
            <input type="text" id="profil_nama" name="nama" class="form-control"
                value="<?= htmlspecialchars($profil['nama'], ENT_QUOTES, 'UTF-8') ?>" required>
        </div>

        <div class="form-row">
            <label for="profil_email" class="form-label">Email</label>
            <input type="email" id="profil_email" name="email" class="form-control"
                value="<?= htmlspecialchars($profil['email'], ENT_QUOTES, 'UTF-8') ?>" required>
        </div>

        <div class="form-row">
            <label for="profil_telp" class="form-label">No. Telepon</label>
            <input type="tel" id="profil_telp" name="no_telp" class="form-control"
                value="<?= htmlspecialchars($profil['no_telp'], ENT_QUOTES, 'UTF-8') ?>" required>
        </div>

        <div class="form-row">
            <label for="profil_noid" class="form-label">No. Identitas</label>
            <input type="text" id="profil_noid" name="no_id" class="form-control"
                value="<?= htmlspecialchars($profil['no_id'], ENT_QUOTES, 'UTF-8') ?>" readonly>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>