<?php
// app/Entities/Kostum.php
declare(strict_types=1);

namespace App\Entities;

class Kostum extends BarangInventaris
{
    private string $ukuran = '';
    private string $warna = '';
    private string $jenis_bahan = '';

    public function fill(array $data): void
    {
        parent::fill($data);
        $this->ukuran      = trim((string)($data['ukuran'] ?? ''));
        $this->warna       = trim((string)($data['warna'] ?? ''));
        $this->jenis_bahan = trim((string)($data['jenis_bahan'] ?? ''));
    }

    public function buildDeskripsiLengkap(): string
    {
        $spec = array_filter([
            $this->ukuran !== '' ? "Ukuran: {$this->ukuran}" : '',
            $this->warna !== '' ? "Warna: {$this->warna}" : '',
            $this->jenis_bahan !== '' ? "Bahan: {$this->jenis_bahan}" : '',
        ]);
        return (!empty($spec) ? '[' . implode(' | ', $spec) . '] ' : '') . $this->deskripsi;
    }
}
