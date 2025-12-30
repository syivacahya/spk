<?php
include 'cek_login.php';
include 'koneksi.php';
include 'layout/header.php';

// ===============================
// CEK ID
// ===============================
if (!isset($_GET['id'])) {
    die("ID alternatif tidak ditemukan");
}

$id = $_GET['id'];

// ===============================
// AMBIL DATA
// ===============================
$q = mysqli_query($conn, "SELECT * FROM alternatif WHERE id_alternatif='$id'");
if (mysqli_num_rows($q) == 0) {
    die("Data alternatif tidak ditemukan");
}
$data = mysqli_fetch_assoc($q);

// ===============================
// PROSES UPDATE
// ===============================
if (isset($_POST['update'])) {
    $nama = $_POST['nama_kos'];

    $update = mysqli_query(
        $conn,
        "UPDATE alternatif SET nama_kos='$nama' WHERE id_alternatif='$id'"
    );

    if ($update) {
        echo "<script>alert('Alternatif berhasil diubah');window.location='alternatif.php';</script>";
    } else {
        echo mysqli_error($conn);
    }
}
?>

<div class="card">
    <h2>Edit Alternatif</h2>

    <form method="post">
        <label>Nama Kos</label><br>
        <input type="text" name="nama_kos" value="<?= $data['nama_kos'] ?>" required><br><br>

        <button type="submit" name="update" class="btn">Update</button>
        <a href="alternatif.php" class="btn btn-danger">Batal</a>
    </form>
</div>

<?php include 'layout/footer.php'; ?>
