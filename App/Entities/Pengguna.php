<?php
// app/Entities/Pengguna.php
declare(strict_types=1);

namespace App\Entities;

use App\Config\Database;
use App\Contracts\AutentikasiInterface;

class Pengguna implements AutentikasiInterface
{
    protected string $id_pengguna = '';
    protected string $nama_lengkap = '';
    protected string $email = '';
    protected string $no_telp = '';
    protected string $no_id = '';
    protected string $role = 'peminjam';

    public function login(string $email, string $password): ?array
    {
        $user = Database::fetchOne("SELECT * FROM pengguna WHERE email = :email LIMIT 1", [':email' => trim($email)]);
        if (!$user || !password_verify($password, $user['password'])) {
            return null;
        }
        $this->id_pengguna  = $user['id_pengguna'];
        $this->nama_lengkap = $user['nama'];
        $this->email        = $user['email'];
        $this->no_telp      = $user['no_telp'];
        $this->no_id        = $user['no_id'];
        $this->role         = $user['role'];
        return $user;
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    public function updateProfil(string $idPengguna, array $data): bool
    {
        return Database::exec(
            "UPDATE pengguna SET nama = :nama, email = :email, no_telp = :telp WHERE id_pengguna = :id",
            [':nama' => trim($data['nama']), ':email' => trim($data['email']), ':telp' => trim($data['no_telp'] ?? ''), ':id' => $idPengguna]
        );
    }

    public function findById(string $id): ?array
    {
        return Database::fetchOne("SELECT * FROM pengguna WHERE id_pengguna = :id LIMIT 1", [':id' => $id]);
    }

    public function findByEmail(string $email): ?array
    {
        return Database::fetchOne("SELECT * FROM pengguna WHERE email = :email LIMIT 1", [':email' => trim($email)]);
    }

    public function validate(array $data, bool $requirePass = false): array
    {
        $err = [];
        if (trim($data['nama'] ?? '') === '') $err[] = 'Nama wajib diisi.';
        if (!filter_var(trim($data['email'] ?? ''), FILTER_VALIDATE_EMAIL)) $err[] = 'Format email tidak valid.';
        if ($requirePass && strlen($data['password'] ?? '') < 6) $err[] = 'Password minimal 6 karakter.';
        return $err;
    }
}
