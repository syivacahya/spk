<?php
include 'koneksi.php';
include 'layout/header.php';

$daftar_kriteria = [
    'Jarak ke Kampus' => 'cost',
    'Harga' => 'cost',
    'Luas Kamar' => 'benefit',
    'Fasilitas' => 'benefit'
];

if (isset($_POST['simpan'])) {
    $nama  = $_POST['nama_kriteria'];
    $bobot = $_POST['bobot'];
    $jenis = $daftar_kriteria[$nama];

    mysqli_query($conn, "
        INSERT INTO kriteria (nama_kriteria, jenis, bobot)
        VALUES ('$nama', '$jenis', '$bobot')
    ");

    echo "<script>alert('Kriteria ditambahkan');window.location='kriteria.php';</script>";
}
?>

<h2>Tambah Kriteria</h2>

<form method="post">
    <label>Nama Kriteria</label><br>
    <select name="nama_kriteria" required>
        <option value="">-- Pilih Kriteria --</option>
        <?php foreach ($daftar_kriteria as $k => $v): ?>
            <option value="<?= $k ?>"><?= $k ?></option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Bobot</label><br>
    <input type="number" step="0.01" name="bobot" required><br><br>

    <button type="submit" name="simpan">Simpan</button>
</form>

<?php include 'layout/footer.php'; ?>
