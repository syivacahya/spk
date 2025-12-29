<?php
include 'layout/header.php';
include 'koneksi.php';

// Ambil nomor terakhir untuk kode dan nama otomatis
$q = mysqli_query($conn, "SELECT kode FROM alternatif ORDER BY id DESC LIMIT 1");
$last = mysqli_fetch_assoc($q);

if($last){
    $num = (int)substr($last['kode'], 1) + 1;
} else {
    $num = 1;
}

$newKode = "A".$num;
$newNama = "Alternatif ".$num; // Nama kos otomatis, misal "Alternatif 1"

// Simpan ke database saat submit
if(isset($_POST['submit'])){
    $query = mysqli_query($conn, "INSERT INTO alternatif (kode, nama_kos) VALUES ('$newKode', '$newNama')");
    if($query){
        echo "<script>alert('Alternatif berhasil ditambahkan'); window.location='alternatif.php';</script>";
    } else {
        echo "Error: ".mysqli_error($koneksi);
    }
}
?>

<div class="card">
    <h2>Tambah Alternatif Kos</h2>
    <form method="POST">
        <label>Kode Kos</label><br>
        <input type="text" name="kode" value="<?= $newKode ?>" readonly><br><br>

        <label>Nama Kos</label><br>
        <input type="text" name="nama_kos" value="<?= $newNama ?>" readonly><br><br>

        <input type="submit" name="submit" value="Simpan" class="btn">
        <a href="alternatif.php" class="btn btn-danger">Batal</a>
    </form>
</div>

<?php include 'layout/footer.php'; ?>
