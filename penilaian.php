<?php
    include 'koneksi.php';
    include 'layout/header.php';

    /* =====================================================
    HAPUS PENILAIAN
    ===================================================== */
    if (isset($_GET['hapus'])) {
        $id = $_GET['hapus'];
        mysqli_query($conn, "DELETE FROM penilaian WHERE id_alternatif='$id'");
        echo "<script>alert('Penilaian berhasil dihapus');location='penilaian.php';</script>";
        exit;
    }

    /* =====================================================
    SIMPAN / UPDATE PENILAIAN
    ===================================================== */
    if (isset($_POST['simpan'])) {
        $id_alt = $_POST['id'];
        $nilai  = $_POST['nilai'];

        mysqli_query($conn, "DELETE FROM penilaian WHERE id_alternatif='$id_alt'");
        foreach ($nilai as $id_k => $n) {
            mysqli_query($conn, "
                INSERT INTO penilaian (id_alternatif,id_kriteria,nilai)
                VALUES ('$id_alt','$id_k','$n')
            ");
        }

        echo "<script>alert('Penilaian berhasil disimpan');location='penilaian.php';</script>";
        exit;
    }

    /* =====================================================
    DATA ALTERNATIF & KRITERIA
    ===================================================== */
    $alternatif = [];
    $q = mysqli_query($conn,"SELECT * FROM alternatif");
    while($r=mysqli_fetch_assoc($q)) $alternatif[$r['id_alternatif']]=$r['nama_kos'];

    $kriteria = [];
    $q = mysqli_query($conn,"SELECT * FROM kriteria");
    while($r=mysqli_fetch_assoc($q)){
        $kriteria[$r['id_kriteria']] = $r;
    }

    /* =====================================================
    EDIT PENILAIAN
    ===================================================== */
    $edit_id = null;
    $nilai_edit = [];
    if (isset($_GET['edit'])) {
        $edit_id = $_GET['edit'];
        $q = mysqli_query($conn,"SELECT * FROM penilaian WHERE id_alternatif='$edit_id'");
        while($r=mysqli_fetch_assoc($q)){
            $nilai_edit[$r['id_kriteria']] = $r['nilai'];
        }
    }

    /* =====================================================
    DATA PENILAIAN (MATRIX)
    ===================================================== */
    $data_penilaian = [];
    $q = mysqli_query($conn,"
        SELECT a.id_alternatif,a.nama_kos,k.id_kriteria,p.nilai
        FROM penilaian p
        JOIN alternatif a ON p.id_alternatif=a.id_alternatif
        JOIN kriteria k ON p.id_kriteria=k.id_kriteria
    ");
    while($r=mysqli_fetch_assoc($q)){
        $data_penilaian[$r['id_alternatif']]['nama']=$r['nama_kos'];
        $data_penilaian[$r['id_alternatif']]['nilai'][$r['id_kriteria']]=$r['nilai'];
    }

    /* =====================================================
    MOORA
    ===================================================== */

    $nilai_all = [];

    foreach ($data_penilaian as $id => $a) {
        $lengkap = true;

        foreach ($kriteria as $k => $v) {
            if (!isset($a['nilai'][$k])) {
                $lengkap = false;
                break;
            }
        }

        if ($lengkap) {
            foreach ($kriteria as $k => $v) {
                $nilai_all[$id][$k] = $a['nilai'][$k];
            }
        }
    }


    $normalisasi=[];
    foreach($kriteria as $k=>$v){
        $sum=0;
        foreach($nilai_all as $a) $sum+=pow($a[$k]??0,2);
        $akar=sqrt($sum);
        if($akar==0) continue;
        foreach($nilai_all as $id=>$a)
            $normalisasi[$id][$k]=($a[$k]??0)/$akar;
    }

    $skor=[];
    foreach($normalisasi as $id=>$a){
        $b=0;$c=0;
        foreach($a as $k=>$n){
            $v=$n*$kriteria[$k]['bobot'];
            if($kriteria[$k]['jenis']=='benefit') $b+=$v;
            else $c+=$v;
        }
        $skor[$id]=$b-$c;
    }
    arsort($skor);
?>

<style>
    body{font-family:Arial;background:#f4f6f9}
    table{width:100%;border-collapse:collapse;margin-top:10px}
    th,td{border:1px solid #ddd;padding:8px;text-align:center}
    th{background:#3498db;color:#fff}
    .btn{padding:6px 10px;border-radius:4px;color:#fff;text-decoration:none}
    .btn-add{background:#3498db}
    .btn-edit{background:#f39c12}
    .btn-del{background:#e74c3c}
    .card{background:#fff;padding:20px;border-radius:6px;margin-top:15px}
    .form-card {
        background: #fff;
        padding: 20px;
        border-radius: 6px;
        margin-top: 15px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        font-weight: bold;
        margin-bottom: 5px;
    }

    .form-group input,
    .form-group select {
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    .form-actions {
        margin-top: 20px;
    }

    .form-actions button {
        padding: 8px 16px;
        background: #3498db;
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .form-actions button:hover {
        background: #217dbb;
    }
</style>

<h2>Data Penilaian</h2>
<a href="#" id="btnTambah" class="btn btn-add">+ Tambah Penilaian</a>

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

    </form>
</div>


<h3>Tabel Penilaian</h3>
<table>
<tr>
    <th>Alternatif</th>
    <?php foreach($kriteria as $k=>$v) echo "<th>{$v['nama_kriteria']}</th>"; ?>
    <th>Aksi</th>
</tr>

<?php foreach($data_penilaian as $id=>$a){ ?>
<tr>
    <td><?= $a['nama'] ?></td>
    <?php foreach($kriteria as $k=>$v){ ?>
        <td><?= $a['nilai'][$k]??'-' ?></td>
    <?php } ?>
    <td>
        <a href="?edit=<?= $id ?>" class="btn btn-edit">Edit</a>
        <a href="?hapus=<?= $id ?>" onclick="return confirm('Hapus?')" class="btn btn-del">Hapus</a>
    </td>
</tr>
<?php } ?>
</table>

<h3>Hasil Ranking MOORA</h3>
<table>
<tr><th>Rank</th><th>Nama Kos</th><th>Skor</th></tr>
<?php $i=1; foreach($skor as $id=>$s){ ?>
<tr>
    <td><?= $i++ ?></td>
    <td><?= $alternatif[$id] ?></td>
    <td><?= round($s,4) ?></td>
</tr>
<?php } ?>
</table>

<script>
document.getElementById('btnTambah')?.addEventListener('click',e=>{
    e.preventDefault();
    document.getElementById('form').style.display='block';
});
</script>
