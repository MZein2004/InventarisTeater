<?php

declare(strict_types=1);

namespace App\Entities;

use App\Algorithms\AlgoritmaDAA;
use App\Config\Database;
use Exception;

class DetailPeminjaman
{
    public function simpanDetail(string $idTransaksi, string $idBarang, int $jumlah): bool
    {
        return Database::exec(
            "INSERT INTO detail_peminjaman (id_detail, id_peminjaman, id_barang, jml_dipinjam)
             VALUES (:dtl, :pjm, :brg, :jml)",
            [':dtl' => Database::generateId('detail_peminjaman', 'id_detail', 'DTL'), ':pjm' => $idTransaksi, ':brg' => $idBarang, ':jml' => (string)$jumlah]
        );
    }

    /**
     * FR-10, FR-12, FR-14 & Aturan Bisnis #4, #6, #7:
     * - Baik   : stok tersedia dipulihkan (+jml), kondisi -> 'Baik'.
     * - Rusak  : stok tersedia TIDAK dipulihkan, kondisi -> 'Dalam Perbaikan'.
     * - Hilang : stok tersedia TIDAK dipulihkan & stok total dikurangi (-jml).
     */
    public function catatKondisiPengembalian(array $data, string $idAdmin = ''): bool
    {
        $idPjm   = trim($data['id_peminjaman'] ?? '');
        $kondisi = trim($data['kondisi_barang'] ?? 'Baik');
        $catatan = trim($data['catatan_kondisi'] ?? '');
        $tglKmb  = trim($data['tgl_kembali'] ?? date('Y-m-d'));
        $manual  = (float)($data['jml_biaya'] ?? 0);
        $alasan  = trim($data['alasan'] ?? '');

        $pdo = Database::getConnection();
        try {
            $pdo->beginTransaction();

            $pjm = Database::fetchOne(
                "SELECT p.id_pengguna, p.tgl_rencana_kembali, dp.id_barang, dp.jml_dipinjam
                 FROM peminjaman p JOIN detail_peminjaman dp ON p.id_peminjaman = dp.id_peminjaman
                 WHERE p.id_peminjaman = :id AND p.status_peminjaman IN ('Disetujui', 'Dipinjam')",
                [':id' => $idPjm]
            );
            if (!$pjm) {
                throw new Exception('Peminjaman aktif tidak ditemukan.');
            }

            $jml        = (int)$pjm['jml_dipinjam'];
            $auditAdmin = $idAdmin !== '' ? " (Petugas: {$idAdmin})" : '';

            Database::exec(
                "INSERT INTO pengembalian (id_pengembalian, id_peminjaman, id_pengguna, catatan_kondisi)
                 VALUES (:kid, :pid, :uid, :cat)",
                [
                    ':kid' => Database::generateId('pengembalian', 'id_pengembalian', 'KMB'),
                    ':pid' => $idPjm,
                    ':uid' => $pjm['id_pengguna'],
                    ':cat' => "[{$kondisi}] {$catatan}{$auditAdmin}",
                ]
            );

            // Pembaruan stok sesuai kondisi barang (FR-14 & Aturan Bisnis #4)
            if (strcasecmp($kondisi, 'Hilang') === 0) {
                Database::exec(
                    "UPDATE unit_barang SET jumlah_stok = GREATEST(0, jumlah_stok - :jml) WHERE id_barang = :bid",
                    [':jml' => $jml, ':bid' => $pjm['id_barang']]
                );
                Database::exec(
                    "UPDATE barang_inventaris SET jumlah_stok = GREATEST(0, jumlah_stok - :jml) WHERE id_barang = :bid",
                    [':jml' => $jml, ':bid' => $pjm['id_barang']]
                );
            } elseif (stripos($kondisi, 'Rusak') !== false || strcasecmp($kondisi, 'Dalam Perbaikan') === 0) {
                Database::exec(
                    "UPDATE unit_barang SET kondisi = 'Dalam Perbaikan' WHERE id_barang = :bid",
                    [':bid' => $pjm['id_barang']]
                );
                Database::exec(
                    "UPDATE barang_inventaris SET kondisi = 'Dalam Perbaikan' WHERE id_barang = :bid",
                    [':bid' => $pjm['id_barang']]
                );
            } else {
                Database::exec(
                    "UPDATE unit_barang SET jumlah_tersedia = LEAST(jumlah_stok, jumlah_tersedia + :jml), kondisi = 'Baik' WHERE id_barang = :bid",
                    [':jml' => $jml, ':bid' => $pjm['id_barang']]
                );
                Database::exec(
                    "UPDATE barang_inventaris SET kondisi = 'Baik' WHERE id_barang = :bid",
                    [':bid' => $pjm['id_barang']]
                );
            }

            Database::exec("UPDATE peminjaman SET status_peminjaman = 'Selesai' WHERE id_peminjaman = :id", [':id' => $idPjm]);

            // Hitung denda keterlambatan + kerusakan/kehilangan
            $telat = (new TransaksiPeminjaman())->hitungKeterlambatan($pjm['tgl_rencana_kembali'], $tglKmb);
            $denda = new Denda();
            $total = $denda->hitungDenda($telat, 10000.0, $manual);
            if ($total > 0) {
                $dasarKet = $alasan !== '' ? $alasan : ($telat > 0 ? "Terlambat {$telat} hari" : "Barang {$kondisi}");
                $ket      = "{$dasarKet} [{$idPjm} | {$pjm['nama_peminjam']} - {$pjm['id_pengguna']}]";
                $denda->simpanDenda($pjm['id_barang'], $ket, $total);
            }
            $pjm = Database::fetchOne(
                "SELECT p.id_pengguna, p.tgl_rencana_kembali, pg.nama AS nama_peminjam,
                        dp.id_barang, dp.jml_dipinjam
                 FROM peminjaman p
                 JOIN pengguna pg ON p.id_pengguna = pg.id_pengguna
                 JOIN detail_peminjaman dp ON p.id_peminjaman = dp.id_peminjaman
                 WHERE p.id_peminjaman = :id AND p.status_peminjaman IN ('Disetujui', 'Dipinjam')",
                [':id' => $idPjm]
            );

            $pdo->commit();
            AlgoritmaDAA::clearMemo();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return false;
        }
    }
}
