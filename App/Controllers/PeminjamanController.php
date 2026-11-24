<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Entities\Admin;
use App\Entities\BarangInventaris;
use App\Entities\DetailPeminjaman;
use App\Entities\Peminjam;
use App\Entities\TransaksiPeminjaman;
use App\Middleware\AuthMiddleware;
use App\Entities\Denda;

class PeminjamanController extends BaseController
{
    public function index(): void
    {
        AuthMiddleware::check();
        $isAdmin = ($_SESSION['role'] ?? '') === 'admin';
        $status  = trim($_GET['status'] ?? '');
        $this->render('peminjaman/index', [
            'loans'        => (new TransaksiPeminjaman())->getAll($isAdmin ? null : (string)$_SESSION['id_pengguna'], $status),
            'activeStatus' => $status,
        ]);
    }

    public function create(): void
    {
        AuthMiddleware::check();
        $barang = (new BarangInventaris())->findRowById(trim($_GET['id_barang'] ?? ''));
        if (!$barang || (int)$barang['jumlah_tersedia'] < 1) exit('Barang tidak tersedia.');
        $this->render('peminjaman/create', ['barang' => $barang]);
    }

    public function store(): void
    {
        AuthMiddleware::check();
        $this->requirePost();
        $hasil = (new Peminjam((string)$_SESSION['id_pengguna']))->ajukanPinjaman($_POST);
        if (!$hasil['success']) exit(htmlspecialchars($hasil['message'], ENT_QUOTES, 'UTF-8'));
        $this->redirect('page=peminjaman-saya');
    }

    public function verifikasi(): void
    {
        AuthMiddleware::check(true);
        $this->requirePost();
        (new Admin())->verifikasiPeminjaman(trim($_POST['id_peminjaman'] ?? ''), trim($_POST['aksi'] ?? ''));
        $this->redirect('page=peminjaman');
    }

    /** FR-09: Konfirmasi penyerahan fisik barang (Disetujui -> Dipinjam) */
    public function serahkan(): void
    {
        AuthMiddleware::check(true);
        $this->requirePost();
        (new TransaksiPeminjaman())->updateStatus(trim($_POST['id_peminjaman'] ?? ''), 'Dipinjam');
        $this->redirect('page=peminjaman');
    }

    /** FR-10: Form Pengembalian Barang */
    public function pengembalian(): void
    {
        AuthMiddleware::check(true);
        $this->render('pengembalian/create', ['activeLoans' => (new TransaksiPeminjaman())->getPeminjamanAktif()]);
    }

    /** FR-10, FR-12, FR-14: Proses Simpan Pengembalian & Kondisi Barang */
    public function pengembalianStore(): void
    {
        AuthMiddleware::check(true);
        $this->requirePost();
        (new DetailPeminjaman())->catatKondisiPengembalian($_POST, (string)($_SESSION['id_pengguna'] ?? ''));
        $this->redirect('page=pengembalian');
    }

    /** FR-13: Laporan Inventaris & Peminjaman */
    public function laporan(): void
    {
        AuthMiddleware::check(true);
        $this->render('laporan/index', [
            'reports' => (new TransaksiPeminjaman())->getLaporan(trim($_GET['mulai'] ?? ''), trim($_GET['selesai'] ?? '')),
        ]);
    }

    /** Halaman Daftar Denda (Admin melihat semua & pelunasan; Peminjam melihat denda miliknya) */
    public function denda(): void
    {
        AuthMiddleware::check();
        $isAdmin = ($_SESSION['role'] ?? '') === 'admin';
        $status  = trim($_GET['status'] ?? '');
        $denda   = new Denda();

        $this->render('denda/index', [
            'fines'        => $denda->getAll($status, $isAdmin ? null : (string)$_SESSION['id_pengguna']),
            'ringkasan'    => $denda->getRingkasan(),
            'activeStatus' => $status,
            'items'        => $isAdmin ? (new BarangInventaris())->getAllSortedById() : [],
        ]);
    }

    /** Admin mencatat denda manual (misal kerusakan/kehilangan barang) */
    public function dendaStore(): void
    {
        AuthMiddleware::check(true);
        $this->requirePost();

        $idBarang = trim($_POST['id_barang'] ?? '');
        $alasan   = trim($_POST['alasan'] ?? '');
        $biaya    = (float)($_POST['jml_biaya'] ?? 0);

        if ($idBarang !== '' && $alasan !== '' && $biaya > 0) {
            (new Denda())->simpanDenda($idBarang, $alasan, $biaya);
        }
        $this->redirect('page=denda');
    }

    /** Admin menandai denda sudah dibayar */
    public function dendaLunasi(): void
    {
        AuthMiddleware::check(true);
        $this->requirePost();

        $idDenda = trim($_POST['id_denda'] ?? '');
        if ($idDenda !== '') {
            (new Denda())->lunasiDenda($idDenda);
        }
        $this->redirect('page=denda');
    }
}
