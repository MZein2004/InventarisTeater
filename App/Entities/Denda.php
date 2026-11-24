<?php

declare(strict_types=1);

namespace App\Entities;

use App\Algorithms\AlgoritmaDAA;
use App\Config\Database;

class Denda
{
    public function hitungDenda(int $hariTerlambat, float $tarifPerHari = 10000.0, float $biayaTambahan = 0.0): float
    {
        return ($hariTerlambat * $tarifPerHari) + $biayaTambahan;
    }

    public function simpanDenda(string $idBarang, string $alasan, float $jumlahBiaya): bool
    {
        if ($idBarang === '' || $jumlahBiaya <= 0) {
            return false;
        }

        $ok = Database::exec(
            "INSERT INTO denda (id_denda, id_barang, alasan, jml_biaya, status_pembayaran)
             VALUES (:did, :bid, :als, :bya, 'Belum dibayar')",
            [
                ':did' => Database::generateId('denda', 'id_denda', 'DND'),
                ':bid' => $idBarang,
                ':als' => trim($alasan),
                ':bya' => $jumlahBiaya,
            ]
        );
        if ($ok) {
            AlgoritmaDAA::clearMemo();
        }
        return $ok;
    }

    public function lunasiDenda(string $idDenda): bool
    {
        $ok = Database::exec(
            "UPDATE denda SET status_pembayaran = 'Sudah dibayar' WHERE id_denda = :id",
            [':id' => $idDenda]
        );
        if ($ok) {
            AlgoritmaDAA::clearMemo();
        }
        return $ok;
    }

    public function getAll(string $status = '', ?string $idPengguna = null): array
    {
        $where  = ['1=1'];
        $params = [];

        if ($status !== '') {
            $where[]         = 'd.status_pembayaran = :st';
            $params[':st']   = $status;
        }
        if ($idPengguna !== null) {
            $where[]         = 'd.alasan LIKE :uid';
            $params[':uid']  = '%' . $idPengguna . '%';
        }

        return Database::fetchAll(
            "SELECT d.id_denda, d.id_barang, d.alasan, d.jml_biaya, d.status_pembayaran,
                    COALESCE(u.nama_barang, d.id_barang) AS nama_barang
             FROM denda d
             LEFT JOIN unit_barang u ON d.id_barang = u.id_barang
             WHERE " . implode(' AND ', $where) . "
             ORDER BY d.id_denda DESC",
            $params
        );
    }

    public function getRingkasan(): array
    {
        return AlgoritmaDAA::memoize('ringkasan_denda', function () {
            $row = Database::fetchOne(
                "SELECT
                    COALESCE(SUM(CASE WHEN status_pembayaran = 'Belum dibayar' THEN jml_biaya ELSE 0 END), 0) AS total_belum,
                    COALESCE(SUM(CASE WHEN status_pembayaran = 'Sudah dibayar' THEN jml_biaya ELSE 0 END), 0) AS total_lunas,
                    SUM(status_pembayaran = 'Belum dibayar') AS kasus_aktif
                 FROM denda"
            );
            return [
                'total_belum' => (float)($row['total_belum'] ?? 0),
                'total_lunas' => (float)($row['total_lunas'] ?? 0),
                'kasus_aktif' => (int)($row['kasus_aktif'] ?? 0),
            ];
        });
    }
}
