<?php
include 'cek_login.php';
include 'koneksi.php';
include 'layout/header.php';

$id_alt = $_GET['id'];

/* =============================
AMBIL DATA ALTERNATIF
============================= */
$alt = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT * FROM alternatif WHERE id_alternatif='$id_alt'")
);

/* =============================
AMBIL KRITERIA
============================= */
$kriteria = [];
$q = mysqli_query($conn,"SELECT * FROM kriteria ORDER BY id_kriteria");
while($r=mysqli_fetch_assoc($q)){
    $kriteria[$r['id_kriteria']] = $r;
}

/* =============================
AMBIL NILAI LAMA
============================= */
$nilai_lama = [];
$q = mysqli_query($conn,"
    SELECT * FROM penilaian WHERE id_alternatif='$id_alt'
");
while($r=mysqli_fetch_assoc($q)){
    $nilai_lama[$r['id_kriteria']] = $r['nilai'];
}

/* =============================
UPDATE PENILAIAN
============================= */
if(isset($_POST['simpan'])){
    $nilai = $_POST['nilai'];

    mysqli_query($conn,"DELETE FROM penilaian WHERE id_alternatif='$id_alt'");

    foreach($nilai as $id_k=>$n){
        mysqli_query($conn,"
            INSERT INTO penilaian (id_alternatif,id_kriteria,nilai)
            VALUES ('$id_alt','$id_k','$n')
        ");
    }

    echo "<script>alert('Penilaian berhasil diubah');location='penilaian.php';</script>";
    exit;
}
?>

<style>
.card{background:#fff;padding:20px;border-radius:6px;margin-top:15px}
.form-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:15px}
.form-group{display:flex;flex-direction:column}
.form-group label{font-weight:bold;margin-bottom:5px}
.form-group input{
    padding:8px;border:1px solid #ccc;border-radius:4px
}
.btn{padding:8px 14px;border-radius:4px;color:#fff;text-decoration:none;border:none}
.btn-save{background:#3498db}
.btn-cancel{background:#7f8c8d}
</style>

<div class="card">
<h2>Edit Penilaian</h2>
<p><b>Nama Kos:</b> <?= $alt['nama_kos'] ?></p>

<form method="post">

<div class="form-grid">
<?php foreach($kriteria as $k=>$v){ ?>
    <div class="form-group">
        <label><?= $v['nama_kriteria'] ?></label>
        <input type="number"
               name="nilai[<?= $k ?>]"
               value="<?= $nilai_lama[$k] ?? '' ?>"
               required>
    </div>
<?php } ?>
</div>

<br>
<button type="submit" name="simpan" class="btn btn-save">Simpan</button>
<a href="penilaian.php" class="btn btn-cancel">Batal</a>

</form>
</div>

<?php include 'layout/footer.php'; ?>
