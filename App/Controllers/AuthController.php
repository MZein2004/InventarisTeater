<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Entities\Peminjam;
use App\Entities\Pengguna;

class AuthController extends BaseController
{
    public function index(): void
    {
        $this->showLogin();
    }

    public function showLogin(): void
    {
        if (isset($_SESSION['id_pengguna'])) {
            $this->redirect('page=dashboard');
        }

        $this->render('auth/login', [
            'errorMessage'   => isset($_GET['error']) ? 'Email atau password salah.' : '',
            'successMessage' => isset($_GET['registered']) ? 'Registrasi berhasil! Silakan masuk dengan akun Anda.' : '',
        ]);
    }

    public function login(): void
    {
        $this->requirePost();
        $user = (new Pengguna())->login((string)($_POST['email'] ?? ''), (string)($_POST['password'] ?? ''));
        if (!$user) {
            $this->redirect('page=login&error=1');
        }

        session_regenerate_id(true);
        $_SESSION['id_pengguna'] = $user['id_pengguna'];
        $_SESSION['nama']        = $user['nama'];
        $_SESSION['role']        = $user['role'];
        $this->redirect('page=dashboard');
    }

    public function showRegister(): void
    {
        if (isset($_SESSION['id_pengguna'])) {
            $this->redirect('page=dashboard');
        }

        $this->render('auth/register', [
            'errorMessage' => $_GET['error'] ?? '',
        ]);
    }

    public function register(): void
    {
        $this->requirePost();
        $peminjam = new Peminjam();

        if ($err = $peminjam->validate($_POST, true)) {
            $this->redirect('page=register&error=' . urlencode(implode(' ', $err)));
        }
        if (($_POST['password'] ?? '') !== ($_POST['konfirmasi_password'] ?? '')) {
            $this->redirect('page=register&error=' . urlencode('Konfirmasi password tidak cocok.'));
        }
        if (trim((string)($_POST['no_id'] ?? '')) === '') {
            $this->redirect('page=register&error=' . urlencode('NIM / NIK / Nomor Identitas wajib diisi.'));
        }
        if ($peminjam->findByEmail((string)$_POST['email'])) {
            $this->redirect('page=register&error=' . urlencode('Email sudah terdaftar. Silakan gunakan email lain.'));
        }

        $peminjam->registrasi($_POST);
        $this->redirect('page=login&registered=1');
    }

    public function logout(): void
    {
        (new Pengguna())->logout();
        $this->redirect('page=login');
    }
}
