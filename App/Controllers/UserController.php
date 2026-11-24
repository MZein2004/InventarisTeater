<?php
// app/Controllers/UserController.php
declare(strict_types=1);

namespace App\Controllers;

use App\Entities\Admin;
use App\Entities\Pengguna;
use App\Middleware\AuthMiddleware;

class UserController extends BaseController
{
    public function index(): void
    {
        AuthMiddleware::check(true);
        $this->render('user/index', ['users' => (new Admin())->getAllPengguna()]);
    }

    public function create(): void
    {
        AuthMiddleware::check(true);
        $this->render('user/form', ['user' => null]);
    }

    public function store(): void
    {
        AuthMiddleware::check(true);
        $this->requirePost();
        $admin = new Admin();
        if ($err = $admin->validate($_POST, true)) exit(implode(' ', $err));
        $admin->simpanPengguna($_POST);
        $this->redirect('page=pengguna');
    }

    public function edit(): void
    {
        AuthMiddleware::check(true);
        $user = (new Admin())->findById(trim($_GET['id'] ?? ''));
        if (!$user) exit('Pengguna tidak ditemukan.');
        $this->render('user/form', ['user' => $user]);
    }

    public function update(): void
    {
        AuthMiddleware::check(true);
        $this->requirePost();
        $admin = new Admin();
        $id    = trim($_POST['id_pengguna'] ?? '');
        if ($id === '' || $admin->validate($_POST, false)) exit('Data tidak valid.');
        $admin->updatePengguna($id, $_POST);
        $this->redirect('page=pengguna');
    }

    public function delete(): void
    {
        AuthMiddleware::check(true);
        $id = trim($_GET['id'] ?? '');
        if ($id !== '' && $id !== ($_SESSION['id_pengguna'] ?? '')) (new Admin())->hapusPengguna($id);
        $this->redirect('page=pengguna');
    }

    public function profil(): void
    {
        AuthMiddleware::check();
        $this->render('profil/index', ['profil' => (new Pengguna())->findById((string)$_SESSION['id_pengguna'])]);
    }

    public function updateProfil(): void
    {
        AuthMiddleware::check();
        $this->requirePost();
        $pengguna = new Pengguna();
        if ($err = $pengguna->validate($_POST, false)) exit(implode(' ', $err));
        $pengguna->updateProfil((string)$_SESSION['id_pengguna'], $_POST);
        $_SESSION['nama'] = trim($_POST['nama']);
        $this->redirect('page=profil');
    }
}
