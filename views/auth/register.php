<?php
$baseUrl = $baseUrl ?? '/PeminjamanKostumdanPropertiTeater';
$errorMessage = $errorMessage ?? '';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Peminjam - Sistem Inventaris Kostum & Properti Teater</title>
    <link rel="stylesheet" href="<?= $baseUrl ?>/assets/css/style.css">
</head>

<body class="auth-body">
    <main class="auth-container">
        <div class="auth-card card">
            <div class="auth-header">
                <div class="brand-icon" aria-hidden="true">🎭</div>
                <h1>Daftar Akun Peminjam</h1>
                <p>Buat akun untuk meminjam kostum dan properti teater</p>
            </div>

            <?php if ($errorMessage !== ''): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form method="post" action="<?= $baseUrl ?>/index.php?action=register" class="auth-form">
                <div class="form-group">
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" class="form-control" placeholder="Masukkan nama lengkap" required>
                </div>

                <div class="form-group">
                    <label for="no_id">NIM / NIK / No. Anggota</label>
                    <input type="text" id="no_id" name="no_id" class="form-control" placeholder="Contoh: 23416255201001" required>
                </div>

                <div class="form-group">
                    <label for="no_telp">No. Telepon / WhatsApp</label>
                    <input type="text" id="no_telp" name="no_telp" class="form-control" placeholder="Contoh: 081234567890" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="nama@email.com" required>
                </div>

                <div class="form-group">
                    <label for="password">Password (minimal 6 karakter)</label>
                    <input type="password" id="password" name="password" class="form-control" minlength="6" placeholder="Buat password" required>
                </div>

                <div class="form-group">
                    <label for="konfirmasi_password">Konfirmasi Password</label>
                    <input type="password" id="konfirmasi_password" name="konfirmasi_password" class="form-control" minlength="6" placeholder="Ulangi password" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">Daftar Sekarang</button>
            </form>

            <p class="auth-footer">
                Sudah punya akun? <a href="<?= $baseUrl ?>/index.php?page=login">Login di sini</a>
            </p>
        </div>
    </main>
</body>

</html>