<?php
// app/Entities/Peminjam.php
declare(strict_types=1);

namespace App\Entities;

use App\Config\Database;
use App\Services\ProsesPeminjaman;

class Peminjam extends Pengguna
{
    protected string $role = 'peminjam';

    public function __construct(string $idPengguna = '')
    {
        $this->id_pengguna = $idPengguna;
    }

    public function registrasi(array $data): bool
    {
        return Database::exec(
            "INSERT INTO pengguna (id_pengguna, nama, email, password, no_telp, no_id, role)
             VALUES (:id, :nama, :email, :pass, :telp, :noid, 'peminjam')",
            [
                ':id'   => Database::generateId('pengguna', 'id_pengguna', 'USR'),
                ':nama' => trim($data['nama']),
                ':email' => trim($data['email']),
                ':pass' => password_hash((string)$data['password'], PASSWORD_DEFAULT),
                ':telp' => trim($data['no_telp'] ?? ''),
                ':noid' => trim($data['no_id'] ?? ''),
            ]
        );
    }

    public function ajukanPinjaman(array $dataPengajuan): array
    {
        return (new ProsesPeminjaman())->prosesPengajuan($this->id_pengguna, $dataPengajuan);
    }
}
