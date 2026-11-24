<?php
// app/Middleware/AdminMiddleware.php
declare(strict_types=1);

namespace App\Middleware;

class AdminMiddleware extends AuthMiddleware
{
    public function handle(): void
    {
        parent::__construct();

        if (($_SESSION['role'] ?? '') !== 'admin') {
            http_response_code(403);
            exit('Akses ditolak. Halaman ini khusus Admin.');
        }
    }
}
