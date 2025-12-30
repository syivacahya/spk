<?php
include 'cek_login.php';
include 'koneksi.php';
include 'layout/header.php';

$id = $_GET['id'];
$data = mysqli_query($koneksi, "SELECT * FROM penilaian WHERE id='$id'");
$d = mysqli_fetch_assoc($data);

if(isset($_POST['submit'])){
    $nilai = $_POST['nilai'];

    $query = mysqli_query($koneksi, "UPDATE penilaian SET nilai='$nilai' WHERE id='$id'");
    if($query){
        echo "<script>alert('Penilaian berhasil diubah'); window.location='penilaian.php';</script>";
    } else {
        echo "Error: ".mysqli_error($koneksi);
    }
}
?>

<h2>Edit Penilaian</h2>
<p>Nama Kos: <b><?= $d['alternatif_id'] ?></b> | Kriteria: <b><?= $d['kriteria_id'] ?></b></p>

<form method="POST">
    <label>Nilai</label><br>
    <input type="number" step="0.01" name="nilai" value="<?= $d['nilai'] ?>" required><br><br>

    <input type="submit" name="submit" value="Simpan" class="btn">
    <a href="penilaian.php" class="btn">Batal</a>
</form>

<?php include 'layout/footer.php'; ?>
