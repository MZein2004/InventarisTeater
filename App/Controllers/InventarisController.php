<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Algorithms\AlgoritmaDAA;
use App\Entities\Admin;
use App\Entities\BarangInventaris;
use App\Entities\Kostum;
use App\Entities\Properti;
use App\Middleware\AuthMiddleware;

class InventarisController extends BaseController
{
    private BarangInventaris $inventaris;

    private const CLASS_MAP = [
        'KAT00001' => Kostum::class,
        'KAT00002' => Properti::class,
    ];

    public function __construct()
    {
        parent::__construct();
        $this->inventaris = new BarangInventaris();
    }

    private function buatEntitasBarang(array $data): BarangInventaris
    {
        $class = self::CLASS_MAP[trim($data['id_kategori'] ?? '')] ?? BarangInventaris::class;
        return new $class($data);
    }

    public function index(): void
    {
        AuthMiddleware::check(true);
        $this->render('inventaris/index', [
            'items'     => $this->inventaris->getAllFiltered(trim($_GET['q'] ?? ''), trim($_GET['kategori'] ?? ''), trim($_GET['status'] ?? '')),
            'kategoris' => $this->inventaris->getAllKategori(),
        ]);
    }

    public function katalog(): void
    {
        AuthMiddleware::check();
        $q     = trim($_GET['q'] ?? '');
        $items = $this->inventaris->getAllFiltered($q, trim($_GET['kategori'] ?? ''));
        $saran = empty($items) ? AlgoritmaDAA::saranTerdekat($q, $this->inventaris->getAllSortedById()) : null;

        $this->render('inventaris/katalog', [
            'items'          => $items,
            'kategoris'      => $this->inventaris->getAllKategori(),
            'saranPencarian' => $saran,
        ]);
    }

    public function detail(): void
    {
        AuthMiddleware::check();
        $barang = $this->inventaris->findRowById(trim($_GET['id'] ?? ''));
        if (!$barang) exit('Data barang tidak ditemukan.');
        $this->render('inventaris/detail', ['barang' => $barang]);
    }

    public function create(): void
    {
        AuthMiddleware::check(true);
        $this->render('inventaris/form', ['kategoris' => $this->inventaris->getAllKategori(), 'barang' => null]);
    }

    public function store(): void
    {
        AuthMiddleware::check(true);
        $this->requirePost();
        (new Admin())->tambahBarang($this->buatEntitasBarang($_POST), $_FILES['foto'] ?? null);
        $this->redirect('page=inventaris');
    }

    public function edit(): void
    {
        AuthMiddleware::check(true);
        $barang = $this->inventaris->findRowById(trim($_GET['id'] ?? ''));
        if (!$barang) exit('Barang tidak ditemukan.');
        $this->render('inventaris/form', ['kategoris' => $this->inventaris->getAllKategori(), 'barang' => $barang]);
    }

    public function update(): void
    {
        AuthMiddleware::check(true);
        $this->requirePost();
        $this->buatEntitasBarang($_POST)->update();
        $this->redirect('page=inventaris');
    }

    public function delete(): void
    {
        AuthMiddleware::check(true);
        if (($id = trim($_GET['id'] ?? '')) !== '') $this->inventaris->hapus($id);
        $this->redirect('page=inventaris');
    }

    public function selesaiPerbaikan(): void
    {
        AuthMiddleware::check(true);
        $this->requirePost();
        $id  = trim($_POST['id_barang'] ?? '');
        $jml = max(1, (int)($_POST['jumlah_pulih'] ?? 1));
        if ($id !== '') {
            $this->inventaris->selesaiPerbaikan($id, $jml);
        }
        $this->redirect('page=inventaris');
    }

    /* ================= KELOLA KATEGORI (FR-03) ================= */

    public function kategori(): void
    {
        AuthMiddleware::check(true);
        $this->render('inventaris/kategori', [
            'kategoris' => $this->inventaris->getAllKategori(),
            'errorMsg'  => isset($_GET['error']) ? 'Kategori tidak dapat dihapus karena masih digunakan oleh data barang.' : '',
        ]);
    }

    public function kategoriStore(): void
    {
        AuthMiddleware::check(true);
        $this->requirePost();
        $this->inventaris->simpanKategori(
            (string)($_POST['nama_kategori'] ?? ''),
            (string)($_POST['deskripsi'] ?? '')
        );
        $this->redirect('page=kategori');
    }

    public function kategoriUpdate(): void
    {
        AuthMiddleware::check(true);
        $this->requirePost();
        $this->inventaris->updateKategori(
            trim($_POST['id_kategori'] ?? ''),
            (string)($_POST['nama_kategori'] ?? ''),
            (string)($_POST['deskripsi'] ?? '')
        );
        $this->redirect('page=kategori');
    }

    public function kategoriDelete(): void
    {
        AuthMiddleware::check(true);
        $ok = $this->inventaris->hapusKategori(trim($_GET['id'] ?? ''));
        $this->redirect($ok ? 'page=kategori' : 'page=kategori&error=1');
    }
}
