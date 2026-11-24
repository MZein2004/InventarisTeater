<?php

declare(strict_types=1);

namespace App\Algorithms;

class AlgoritmaDAA
{
    /** Tabel Memoization untuk mencegah komputasi/query berulang (DP Top-Down) */
    private static array $memo = [];

    /**
     * 1. Dynamic Programming - Memoization: O(1) lookup jika hasil sudah ada di memori.
     */
    public static function memoize(string $key, callable $resolver): mixed
    {
        if (!array_key_exists($key, self::$memo)) {
            self::$memo[$key] = $resolver();
        }
        return self::$memo[$key];
    }

    public static function clearMemo(): void
    {
        self::$memo = [];
    }

    /**
     * 2. Hash Indexing: Ubah pencarian array O(n) menjadi akses langsung O(1).
     */
    public static function indexByKey(array $items, string $key): array
    {
        return array_column($items, null, $key);
    }

    /**
     * 3. Divide & Conquer - Binary Search Rekursif: T(n) = T(n/2) + O(1) -> Theta(log n).
     * Prasyarat: $sortedItems sudah terurut menaik berdasarkan $key.
     */
    public static function binarySearchById(
        array $sortedItems,
        string $targetId,
        string $key = 'id_barang',
        int $low = 0,
        ?int $high = null
    ): ?array {
        $high ??= count($sortedItems) - 1;
        if ($low > $high) {
            return null; // Basic Step: tidak ditemukan
        }

        $mid = intdiv($low + $high, 2);
        $cmp = strcmp((string)$sortedItems[$mid][$key], $targetId);

        if ($cmp === 0) {
            return $sortedItems[$mid]; // Basic Step: ditemukan
        }
        return ($cmp < 0)
            ? self::binarySearchById($sortedItems, $targetId, $key, $mid + 1, $high)
            : self::binarySearchById($sortedItems, $targetId, $key, $low, $mid - 1);
    }

    /**
     * 4. Dynamic Programming - Tabulation Bottom-Up (Edit Distance / Levenshtein).
     * Kompleksitas Waktu: O(m * n), Kompleksitas Ruang: O(n) (optimasi 2 baris).
     */
    public static function editDistance(string $s1, string $s2): int
    {
        $s1 = mb_strtolower(trim($s1));
        $s2 = mb_strtolower(trim($s2));
        $m  = mb_strlen($s1);
        $n  = mb_strlen($s2);

        $prev = range(0, $n);
        for ($i = 1; $i <= $m; $i++) {
            $curr = [$i];
            $c1   = mb_substr($s1, $i - 1, 1);
            for ($j = 1; $j <= $n; $j++) {
                $cost     = ($c1 === mb_substr($s2, $j - 1, 1)) ? 0 : 1;
                $curr[$j] = min(
                    $prev[$j] + 1,          // hapus
                    $curr[$j - 1] + 1,      // sisip
                    $prev[$j - 1] + $cost   // ganti
                );
            }
            $prev = $curr;
        }
        return $prev[$n];
    }

    /**
     * Mencari saran nama barang terdekat jika hasil pencarian katalog kosong (typo).
     */
    public static function saranTerdekat(string $keyword, array $items, string $field = 'nama_barang', int $batas = 3): ?string
    {
        $keyword = trim($keyword);
        if ($keyword === '' || empty($items)) {
            return null;
        }

        $terbaik = null;
        $minJarak = $batas + 1;

        foreach ($items as $item) {
            $nama  = (string)($item[$field] ?? '');
            $jarak = self::editDistance($keyword, $nama);
            if ($jarak < $minJarak) {
                $minJarak = $jarak;
                $terbaik  = $nama;
            }
        }
        return $terbaik;
    }
}
