<?php
include 'koneksi.php';
include 'layout/header.php';

/* cek id */
if (!isset($_GET['id'])) {
    die("ID alternatif tidak ditemukan");
}

$id = $_GET['id'];

/* ambil data alternatif */
$q = mysqli_query($conn, "
    SELECT * FROM alternatif 
    WHERE id_alternatif='$id'
");
$d = mysqli_fetch_assoc($q);

if (!$d) {
    die("Data alternatif tidak ditemukan");
}

/* daftar kos */
$daftar_kos = [
    'Wisma Arunika',
    'Kost Madina Monochrome',
    'Kost All Stay Unsiq 2',
    'Puri Emas Kost'
];

/* update data */
if (isset($_POST['submit'])) {
    $nama = $_POST['nama_kos'];

    mysqli_query($conn, "
        UPDATE alternatif 
        SET nama_kos='$nama'
        WHERE id_alternatif='$id'
    ");

    echo "<script>alert('Data berhasil diubah');
          window.location='alternatif.php';</script>";
}
?>

<div class="card">
    <h2>Edit Alternatif Kos</h2>

    <form method="POST">
        <label>Kode Alternatif</label><br>
        <input type="text" value="<?= $d['kode'] ?>" readonly><br><br>

        <label>Nama Kos</label><br>
        <select name="nama_kos" required>
            <option value="">-- Pilih Kos --</option>
            <?php foreach ($daftar_kos as $k): ?>
                <option value="<?= $k ?>"
                    <?= ($d['nama_kos'] == $k) ? 'selected' : '' ?>>
                    <?= $k ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <button type="submit" name="submit" class="btn">Simpan</button>
        <a href="alternatif.php" class="btn btn-danger">Batal</a>
    </form>
</div>

<?php include 'layout/footer.php'; ?>
