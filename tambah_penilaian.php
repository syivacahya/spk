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
<div id="form" class="form-card" style="<?= $edit_id ? '' : 'display:none' ?>">
    <form method="post">

        <div class="form-grid">

            <div class="form-group">
                <label>Nama Kos</label>
                <select name="id" required <?= $edit_id ? 'readonly' : '' ?>>
                    <option value="">-- pilih --</option>
                    <?php foreach($alternatif as $id=>$n){ ?>
                        <option value="<?= $id ?>" <?= $edit_id==$id?'selected':'' ?>>
                            <?= $n ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <?php foreach($kriteria as $k=>$v){ ?>
            <div class="form-group">
                <label><?= $v['nama_kriteria'] ?></label>
                <input type="number"
                    name="nilai[<?= $k ?>]"
                    value="<?= $nilai_edit[$k] ?? '' ?>"
                    required>
            </div>
            <?php } ?>

        </div>

        <div class="form-actions">
            <button type="submit" name="simpan">Simpan</button>
        </div>
        <a href="penilaian.php" class="btn-cancel">Batal</a>
      
    </form>
</div>

<?php include 'layout/footer.php'; ?>
