<?php
// app/Entities/Admin.php
declare(strict_types=1);

namespace App\Entities;

use App\Config\Database;

class Admin extends Pengguna
{
    protected string $role = 'admin';

    public function tambahBarang(BarangInventaris $barang, ?array $file = null): bool
    {
        return $barang->simpanBaru($file);
    }

    public function verifikasiPeminjaman(string $idTransaksi, string $keputusan): bool
    {
        return (new TransaksiPeminjaman())->updateStatus($idTransaksi, $keputusan);
    }

    public function getAllPengguna(): array
    {
        return Database::fetchAll("SELECT id_pengguna, nama, email, no_telp, no_id, role FROM pengguna ORDER BY id_pengguna ASC");
    }

    public function simpanPengguna(array $data): bool
    {
        $role = in_array($data['role'] ?? '', ['admin', 'peminjam'], true) ? $data['role'] : 'peminjam';
        $id   = Database::generateId('pengguna', 'id_pengguna', $role === 'admin' ? 'ADM' : 'USR');

        return Database::exec(
            "INSERT INTO pengguna (id_pengguna, nama, email, password, no_telp, no_id, role)
             VALUES (:id, :nama, :email, :pass, :telp, :noid, :role)",
            [
                ':id'   => $id,
                ':nama' => trim($data['nama']),
                ':email' => trim($data['email']),
                ':pass' => password_hash((string)$data['password'], PASSWORD_DEFAULT),
                ':telp' => trim($data['no_telp'] ?? ''),
                ':noid' => trim($data['no_id'] ?? ''),
                ':role' => $role,
            ]
        );
    }

    public function updatePengguna(string $id, array $data): bool
    {
        $role = in_array($data['role'] ?? '', ['admin', 'peminjam'], true) ? $data['role'] : 'peminjam';
        $params = [
            ':nama' => trim($data['nama']),
            ':email' => trim($data['email']),
            ':telp' => trim($data['no_telp'] ?? ''),
            ':noid' => trim($data['no_id'] ?? ''),
            ':role' => $role,
            ':id'   => $id,
        ];

        $passSql = '';
        if (!empty($data['password'])) {
            $passSql = ', password = :pass';
            $params[':pass'] = password_hash((string)$data['password'], PASSWORD_DEFAULT);
        }

        return Database::exec(
            "UPDATE pengguna SET nama = :nama, email = :email, no_telp = :telp, no_id = :noid, role = :role{$passSql} WHERE id_pengguna = :id",
            $params
        );
    }

    public function hapusPengguna(string $id): bool
    {
        return Database::exec("DELETE FROM pengguna WHERE id_pengguna = :id", [':id' => $id]);
    }
}
