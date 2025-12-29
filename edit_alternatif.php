<?php
include 'layout/header.php';
include 'koneksi.php';

$id = $_GET['id'];
$data = mysqli_query($koneksi, "SELECT * FROM alternatif WHERE id='$id'");
$d = mysqli_fetch_assoc($data);

if(isset($_POST['submit'])){
    $nama_kos = $_POST['nama_kos'];
    $alamat = $_POST['alamat'];

    $query = mysqli_query($koneksi, "UPDATE alternatif SET nama_kos='$nama_kos', alamat='$alamat' WHERE id='$id'");
    if($query){
        echo "<script>alert('Data berhasil diubah'); window.location='alternatif.php';</script>";
    } else {
        echo "Error: ".mysqli_error($koneksi);
    }
}
?>

<div class="card">
    <h2>Edit Kos</h2>
    <form method="POST">
        <label>Nama Kos</label><br>
        <input type="text" name="nama_kos" value="<?= $d['nama_kos'] ?>" required><br><br>

        <label>Alamat</label><br>
        <input type="text" name="alamat" value="<?= $d['alamat'] ?>" required><br><br>

        <input type="submit" name="submit" value="Simpan" class="btn">
        <a href="alternatif.php" class="btn btn-danger">Batal</a>
    </form>
</div>

<?php include 'layout/footer.php'; ?>
