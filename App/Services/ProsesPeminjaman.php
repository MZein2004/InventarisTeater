<?php

declare(strict_types=1);

namespace App\Services;

use App\Entities\BarangInventaris;
use App\Entities\TransaksiPeminjaman;

class ProsesPeminjaman
{
    public function memvalidasiKelengkapanData(array $data): array
    {
        $err = [];
        if (trim((string)($data['id_barang'] ?? '')) === '') $err[] = 'Barang wajib dipilih.';
        if ((int)($data['jml_dipinjam'] ?? 0) < 1) $err[] = 'Jumlah minimal 1.';
        $p = trim((string)($data['tgl_rencana_pinjam'] ?? ''));
        $k = trim((string)($data['tgl_rencana_kembali'] ?? ''));
        if ($p === '' || $k === '' || $k < $p) $err[] = 'Rentang tanggal tidak valid.';
        return $err;
    }

    public function memeriksaKetersediaanBarang(string $idBarang): ?BarangInventaris
    {
        return (new BarangInventaris())->findById($idBarang);
    }

    public function memeriksaKecukupanStok(BarangInventaris $barang, int $jumlah): bool
    {
        return $barang->cekKetersediaan($jumlah);
    }

    public function menyimpanPermohonan(string $uid, string $bid, int $jml, string $pinjam, string $kembali): bool
    {
        return (new TransaksiPeminjaman())->buatTransaksi($uid, $bid, $jml, $pinjam, $kembali);
    }

    public function prosesPengajuan(string $idPengguna, array $input): array
    {
        if ($errors = $this->memvalidasiKelengkapanData($input)) {
            return ['success' => false, 'message' => implode(' ', $errors)];
        }
        $barang = $this->memeriksaKetersediaanBarang(trim((string)$input['id_barang']));
        if (!$barang || !$this->memeriksaKecukupanStok($barang, (int)$input['jml_dipinjam'])) {
            return ['success' => false, 'message' => 'Stok barang tidak tersedia atau tidak mencukupi.'];
        }
        $ok = $this->menyimpanPermohonan($idPengguna, trim((string)$input['id_barang']), (int)$input['jml_dipinjam'], trim((string)$input['tgl_rencana_pinjam']), trim((string)$input['tgl_rencana_kembali']));
        return ['success' => $ok, 'message' => $ok ? 'Pengajuan berhasil.' : 'Gagal menyimpan transaksi.'];
    }
}
