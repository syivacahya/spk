<?php
include 'koneksi.php';
include 'layout/header.php';

if(isset($_POST['submit'])){
    $alternatif_id = $_POST['alternatif_id'];
    $kriteria_id = $_POST['kriteria_id'];
    $nilai = $_POST['nilai'];

    // Cek apakah penilaian untuk kombinasi ini sudah ada
    $cek = mysqli_query($koneksi, "SELECT * FROM penilaian WHERE alternatif_id='$alternatif_id' AND kriteria_id='$kriteria_id'");
    if(mysqli_num_rows($cek) > 0){
        echo "<script>alert('Penilaian untuk kombinasi ini sudah ada'); window.location='penilaian.php';</script>";
    } else {
        $query = mysqli_query($koneksi, "INSERT INTO penilaian (alternatif_id, kriteria_id, nilai) VALUES ('$alternatif_id', '$kriteria_id', '$nilai')");
        if($query){
            echo "<script>alert('Penilaian berhasil ditambahkan'); window.location='penilaian.php';</script>";
        } else {
            echo "Error: ".mysqli_error($koneksi);
        }
    }
}
?>

<h2>Tambah Penilaian</h2>
<form method="POST">
    <label>Nama Kos</label><br>
    <select name="alternatif_id" required>
        <option value="">--Pilih Kos--</option>
        <?php
        $qa = mysqli_query($koneksi, "SELECT * FROM alternatif");
        while($a = mysqli_fetch_assoc($qa)){
            echo "<option value='{$a['id']}'>{$a['nama_kost']}</option>";
        }
        ?>
    </select><br><br>

    <label>Kriteria</label><br>
    <select name="kriteria_id" required>
        <option value="">--Pilih Kriteria--</option>
        <?php
        $qk = mysqli_query($koneksi, "SELECT * FROM kriteria");
        while($k = mysqli_fetch_assoc($qk)){
            echo "<option value='{$k['id']}'>{$k['nama_kriteria']}</option>";
        }
        ?>
    </select><br><br>

    <label>Nilai</label><br>
    <input type="number" step="0.01" name="nilai" required><br><br>

    <input type="submit" name="submit" value="Simpan" class="btn">
    <a href="penilaian.php" class="btn">Batal</a>
</form>

<?php include 'layout/footer.php'; ?>
