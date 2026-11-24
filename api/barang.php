<?php

declare(strict_types=1);

session_start();
require_once __DIR__ . '/../app/autoload.php';

use App\Entities\BarangInventaris;

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['id_pengguna'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Login diperlukan.']);
    exit;
}

try {
    $model   = new BarangInventaris();
    $keyword = trim($_GET['search'] ?? '');
    $kat     = trim($_GET['kategori'] ?? '');
    $data    = $model->getAllFiltered($keyword, $kat);

    echo json_encode([
        'success' => true,
        'data'    => $data,
    ]);
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan server.']);
}
