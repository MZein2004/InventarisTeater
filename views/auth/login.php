<?php
$baseUrl = $baseUrl ?? '/PeminjamanKostumdanPropertiTeater';
$errorMessage = $errorMessage ?? '';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Inventaris Kostum &amp; Properti Teater</title>
    <link rel="stylesheet" href="<?= $baseUrl ?>/assets/css/style.css">
</head>

<body class="auth-body">
    <div class="auth-layout">
        <section class="auth-panel">
            <div class="auth-card">
                <div class="auth-header">
                    <div class="auth-logo" aria-hidden="true">&#127917;</div>
                    <h1 class="auth-title">Sistem Inventaris Kostum &amp; Properti Teater</h1>
                    <p class="auth-subtitle">Masuk untuk mengakses sistem</p>
                </div>

                <?php if ($errorMessage !== ''): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <form action="<?= $baseUrl ?>/index.php?action=login" method="post" class="auth-form" id="formLogin">
                    <div class="form-field">
                        <label for="email" class="sr-only">Email</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="Email" required>
                    </div>

                    <div class="form-field">
                        <label for="password" class="sr-only">Password</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
                    </div>

                    <div class="auth-options">
                        <label class="checkbox-label">
                            <input type="checkbox" name="remember" value="1">
                            <span>Ingat saya</span>
                        </label>
                        <a href="#hubungi-admin" class="auth-link">Lupa password?</a>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Login</button>
                </form>

                <?php if (!empty($successMessage)): ?>
                    <div class="alert alert-success" role="alert">
                        <?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <p class="auth-footer">
                    Belum punya akun? <a href="<?= $baseUrl ?>/index.php?page=register">Daftar di sini</a>
                </p>
            </div>
        </section>

        <aside class="auth-showcase" aria-hidden="true">
            <div class="auth-showcase-overlay"></div>
        </aside>
    </div>

    <script src="<?= $baseUrl ?>/assets/js/main.js"></script>
</body>

</html>