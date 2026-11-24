<?php
$pageTitle    = 'Proses Pengembalian';
$pageSubtitle = 'Catat pengembalian dan kondisi barang.';
$activeMenu   = 'pengembalian';
$activeLoans  = $activeLoans ?? [];

require __DIR__ . '/../layouts/header.php';
?>

<section class="section-block">
    <form action="<?= $baseUrl ?>/index.php?action=pengembalian-store" method="post" class="card form-card" id="formPengembalian">
        <div class="form-row">
            <label for="id_peminjaman" class="form-label">Pilih Peminjaman</label>
            <select id="id_peminjaman" name="id_peminjaman" class="form-control" required>
                <option value="">-- Pilih Peminjaman Aktif --</option>
                <?php foreach ($activeLoans as $item): ?>
                    <option value="<?= htmlspecialchars($item['id_peminjaman'], ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars($item['id_peminjaman'] . ' - ' . $item['nama_peminjam'] . ' - ' . $item['nama_barang'] . ' (Jatuh tempo: ' . $item['tgl_rencana_kembali'] . ')', ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-row">
            <label for="tgl_kembali" class="form-label">Tanggal Kembali</label>
            <input type="date" id="tgl_kembali" name="tgl_kembali" class="form-control" required>
        </div>

        <div class="form-row">
            <label for="kondisi_barang" class="form-label">Kondisi Barang</label>
            <select id="kondisi_barang" name="kondisi_barang" class="form-control" required>
                <option value="Baik">Baik</option>
                <option value="Rusak Ringan">Rusak Ringan</option>
                <option value="Rusak Berat">Rusak Berat</option>
            </select>
        </div>

        <div class="form-row">
            <label for="catatan_kondisi" class="form-label">Catatan</label>
            <textarea id="catatan_kondisi" name="catatan_kondisi" class="form-control" rows="3" placeholder="Barang dikembalikan dalam kondisi baik." required></textarea>
        </div>

        <fieldset class="form-fieldset">
            <legend>Denda (Opsional jika terlambat / rusak)</legend>
            <div class="form-grid">
                <div class="form-field">
                    <label for="jml_biaya" class="form-label">Nominal Denda (Rp)</label>
                    <input type="number" id="jml_biaya" name="jml_biaya" class="form-control" min="0" step="1000" value="0">
                </div>
                <div class="form-field">
                    <label for="alasan" class="form-label">Alasan Denda</label>
                    <input type="text" id="alasan" name="alasan" class="form-control" placeholder="Keterlambatan / kerusakan">
                </div>
            </div>
        </fieldset>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Simpan Pengembalian</button>
        </div>
    </form>
</section>

<?php require __DIR__ . '/../layouts/footer.php'; ?>