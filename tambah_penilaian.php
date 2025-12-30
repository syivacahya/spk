<?php
include 'cek_login.php';
include 'koneksi.php';
include 'layout/header.php';

/* =============================
AMBIL ALTERNATIF
============================= */
$alternatif = [];
$q = mysqli_query($conn,"SELECT * FROM alternatif");
while($r = mysqli_fetch_assoc($q)){
    $alternatif[$r['id_alternatif']] = $r['nama_kos'];
}

/* =============================
AMBIL KRITERIA
============================= */
$kriteria = [];
$q = mysqli_query($conn,"SELECT * FROM kriteria ORDER BY id_kriteria");
while($r = mysqli_fetch_assoc($q)){
    $kriteria[$r['id_kriteria']] = $r;
}

/* =============================
SIMPAN PENILAIAN
============================= */
if(isset($_POST['simpan'])){
    $id_alt = $_POST['id_alternatif'];
    $nilai  = $_POST['nilai'];

    // hapus dulu biar tidak dobel
    mysqli_query($conn,"DELETE FROM penilaian WHERE id_alternatif='$id_alt'");

    foreach($nilai as $id_k=>$n){
        mysqli_query($conn,"
            INSERT INTO penilaian (id_alternatif,id_kriteria,nilai)
            VALUES ('$id_alt','$id_k','$n')
        ");
    }

    echo "<script>alert('Penilaian berhasil ditambahkan');location='penilaian.php';</script>";
    exit;
}
?>

<style>
.card{background:#fff;padding:20px;border-radius:6px;margin-top:15px}
.form-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:15px}
.form-group{display:flex;flex-direction:column}
.form-group label{font-weight:bold;margin-bottom:5px}
.form-group input,.form-group select{
    padding:8px;border:1px solid #ccc;border-radius:4px
}
.btn{padding:8px 14px;border-radius:4px;color:#fff;text-decoration:none;border:none}
.btn-save{background:#3498db}
.btn-cancel{background:#7f8c8d}
</style>

<div class="card">
<h2>Tambah Penilaian</h2>

<form method="post">

<div class="form-grid">

    <div class="form-group">
        <label>Nama Kos</label>
        <select name="id_alternatif" required>
            <option value="">-- pilih --</option>
            <?php foreach($alternatif as $id=>$nama){ ?>
                <option value="<?= $id ?>"><?= $nama ?></option>
            <?php } ?>
        </select>
    </div>

    <?php foreach($kriteria as $k=>$v){ ?>
    <div class="form-group">
        <label><?= $v['nama_kriteria'] ?></label>
        <input type="number" name="nilai[<?= $k ?>]" required>
    </div>
    <?php } ?>

</div>

<br>
<button type="submit" name="simpan" class="btn btn-save">Simpan</button>
<a href="penilaian.php" class="btn btn-cancel">Batal</a>

</form>
</div>

<?php include 'layout/footer.php'; ?>
