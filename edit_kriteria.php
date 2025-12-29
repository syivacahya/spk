<?php
include 'koneksi.php';
include 'layout/header.php';

// =======================
// CEK ID KRITERIA
// =======================
if (!isset($_GET['id_kriteria'])) {
    die("ID kriteria tidak ditemukan");
}

$id = $_GET['id_kriteria'];

// =======================
// AMBIL DATA KRITERIA
// =======================
$query = mysqli_query($conn, "SELECT * FROM kriteria WHERE id_kriteria='$id'");
if (mysqli_num_rows($query) == 0) {
    die("Data kriteria tidak ditemukan");
}
$data = mysqli_fetch_assoc($query);

// =======================
// PROSES UPDATE
// =======================
if (isset($_POST['submit'])) {
    $nama  = $_POST['nama_kriteria'];
    $jenis = $_POST['jenis'];
    $bobot = $_POST['bobot'];

    $update = mysqli_query($conn, "
        UPDATE kriteria 
        SET nama_kriteria='$nama', 
            jenis='$jenis', 
            bobot='$bobot'
        WHERE id_kriteria='$id'
    ");

    if ($update) {
        echo "<script>
                alert('Kriteria berhasil diubah');
                window.location='kriteria.php';
              </script>";
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<div class="card">
    <h2>Edit Kriteria</h2>

    <form method="POST">
        <label>Nama Kriteria</label><br>
        <input 
            type="text" 
            name="nama_kriteria" 
            value="<?= $data['nama_kriteria'] ?>" 
            required
        ><br><br>

        <label>Jenis Kriteria</label><br>
        <select name="jenis" required>
            <option value="benefit" <?= $data['jenis']=='benefit'?'selected':'' ?>>Benefit</option>
            <option value="cost" <?= $data['jenis']=='cost'?'selected':'' ?>>Cost</option>
        </select><br><br>

        <label>Bobot</label><br>
        <input 
            type="number" 
            step="0.01" 
            name="bobot" 
            value="<?= $data['bobot'] ?>" 
            required
        ><br><br>

        <button type="submit" name="submit" class="btn">Simpan</button>
        <a href="kriteria.php" class="btn btn-danger">Batal</a>
    </form>
</div>

<?php include 'layout/footer.php'; ?>
