<?php
include 'koneksi.php';
include 'layout/header.php';

if (isset($_POST['simpan'])) {
    $nama  = $_POST['nama_kriteria'];
    $jenis = $_POST['jenis']; // cost / benefit
    $bobot = $_POST['bobot'];

    mysqli_query($conn, "
        INSERT INTO kriteria (nama_kriteria, jenis, bobot)
        VALUES ('$nama', '$jenis', '$bobot')
    ");

    echo "<script>alert('Kriteria berhasil ditambahkan');
          window.location='kriteria.php';</script>";
}
?>

<h2>Tambah Kriteria</h2>

<form method="post">
    <label>Nama Kriteria</label><br>
    <input type="text" name="nama_kriteria" required><br><br>

    <label>Jenis Kriteria</label><br>
    <select name="jenis" required>
        <option value="">-- Pilih Jenis --</option>
        <option value="benefit">Benefit</option>
        <option value="cost">Cost</option>
    </select><br><br>

    <label>Bobot</label><br>
    <input type="number" step="0.01" name="bobot" required><br><br>

    <button type="submit" name="simpan">Simpan</button>
    <a href="kriteria.php" class="btn btn-danger">Batal</a>
</form>

<?php include 'layout/footer.php'; ?>
