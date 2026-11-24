<?php

declare(strict_types=1);

namespace App\Entities;

use App\Algorithms\AlgoritmaDAA;
use App\Config\Database;
use DateTime;
use Exception;

class TransaksiPeminjaman
{
    public function buatTransaksi(string $idPengguna, string $idBarang, int $jumlah, string $tglPinjam, string $tglKembali): bool
    {
        $pdo = Database::getConnection();
        try {
            $pdo->beginTransaction();
            $idTransaksi = Database::generateId('peminjaman', 'id_peminjaman', 'PJM');

            Database::exec(
                "INSERT INTO peminjaman (id_peminjaman, id_pengguna, tgl_pengajuan, tgl_rencana_pinjam, tgl_rencana_kembali, status_peminjaman)
                 VALUES (:id, :uid, :ajukan, :pinjam, :kembali, 'Menunggu')",
                [':id' => $idTransaksi, ':uid' => $idPengguna, ':ajukan' => date('Y-m-d'), ':pinjam' => $tglPinjam, ':kembali' => $tglKembali]
            );
            (new DetailPeminjaman())->simpanDetail($idTransaksi, $idBarang, $jumlah);

            $pdo->commit();
            AlgoritmaDAA::clearMemo();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return false;
        }
    }

    /**
     * Siklus status sesuai ADBO: Menunggu -> Disetujui / Ditolak -> Dipinjam (Penyerahan) -> Selesai
     */
    public function updateStatus(string $idTransaksi, string $statusBaru): bool
    {
        if (!in_array($statusBaru, ['Disetujui', 'Dipinjam', 'Ditolak', 'Selesai'], true)) {
            return false;
        }

        $pdo = Database::getConnection();
        try {
            $pdo->beginTransaction();

            // Aturan Bisnis #10: Cek ulang ketersediaan stok saat persetujuan
            if ($statusBaru === 'Disetujui') {
                $dtl = Database::fetchOne(
                    "SELECT dp.id_barang, dp.jml_dipinjam, u.jumlah_tersedia
                     FROM detail_peminjaman dp JOIN unit_barang u ON dp.id_barang = u.id_barang
                     WHERE dp.id_peminjaman = :id",
                    [':id' => $idTransaksi]
                );
                if (!$dtl || (int)$dtl['jumlah_tersedia'] < (int)$dtl['jml_dipinjam']) {
                    throw new Exception('Stok tidak mencukupi.');
                }
                Database::exec(
                    "UPDATE unit_barang SET jumlah_tersedia = jumlah_tersedia - :jml WHERE id_barang = :bid",
                    [':jml' => (int)$dtl['jml_dipinjam'], ':bid' => $dtl['id_barang']]
                );
            }

            // FR-09: Konfirmasi penyerahan fisik barang hanya untuk permohonan yang sudah Disetujui
            if ($statusBaru === 'Dipinjam') {
                $cek = Database::fetchOne("SELECT status_peminjaman FROM peminjaman WHERE id_peminjaman = :id", [':id' => $idTransaksi]);
                if (!$cek || $cek['status_peminjaman'] !== 'Disetujui') {
                    throw new Exception('Barang hanya dapat diserahkan jika permohonan berstatus Disetujui.');
                }
            }

            Database::exec(
                "UPDATE peminjaman SET status_peminjaman = :st WHERE id_peminjaman = :id",
                [':st' => $statusBaru, ':id' => $idTransaksi]
            );

            $pdo->commit();
            AlgoritmaDAA::clearMemo();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return false;
        }
    }

    public function hitungKeterlambatan(string $tglRencana, string $tglAktual): int
    {
        $r = new DateTime($tglRencana);
        $a = new DateTime($tglAktual);
        return ($a <= $r) ? 0 : (int)$r->diff($a)->days;
    }

    public function getAll(?string $idPengguna = null, string $status = ''): array
    {
        $where  = ['1=1'];
        $params = [];
        if ($idPengguna !== null) {
            $where[] = 'id_pengguna = :uid';
            $params[':uid'] = $idPengguna;
        }
        if ($status !== '') {
            $where[] = 'status_peminjaman = :st';
            $params[':st'] = $status;
        }

        $subPeminjaman = "SELECT * FROM peminjaman WHERE " . implode(' AND ', $where);

        return Database::fetchAll(
            "SELECT p.id_peminjaman, p.tgl_pengajuan, p.tgl_rencana_pinjam, p.tgl_rencana_kembali,
                    p.status_peminjaman, pg.nama AS nama_peminjam,
                    COALESCE(u.nama_barang, dp.id_barang) AS nama_barang,
                    COALESCE(dp.jml_dipinjam, '0') AS jml_dipinjam
             FROM ({$subPeminjaman}) p
             JOIN pengguna pg ON p.id_pengguna = pg.id_pengguna
             LEFT JOIN detail_peminjaman dp ON p.id_peminjaman = dp.id_peminjaman
             LEFT JOIN unit_barang u ON dp.id_barang = u.id_barang
             ORDER BY p.id_peminjaman DESC",
            $params
        );
    }

    public function getTerbaru(int $limit = 5): array
    {
        return Database::fetchAll(
            "SELECT p.id_peminjaman, p.tgl_rencana_pinjam, p.status_peminjaman,
                    pg.nama AS nama_peminjam, COALESCE(u.nama_barang, '-') AS nama_barang
             FROM (SELECT * FROM peminjaman ORDER BY tgl_pengajuan DESC, id_peminjaman DESC LIMIT {$limit}) p
             JOIN pengguna pg ON p.id_pengguna = pg.id_pengguna
             LEFT JOIN detail_peminjaman dp ON p.id_peminjaman = dp.id_peminjaman
             LEFT JOIN unit_barang u ON dp.id_barang = u.id_barang"
        );
    }

    public function getStatistikPeminjam(string $idPengguna): array
    {
        $row = Database::fetchOne(
            "SELECT SUM(status_peminjaman IN ('Disetujui', 'Dipinjam')) AS aktif,
                    SUM(status_peminjaman = 'Menunggu') AS menunggu,
                    COUNT(*) AS riwayat
             FROM peminjaman WHERE id_pengguna = :id",
            [':id' => $idPengguna]
        );
        return ['aktif' => (int)($row['aktif'] ?? 0), 'menunggu' => (int)($row['menunggu'] ?? 0), 'riwayat' => (int)($row['riwayat'] ?? 0)];
    }

    public function getPeminjamanAktif(): array
    {
        return Database::fetchAll(
            "SELECT p.id_peminjaman, p.tgl_rencana_kembali, p.status_peminjaman,
                    pg.nama AS nama_peminjam,
                    COALESCE(u.nama_barang, dp.id_barang) AS nama_barang,
                    COALESCE(dp.jml_dipinjam, '1') AS jml_dipinjam
             FROM (SELECT * FROM peminjaman WHERE status_peminjaman IN ('Disetujui', 'Dipinjam')) p
             JOIN pengguna pg ON p.id_pengguna = pg.id_pengguna
             LEFT JOIN detail_peminjaman dp ON p.id_peminjaman = dp.id_peminjaman
             LEFT JOIN unit_barang u ON dp.id_barang = u.id_barang
             ORDER BY p.tgl_rencana_kembali ASC"
        );
    }

    public function getLaporan(string $mulai = '', string $selesai = ''): array
    {
        $filter = ($mulai !== '' && $selesai !== '') ? ' AND p.tgl_rencana_pinjam BETWEEN :mulai AND :selesai' : '';
        $params = ($filter !== '') ? [':mulai' => $mulai, ':selesai' => $selesai] : [];

        return Database::fetchAll(
            "SELECT COALESCE(u.nama_barang, b.id_barang) AS nama_barang,
                    COALESCE(k.nama_kategori, '-') AS nama_kategori,
                    COUNT(p.id_peminjaman) AS total_peminjaman,
                    CASE
                        WHEN b.kondisi = 'Dalam Perbaikan' THEN 'Dalam Perbaikan'
                        WHEN COALESCE(u.jumlah_tersedia, b.jumlah_stok) > 0 THEN 'Aktif'
                        ELSE 'Kosong'
                    END AS status
             FROM barang_inventaris b
             LEFT JOIN unit_barang u ON b.id_barang = u.id_barang
             LEFT JOIN kategori k ON b.id_kategori = k.id_kategori
             LEFT JOIN detail_peminjaman dp ON b.id_barang = dp.id_barang
             LEFT JOIN peminjaman p ON dp.id_peminjaman = p.id_peminjaman {$filter}
             GROUP BY b.id_barang, u.nama_barang, k.nama_kategori, u.jumlah_tersedia, b.jumlah_stok, b.kondisi
             ORDER BY total_peminjaman DESC",
            $params
        );
    }
}
