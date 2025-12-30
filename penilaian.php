<?php
include 'cek_login.php';
include 'koneksi.php';
include 'layout/header.php';

/* =====================================================
HAPUS PENILAIAN
===================================================== */
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    mysqli_query($conn, "DELETE FROM penilaian WHERE id_alternatif='$id'");
    echo "<script>alert('Penilaian berhasil dihapus');location='penilaian.php';</script>";
    exit;
}

/* =====================================================
DATA ALTERNATIF
===================================================== */
$alternatif = [];
$q = mysqli_query($conn,"SELECT * FROM alternatif");
while($r = mysqli_fetch_assoc($q)){
    $alternatif[$r['id_alternatif']] = $r['nama_kos'];
}

/* =====================================================
DATA KRITERIA
===================================================== */
$kriteria = [];
$q = mysqli_query($conn,"SELECT * FROM kriteria");
while($r = mysqli_fetch_assoc($q)){
    $kriteria[$r['id_kriteria']] = $r;
}

/* =====================================================
DATA PENILAIAN (MATRIX)
===================================================== */
$data_penilaian = [];
$q = mysqli_query($conn,"
    SELECT a.id_alternatif,a.nama_kos,p.id_kriteria,p.nilai
    FROM penilaian p
    JOIN alternatif a ON p.id_alternatif=a.id_alternatif
");
while($r = mysqli_fetch_assoc($q)){
    $data_penilaian[$r['id_alternatif']]['nama'] = $r['nama_kos'];
    $data_penilaian[$r['id_alternatif']]['nilai'][$r['id_kriteria']] = $r['nilai'];
}

/* =====================================================
MOORA (AMAN DARI ERROR)
===================================================== */
$nilai_all = [];

// hanya alternatif yang NILAINYA LENGKAP
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

$normalisasi = [];
foreach ($kriteria as $k => $v) {
    $sum = 0;
    foreach ($nilai_all as $a) {
        $sum += pow($a[$k], 2);
    }
    if ($sum == 0) continue; // cegah division by zero
    $akar = sqrt($sum);

    foreach ($nilai_all as $id => $a) {
        $normalisasi[$id][$k] = $a[$k] / $akar;
    }
}

$skor = [];
foreach ($normalisasi as $id => $a) {
    $benefit = 0;
    $cost = 0;
    foreach ($a as $k => $n) {
        $bobot = $kriteria[$k]['bobot'];
        if ($kriteria[$k]['jenis'] == 'benefit') {
            $benefit += $n * $bobot;
        } else {
            $cost += $n * $bobot;
        }
    }
    $skor[$id] = $benefit - $cost;
}
arsort($skor);
?>

<style>
body{font-family:Arial;background:#f4f6f9}
table{width:100%;border-collapse:collapse;margin-top:10px}
th,td{border:1px solid #ddd;padding:8px;text-align:center}
th{background:#3498db;color:#fff}
.btn{padding:6px 12px;border-radius:4px;color:#fff;text-decoration:none}
.btn-add{background:#3498db}
.btn-edit{background:#f39c12}
.btn-del{background:#e74c3c}
.card{background:#fff;padding:20px;border-radius:6px;margin-top:15px}
</style>

<h2>Data Penilaian</h2>
<a href="tambah_penilaian.php" class="btn btn-add">+ Tambah Penilaian</a>

<div class="card">
<h3>Tabel Penilaian</h3>
<table>
<tr>
    <th>Alternatif</th>
    <?php foreach($kriteria as $v): ?>
        <th><?= $v['nama_kriteria'] ?></th>
    <?php endforeach; ?>
    <th>Aksi</th>
</tr>

<?php foreach($data_penilaian as $id=>$a): ?>
<tr>
    <td><?= $a['nama'] ?></td>
    <?php foreach($kriteria as $k=>$v): ?>
        <td><?= $a['nilai'][$k] ?? '-' ?></td>
    <?php endforeach; ?>
    <td>
        <a href="edit_penilaian.php?id=<?= $id ?>" class="btn btn-edit">Edit</a>
        <a href="?hapus=<?= $id ?>" onclick="return confirm('Hapus penilaian ini?')" class="btn btn-del">Hapus</a>
    </td>
</tr>
<?php endforeach; ?>
</table>
</div>

<div class="card">
<h3>Hasil Ranking MOORA</h3>
<table>
<tr>
    <th>Rank</th>
    <th>Nama Kos</th>
    <th>Skor</th>
</tr>
<?php $i=1; foreach($skor as $id=>$s): ?>
<tr>
    <td><?= $i++ ?></td>
    <td><?= $alternatif[$id] ?></td>
    <td><?= round($s, 4) ?></td>
</tr>
<?php endforeach; ?>
</table>
</div>

<?php include 'layout/footer.php'; ?>
