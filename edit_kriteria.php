<?php
include 'koneksi.php';
include 'layout/header.php';

$id = $_GET['id'];
$data = mysqli_query($koneksi, "SELECT * FROM kriteria WHERE id='$id'");
$d = mysqli_fetch_assoc($data);

if(isset($_POST['submit'])){
    $nama = $_POST['nama_kriteria'];

    // otomatis tentukan tipe
    if($nama=='Harga Sewa' || $nama=='Jarak ke Kampus'){
        $tipe = 'cost';
    } else {
        $tipe = 'benefit';
    }

    $bobot = $_POST['bobot'];

    $query = mysqli_query($koneksi, "UPDATE kriteria SET nama_kriteria='$nama', tipe='$tipe', bobot='$bobot' WHERE id='$id'");
    if($query){
        echo "<script>alert('Kriteria berhasil diubah'); window.location='kriteria.php';</script>";
    } else {
        echo "Error: ".mysqli_error($koneksi);
    }
}
?>

<h2>Edit Kriteria</h2>
<form method="POST">
    <label>Nama Kriteria</label><br>
    <select name="nama_kriteria" required>
        <option value="Harga Sewa" <?= $d['nama_kriteria']=='Harga Sewa'?'selected':'' ?>>Harga Sewa</option>
        <option value="Jarak ke Kampus" <?= $d['nama_kriteria']=='Jarak ke Kampus'?'selected':'' ?>>Jarak ke Kampus</option>
        <option value="Fasilitas" <?= $d['nama_kriteria']=='Fasilitas'?'selected':'' ?>>Fasilitas</option>
        <option value="Luas Kamar" <?= $d['nama_kriteria']=='Luas Kamar'?'selected':'' ?>>Luas Kamar</option>
    </select><br><br>

    <label>Bobot</label><br>
    <input type="number" step="0.01" name="bobot" value="<?= $d['bobot'] ?>" required><br><br>

    <input type="submit" name="submit" value="Simpan" class="btn">
    <a href="kriteria.php" class="btn">Batal</a>
</form>

<?php include 'layout/footer.php'; ?>
