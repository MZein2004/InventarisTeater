<?php
// app/Middleware/AuthMiddleware.php
declare(strict_types=1);

namespace App\Middleware;

class AuthMiddleware
{
    public static function check(bool $adminOnly = false): void
    {
        if (!isset($_SESSION['id_pengguna'])) {
            $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
            header("Location: {$base}/index.php?page=login");
            exit;
        }
        if ($adminOnly && ($_SESSION['role'] ?? '') !== 'admin') {
            http_response_code(403);
            exit('Akses ditolak. Halaman ini khusus Admin.');
        }
    }
}
