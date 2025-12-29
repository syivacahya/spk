<?php
include 'koneksi.php';
include 'layout/header.php';

/* ambil kode terakhir */
$q = mysqli_query($conn,"
    SELECT kode FROM alternatif
    ORDER BY id_alternatif DESC LIMIT 1
");
$last = mysqli_fetch_assoc($q);
$num  = $last ? (int)substr($last['kode'],1)+1 : 1;
$newKode = "A".$num;

/* daftar kos FIX */
$daftar_kos = [
    'Wisma Arunika',
    'Kost Madina Monochrome',
    'Kost All Stay Unsiq 2',
    'Puri Emas Kost'
];

/* simpan */
if(isset($_POST['simpan'])){
    $kode = $_POST['kode'];
    $nama = $_POST['nama_kos'];

    mysqli_query($conn,"
        INSERT INTO alternatif (kode, nama_kos)
        VALUES ('$kode','$nama')
    ");

    echo "<script>alert('Alternatif berhasil ditambahkan');
          window.location='alternatif.php';</script>";
}
?>

<div class="card">
<h2>Tambah Alternatif Kos</h2>

<form method="POST">
    <label>Kode Alternatif</label><br>
    <input type="text" name="kode" value="<?= $newKode ?>" readonly><br><br>

    <label>Nama Kos</label><br>
    <select name="nama_kos" required>
        <option value="">-- Pilih Kos --</option>
        <?php foreach($daftar_kos as $k): ?>
            <option value="<?= $k ?>"><?= $k ?></option>
        <?php endforeach; ?>
    </select><br><br>

    <button type="submit" name="simpan" class="btn">Simpan</button>
    <a href="alternatif.php" class="btn btn-danger">Batal</a>
</form>
</div>

<?php include 'layout/footer.php'; ?>
