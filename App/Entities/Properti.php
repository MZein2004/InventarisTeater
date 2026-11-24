<?php
// app/Entities/Properti.php
declare(strict_types=1);

namespace App\Entities;

class Properti extends BarangInventaris
{
    private string $dimensi = '';
    private float $berat = 0.0;
    private string $kategori_set = '';

    public function fill(array $data): void
    {
        parent::fill($data);
        $this->dimensi      = trim((string)($data['dimensi'] ?? ''));
        $this->berat        = (float)($data['berat'] ?? 0.0);
        $this->kategori_set = trim((string)($data['kategori_set'] ?? ''));
    }

    public function buildDeskripsiLengkap(): string
    {
        $spec = array_filter([
            $this->dimensi !== '' ? "Dimensi: {$this->dimensi}" : '',
            $this->berat > 0 ? "Berat: {$this->berat} kg" : '',
            $this->kategori_set !== '' ? "Set: {$this->kategori_set}" : '',
        ]);
        return (!empty($spec) ? '[' . implode(' | ', $spec) . '] ' : '') . $this->deskripsi;
    }
}
