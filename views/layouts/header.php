<?php
$pageTitle    = $pageTitle ?? 'Sistem Inventaris Kostum & Properti Teater';
$pageSubtitle = $pageSubtitle ?? '';
$activeMenu   = $activeMenu ?? 'dashboard';
$role         = $_SESSION['role'] ?? 'admin';
$namaUser     = $_SESSION['nama'] ?? 'Admin';
$baseUrl      = $baseUrl ?? '/PeminjamanKostumdanPropertiTeater';
if (!isset($baseUrl)) {
    $baseUrl = '/PeminjamanKostumdanPropertiTeater';
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ($pageTitle) ?></title>
    <link rel="stylesheet" href="<?= $baseUrl ?>/assets/css/style.css">
</head>

<body class="app-body">
    <div class="app-layout">
        <!-- Sidebar Navigasi -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="<?= $baseUrl ?>/index.php?page=dashboard" class="brand">
                    <span class="brand-icon" aria-hidden="true">&#127917;</span>
                    <span class="brand-text">Teater</span>
                </a>
            </div>

            <nav class="sidebar-nav" aria-label="Menu Utama">
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="<?= $baseUrl ?>/index.php?page=dashboard"
                            class="nav-link <?= $activeMenu === 'dashboard' ? 'is-active' : '' ?>">
                            Dashboard
                        </a>
                    </li>

                    <?php if ($role === 'admin'): ?>
                        <li class="nav-item">
                            <a href="<?= $baseUrl ?>/index.php?page=inventaris"
                                class="nav-link <?= $activeMenu === 'inventaris' ? 'is-active' : '' ?>">
                                Inventaris
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $baseUrl ?>/index.php?page=peminjaman"
                                class="nav-link <?= $activeMenu === 'peminjaman' ? 'is-active' : '' ?>">
                                Peminjaman
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $baseUrl ?>/index.php?page=pengembalian"
                                class="nav-link <?= $activeMenu === 'pengembalian' ? 'is-active' : '' ?>">
                                Pengembalian
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $baseUrl ?>/index.php?page=denda" class="nav-link <?= ($activeMenu ?? '') === 'denda' ? 'is-active' : '' ?>">
                                <?= (($_SESSION['role'] ?? '') === 'admin') ? 'Data Denda' : 'Denda Saya' ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $baseUrl ?>/index.php?page=pengguna"
                                class="nav-link <?= $activeMenu === 'pengguna' ? 'is-active' : '' ?>">
                                Pengguna
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $baseUrl ?>/index.php?page=laporan"
                                class="nav-link <?= $activeMenu === 'laporan' ? 'is-active' : '' ?>">
                                Laporan
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a href="<?= $baseUrl ?>/index.php?page=katalog"
                                class="nav-link <?= $activeMenu === 'katalog' ? 'is-active' : '' ?>">
                                Katalog Barang
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $baseUrl ?>/index.php?page=peminjaman-saya"
                                class="nav-link <?= $activeMenu === 'peminjaman-saya' ? 'is-active' : '' ?>">
                                Peminjaman Saya
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $baseUrl ?>/index.php?page=profil"
                                class="nav-link <?= $activeMenu === 'profil' ? 'is-active' : '' ?>">
                                Profil
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>

            <div class="sidebar-footer">
                <a href="<?= $baseUrl ?>/index.php?action=logout" class="nav-link nav-logout">
                    Logout
                </a>
            </div>
        </aside>

        <!-- Kontainer Konten Utama -->
        <div class="main-wrapper">
            <header class="topbar">
                <div class="topbar-heading">
                    <h1 class="page-title"><?= ($pageTitle) ?></h1>
                    <?php if ($pageSubtitle !== ''): ?>
                        <p class="page-subtitle"><?= ($pageSubtitle) ?></p>
                    <?php endif; ?>
                </div>
                <div class="topbar-user">
                    <span class="user-badge" title="<?= htmlspecialchars($namaUser, ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars($namaUser, ENT_QUOTES, 'UTF-8') ?>
                    </span>
                </div>
            </header>

            <main class="main-content">