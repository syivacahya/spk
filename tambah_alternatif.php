<?php
include 'koneksi.php';
include 'layout/header.php';

if (isset($_POST['simpan'])) {
    $nama = $_POST['nama_kos'];

    $query = mysqli_query($conn,
        "INSERT INTO alternatif (nama_kos) VALUES ('$nama')"
    );

    if ($query) {
        echo "<script>alert('Alternatif ditambahkan');window.location='alternatif.php';</script>";
    } else {
        echo mysqli_error($conn);
    }
}
?>

<div class="card">
    <h2>Tambah Alternatif</h2>

    <form method="post">
        <label>Nama Kos</label><br>
        <input type="text" name="nama_kos" required><br><br>

        <button type="submit" name="simpan" class="btn">Simpan</button>
        <a href="alternatif.php" class="btn btn-danger">Batal</a>
    </form>
</div>

<?php include 'layout/footer.php'; ?>
