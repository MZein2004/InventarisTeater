<?php
// app/Controllers/DashboardController.php
declare(strict_types=1);

namespace App\Controllers;

use App\Entities\BarangInventaris;
use App\Entities\TransaksiPeminjaman;
use App\Middleware\AuthMiddleware;

class DashboardController extends BaseController
{
    public function index(): void
    {
        AuthMiddleware::check();
        $trx = new TransaksiPeminjaman();

        if (($_SESSION['role'] ?? '') === 'admin') {
            $this->render('dashboard/admin', [
                'stats'       => (new BarangInventaris())->getStatistikDashboard(),
                'recentLoans' => $trx->getTerbaru(5),
            ]);
            return;
        }
        $this->render('dashboard/peminjam', ['userStats' => $trx->getStatistikPeminjam((string)$_SESSION['id_pengguna'])]);
    }
}
