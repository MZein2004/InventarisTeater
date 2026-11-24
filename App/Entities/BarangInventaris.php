<?php

declare(strict_types=1);

namespace App\Entities;

use App\Algorithms\AlgoritmaDAA;
use App\Config\Database;
use Exception;

class BarangInventaris
{
    protected string $id_barang = '';
    protected string $id_kategori = '';
    protected string $nama_barang = '';
    protected string $deskripsi = '';
    protected int $jumlah_stok = 0;
    protected int $jumlah_tersedia = 0;
    protected string $kondisi = 'Baik';

    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->fill($data);
        }
    }

    public function fill(array $data): void
    {
        $this->id_barang       = trim((string)($data['id_barang'] ?? $this->id_barang));
        $this->id_kategori     = trim((string)($data['id_kategori'] ?? $this->id_kategori));
        $this->nama_barang     = trim((string)($data['nama_barang'] ?? $this->nama_barang));
        $this->deskripsi       = trim((string)($data['deskripsi'] ?? $this->deskripsi));
        $this->jumlah_stok     = (int)($data['jumlah_stok'] ?? $this->jumlah_stok);
        $this->jumlah_tersedia = (int)($data['jumlah_tersedia'] ?? $this->jumlah_stok);
        $this->kondisi         = trim((string)($data['kondisi'] ?? $this->kondisi));
    }

    public function cekKetersediaan(int $jumlahDiminta = 1): bool
    {
        return $this->jumlah_tersedia >= $jumlahDiminta && $jumlahDiminta > 0;
    }

    public function buildDeskripsiLengkap(): string
    {
        return $this->deskripsi;
    }

    public function simpanBaru(?array $file = null): bool
    {
        $foto = '';
        if ($file && !empty($file['name']) && ($file['error'] ?? 1) === UPLOAD_ERR_OK) {
            $dir = __DIR__ . '/../../assets/uploads/';
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                $foto = uniqid('item_', true) . '.' . $ext;
                move_uploaded_file($file['tmp_name'], $dir . $foto);
            }
        }

        $pdo = Database::getConnection();
        try {
            $pdo->beginTransaction();
            $idBrg = Database::generateId('barang_inventaris', 'id_barang', 'BRG');
            $idUnt = Database::generateId('unit_barang', 'id_unit', 'UNT');
            $desc  = $this->buildDeskripsiLengkap();

            Database::exec(
                "INSERT INTO barang_inventaris (id_barang, id_kategori, deskripsi, jumlah_stok, kondisi, foto)
                 VALUES (:id, :kat, :desc, :stok, :kondisi, :foto)",
                [':id' => $idBrg, ':kat' => $this->id_kategori, ':desc' => $desc, ':stok' => $this->jumlah_stok, ':kondisi' => $this->kondisi, ':foto' => $foto]
            );
            Database::exec(
                "INSERT INTO unit_barang (id_unit, id_barang, id_kategori, nama_barang, deskripsi, jumlah_stok, jumlah_tersedia, kondisi, foto)
                 VALUES (:uid, :bid, :kat, :nama, :desc, :stok, :tersedia, :kondisi, :foto)",
                [':uid' => $idUnt, ':bid' => $idBrg, ':kat' => $this->id_kategori, ':nama' => $this->nama_barang, ':desc' => $desc, ':stok' => $this->jumlah_stok, ':tersedia' => $this->jumlah_stok, ':kondisi' => $this->kondisi, ':foto' => $foto]
            );
            $pdo->commit();
            AlgoritmaDAA::clearMemo();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return false;
        }
    }

    public function update(): bool
    {
        $pdo = Database::getConnection();
        try {
            $pdo->beginTransaction();
            $desc = $this->buildDeskripsiLengkap();

            Database::exec(
                "UPDATE barang_inventaris SET id_kategori = :kat, deskripsi = :desc, jumlah_stok = :stok, kondisi = :kondisi WHERE id_barang = :id",
                [':kat' => $this->id_kategori, ':desc' => $desc, ':stok' => $this->jumlah_stok, ':kondisi' => $this->kondisi, ':id' => $this->id_barang]
            );
            Database::exec(
                "UPDATE unit_barang SET id_kategori = :kat, nama_barang = :nama, deskripsi = :desc, jumlah_stok = :stok, jumlah_tersedia = :tersedia, kondisi = :kondisi WHERE id_barang = :id",
                [':kat' => $this->id_kategori, ':nama' => $this->nama_barang, ':desc' => $desc, ':stok' => $this->jumlah_stok, ':tersedia' => $this->jumlah_tersedia, ':kondisi' => $this->kondisi, ':id' => $this->id_barang]
            );
            $pdo->commit();
            AlgoritmaDAA::clearMemo();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return false;
        }
    }

    /**
     * Tindak lanjut Admin (Aturan Bisnis #7 & Bagian 13):
     * Barang yang selesai diperbaiki dikembalikan menjadi 'Baik' dan siap dipinjam.
     */
    public function selesaiPerbaikan(string $idBarang, int $jumlahPulih = 1): bool
    {
        $pdo = Database::getConnection();
        try {
            $pdo->beginTransaction();
            Database::exec(
                "UPDATE unit_barang
                 SET jumlah_tersedia = LEAST(jumlah_stok, jumlah_tersedia + :jml), kondisi = 'Baik'
                 WHERE id_barang = :id",
                [':jml' => max(1, $jumlahPulih), ':id' => $idBarang]
            );
            Database::exec(
                "UPDATE barang_inventaris SET kondisi = 'Baik' WHERE id_barang = :id",
                [':id' => $idBarang]
            );
            $pdo->commit();
            AlgoritmaDAA::clearMemo();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return false;
        }
    }

    public function hapus(string $idBarang): bool
    {
        $pdo = Database::getConnection();
        try {
            $pdo->beginTransaction();
            Database::exec("DELETE FROM unit_barang WHERE id_barang = :id", [':id' => $idBarang]);
            Database::exec("DELETE FROM barang_inventaris WHERE id_barang = :id", [':id' => $idBarang]);
            $pdo->commit();
            AlgoritmaDAA::clearMemo();
            return true;
        } catch (Exception $e) {
            $pdo->rollBack();
            return false;
        }
    }

    public function findRowById(string $idBarang): ?array
    {
        return AlgoritmaDAA::memoize("barang_{$idBarang}", function () use ($idBarang) {
            return AlgoritmaDAA::binarySearchById($this->getAllSortedById(), $idBarang, 'id_barang');
        });
    }

    public function findById(string $idBarang): ?self
    {
        $row = $this->findRowById($idBarang);
        if (!$row) return null;

        return match ($row['id_kategori']) {
            'KAT00001' => new Kostum($row),
            'KAT00002' => new Properti($row),
            default    => new self($row),
        };
    }

    public function getAllSortedById(): array
    {
        return AlgoritmaDAA::memoize('all_barang_sorted', function () {
            return Database::fetchAll(
                "SELECT b.id_barang, b.id_kategori, b.deskripsi, b.jumlah_stok, b.kondisi, b.foto,
                        u.id_unit, COALESCE(u.nama_barang, b.id_barang) AS nama_barang,
                        COALESCE(u.jumlah_tersedia, b.jumlah_stok) AS jumlah_tersedia,
                        COALESCE(k.nama_kategori, '-') AS nama_kategori,
                        COALESCE(pjm.sedang_dipinjam, 0) AS jumlah_dipinjam
                 FROM barang_inventaris b
                 LEFT JOIN unit_barang u ON b.id_barang = u.id_barang
                 LEFT JOIN kategori k ON b.id_kategori = k.id_kategori
                 LEFT JOIN (
                     SELECT dp.id_barang, SUM(CAST(dp.jml_dipinjam AS UNSIGNED)) AS sedang_dipinjam
                     FROM detail_peminjaman dp
                     JOIN peminjaman p ON dp.id_peminjaman = p.id_peminjaman
                     WHERE p.status_peminjaman IN ('Disetujui', 'Dipinjam')
                     GROUP BY dp.id_barang
                 ) pjm ON b.id_barang = pjm.id_barang
                 ORDER BY b.id_barang ASC"
            );
        });
    }

    public function getAllFiltered(string $keyword = '', string $kategori = '', string $status = ''): array
    {
        $items = $this->getAllSortedById();
        return array_values(array_filter($items, function (array $row) use ($keyword, $kategori, $status): bool {
            if ($kategori !== '' && $row['id_kategori'] !== $kategori) return false;

            $tersedia       = (int)($row['jumlah_tersedia'] ?? 0);
            $dipinjam       = (int)($row['jumlah_dipinjam'] ?? 0);
            $dalamPerbaikan = (strcasecmp((string)($row['kondisi'] ?? ''), 'Dalam Perbaikan') === 0);

            if ($status === 'perbaikan' && !$dalamPerbaikan) return false;
            if ($status === 'dipinjam' && ($dalamPerbaikan || $dipinjam <= 0)) return false;
            if ($status === 'tersedia' && ($dalamPerbaikan || $dipinjam > 0 || $tersedia <= 0)) return false;

            if ($keyword !== '') {
                $cocokNama = stripos($row['nama_barang'], $keyword) !== false;
                $cocokDesc = stripos((string)$row['deskripsi'], $keyword) !== false;
                $cocokKode = strcasecmp($row['id_barang'], $keyword) === 0;
                if (!$cocokNama && !$cocokDesc && !$cocokKode) return false;
            }
            return true;
        }));
    }

    /* ================= CRUD KATEGORI (FR-03) ================= */

    public function getAllKategori(): array
    {
        return AlgoritmaDAA::memoize('all_kategori', function () {
            return Database::fetchAll(
                "SELECT k.id_kategori, k.nama_kategori, k.deskripsi, COUNT(b.id_barang) AS jumlah_barang
                 FROM kategori k
                 LEFT JOIN barang_inventaris b ON k.id_kategori = b.id_kategori
                 GROUP BY k.id_kategori, k.nama_kategori, k.deskripsi
                 ORDER BY k.id_kategori ASC"
            );
        });
    }

    public function simpanKategori(string $namaKategori, string $deskripsi = ''): bool
    {
        $nama = trim($namaKategori);
        if ($nama === '') return false;

        $ok = Database::exec(
            "INSERT INTO kategori (id_kategori, nama_kategori, deskripsi) VALUES (:id, :nama, :desc)",
            [
                ':id'   => Database::generateId('kategori', 'id_kategori', 'KAT'),
                ':nama' => $nama,
                ':desc' => trim($deskripsi),
            ]
        );
        if ($ok) AlgoritmaDAA::clearMemo();
        return $ok;
    }

    public function updateKategori(string $idKategori, string $namaKategori, string $deskripsi = ''): bool
    {
        $nama = trim($namaKategori);
        if ($idKategori === '' || $nama === '') return false;

        $ok = Database::exec(
            "UPDATE kategori SET nama_kategori = :nama, deskripsi = :desc WHERE id_kategori = :id",
            [
                ':nama' => $nama,
                ':desc' => trim($deskripsi),
                ':id'   => $idKategori,
            ]
        );
        if ($ok) AlgoritmaDAA::clearMemo();
        return $ok;
    }

    public function hapusKategori(string $idKategori): bool
    {
        $dipakai = Database::fetchOne("SELECT COUNT(*) AS c FROM barang_inventaris WHERE id_kategori = :id", [':id' => $idKategori]);
        if ((int)($dipakai['c'] ?? 0) > 0) {
            return false;
        }
        $ok = Database::exec("DELETE FROM kategori WHERE id_kategori = :id", [':id' => $idKategori]);
        if ($ok) AlgoritmaDAA::clearMemo();
        return $ok;
    }

    public function getStatistikDashboard(): array
    {
        return AlgoritmaDAA::memoize('stats_dashboard', function () {
            $stok      = Database::fetchOne("SELECT COALESCE(SUM(jumlah_stok), 0) AS t, COALESCE(SUM(jumlah_tersedia), 0) AS s FROM unit_barang");
            $perhatian = Database::fetchOne("SELECT COUNT(*) AS c FROM unit_barang WHERE kondisi IN ('Dalam Perbaikan', 'Rusak')");
            $total     = (int)($stok['t'] ?? 0);
            $tersedia  = (int)($stok['s'] ?? 0);
            return [
                'total'     => $total,
                'tersedia'  => $tersedia,
                'dipinjam'  => max(0, $total - $tersedia),
                'perhatian' => (int)($perhatian['c'] ?? 0),
            ];
        });
    }
}
