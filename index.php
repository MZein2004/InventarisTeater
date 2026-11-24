<?php

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

spl_autoload_register(function (string $class): void {
    $prefix  = 'App\\';
    $baseDir = __DIR__ . '/app/';

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $relativePath  = str_replace('\\', '/', $relativeClass) . '.php';
    $file          = $baseDir . $relativePath;

    if (file_exists($file)) {
        require_once $file;
        return;
    }

    $parts = explode('/', $relativePath);
    if (count($parts) === 2) {
        $lowerFolderFile = $baseDir . strtolower($parts[0]) . '/' . $parts[1];
        if (file_exists($lowerFolderFile)) {
            require_once $lowerFolderFile;
        }
    }
});

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\InventarisController;
use App\Controllers\PeminjamanController;
use App\Controllers\UserController;

$action = trim($_GET['action'] ?? '');
$page   = trim($_GET['page'] ?? '');

try {
    if ($action !== '') {
        match ($action) {
            'login'                 => (new AuthController())->login(),
            'register'              => (new AuthController())->register(),
            'logout'                => (new AuthController())->logout(),
            'inventaris-store'      => (new InventarisController())->store(),
            'inventaris-update'     => (new InventarisController())->update(),
            'inventaris-delete'     => (new InventarisController())->delete(),
            'inventaris-perbaikan'  => (new InventarisController())->selesaiPerbaikan(),
            'kategori-store'        => (new InventarisController())->kategoriStore(),
            'kategori-update'       => (new InventarisController())->kategoriUpdate(),
            'kategori-delete'       => (new InventarisController())->kategoriDelete(),
            'peminjaman-store'      => (new PeminjamanController())->store(),
            'peminjaman-verifikasi' => (new PeminjamanController())->verifikasi(),
            'peminjaman-serahkan'   => (new PeminjamanController())->serahkan(),
            'pengembalian-store'    => (new PeminjamanController())->pengembalianStore(),
            'user-store'            => (new UserController())->store(),
            'user-update'           => (new UserController())->update(),
            'user-delete'           => (new UserController())->delete(),
            'profil-update'         => (new UserController())->updateProfil(),
            default                 => (new DashboardController())->index(),
            'denda-store'           => (new PeminjamanController())->dendaStore(),
            'denda-lunasi'          => (new PeminjamanController())->dendaLunasi(),
        };
        exit;
    }

    if ($page === '') {
        $page = isset($_SESSION['id_pengguna']) ? 'dashboard' : 'login';
    }

    match ($page) {
        'login'             => (new AuthController())->showLogin(),
        'register'          => (new AuthController())->showRegister(),
        'dashboard'         => (new DashboardController())->index(),
        'inventaris'        => (new InventarisController())->index(),
        'inventaris-detail' => (new InventarisController())->detail(),
        'inventaris-create' => (new InventarisController())->create(),
        'inventaris-edit'   => (new InventarisController())->edit(),
        'kategori'          => (new InventarisController())->kategori(),
        'katalog'           => (new InventarisController())->katalog(),
        'peminjaman',
        'peminjaman-saya'   => (new PeminjamanController())->index(),
        'peminjaman-create' => (new PeminjamanController())->create(),
        'pengembalian'      => (new PeminjamanController())->pengembalian(),
        'laporan'           => (new PeminjamanController())->laporan(),
        'pengguna'          => (new UserController())->index(),
        'user-create'       => (new UserController())->create(),
        'user-edit'         => (new UserController())->edit(),
        'profil'            => (new UserController())->profil(),
        default             => (new DashboardController())->index(),
        'denda'             => (new PeminjamanController())->denda(),
    };
} catch (\Error $e) {
    if (str_contains($e->getMessage(), 'not found')) {
        http_response_code(500);
        echo "<h3>File Class Backend Belum Ditemukan</h3>";
        echo "<p>Detail: <strong>" . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</strong></p>";
        echo "<p>Path yang dicari: <code>" . htmlspecialchars(__DIR__ . '/app/', ENT_QUOTES, 'UTF-8') . "</code></p>";
        exit;
    }
    throw $e;
}
