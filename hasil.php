<?php
include 'koneksi.php';
include 'layout/header.php';

// ===============================
// AMBIL DATA ALTERNATIF & KRITERIA
// ===============================
$alternatif = [];
$q_alt = mysqli_query($conn, "SELECT * FROM alternatif");
while($a = mysqli_fetch_assoc($q_alt)){
    $alternatif[$a['id_alternatif']] = $a['nama_kos'];
}

$kriteria = [];
$q_krit = mysqli_query($conn, "SELECT * FROM kriteria");
while($k = mysqli_fetch_assoc($q_krit)){
    $kriteria[$k['id_kriteria']] = [
        'nama' => $k['nama_kriteria'],
        'jenis' => $k['jenis'], // benefit / cost
        'bobot' => $k['bobot']
    ];
}

// ===============================
// AMBIL PENILAIAN
// ===============================
$nilai = [];
$q_nilai = mysqli_query($conn, "SELECT * FROM penilaian");
while($d = mysqli_fetch_assoc($q_nilai)){
    $nilai[$d['id_alternatif']][$d['id_kriteria']] = $d['nilai'];
}

// ===============================
// NORMALISASI
// ===============================
$normalisasi = [];
foreach($kriteria as $id_k => $k){
    $sum_sqr = 0;
    foreach($nilai as $id_alt => $alt){
        if(isset($alt[$id_k])){
            $sum_sqr += pow($alt[$id_k], 2);
        }
    }
    $sqrt_sum = sqrt($sum_sqr);
    if($sqrt_sum == 0) $sqrt_sum = 1;

    foreach($nilai as $id_alt => $alt){
        $normalisasi[$id_alt][$id_k] = isset($alt[$id_k]) ? $alt[$id_k] / $sqrt_sum : 0;
    }
}

// ===============================
// TERBOBOT
// ===============================
$terbobot = [];
foreach($normalisasi as $id_alt => $alt){
    foreach($alt as $id_k => $n){
        $terbobot[$id_alt][$id_k] = $n * $kriteria[$id_k]['bobot'];
    }
}

// ===============================
// HITUNG MOORA
// ===============================
$yi_data = [];
$hasil = [];
foreach($terbobot as $id_alt => $alt){
    $benefit = 0;
    $cost = 0;
    foreach($alt as $id_k => $n){
        if($kriteria[$id_k]['jenis'] == 'benefit'){
            $benefit += $n;
        } else {
            $cost += $n;
        }
    }
    $yi = $benefit - $cost;
    $yi_data[$id_alt] = [
        'benefit' => $benefit,
        'cost' => $cost,
        'yi' => $yi
    ];
    $hasil[$id_alt] = $yi;
}

// ===============================
// URUTKAN HASIL
// ===============================
arsort($hasil);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Perhitungan MOORA - SPK Kos</title>
    <style>
        body { font-family: Arial; margin:20px; background:#f9f9f9; }
        h2, h3 { margin-top:30px; }
        table { border-collapse: collapse; width: 80%; margin-bottom:30px; }
        th, td { border:1px solid #333; padding:8px; text-align:center; }
        th { background:#4CAF50; color:white; }
        tr:nth-child(even){ background:#f2f2f2; }
        tr:hover { background:#ddd; }
        .container { max-width:1000px; margin:auto; }
    </style>
</head>
<body>
<div class="container">

<h2>Perhitungan MOORA - SPK Pemilihan Kos</h2>

<!-- Matriks Keputusan -->
<h3>Matriks Keputusan (X)</h3>
<table>
<tr>
    <th>Alternatif</th>
    <?php foreach($kriteria as $k) echo "<th>{$k['nama']}</th>"; ?>
</tr>
<?php foreach($nilai as $id_alt => $vals){ ?>
<tr>
    <td><?= $alternatif[$id_alt] ?></td>
    <?php foreach($kriteria as $id_k => $k){ ?>
        <td><?= isset($vals[$id_k]) ? $vals[$id_k] : 0 ?></td>
    <?php } ?>
</tr>
<?php } ?>
</table>

<!-- Matriks Normalisasi -->
<h3>Matriks Normalisasi</h3>
<table>
<tr>
    <th>Alternatif</th>
    <?php foreach($kriteria as $k) echo "<th>{$k['nama']}</th>"; ?>
</tr>
<?php foreach($normalisasi as $id_alt => $vals){ ?>
<tr>
    <td><?= $alternatif[$id_alt] ?></td>
    <?php foreach($kriteria as $id_k => $k){ ?>
        <td><?= round($vals[$id_k],4) ?></td>
    <?php } ?>
</tr>
<?php } ?>
</table>

<!-- Matriks Terbobot -->
<h3>Matriks Normalisasi Terbobot</h3>
<table>
<tr>
    <th>Alternatif</th>
    <?php foreach($kriteria as $k) echo "<th>{$k['nama']}</th>"; ?>
</tr>
<?php foreach($terbobot as $id_alt => $vals){ ?>
<tr>
    <td><?= $alternatif[$id_alt] ?></td>
    <?php foreach($kriteria as $id_k => $k){ ?>
        <td><?= round($vals[$id_k],4) ?></td>
    <?php } ?>
</tr>
<?php } ?>
</table>

<!-- Perhitungan Yi -->
<h3>Perhitungan Yi (Benefit - Cost)</h3>
<table>
<tr>
    <th>Alternatif</th>
    <th>Benefit</th>
    <th>Cost</th>
    <th>Yi</th>
</tr>
<?php foreach($yi_data as $id_alt => $v){ ?>
<tr>
    <td><?= $alternatif[$id_alt] ?></td>
    <td><?= round($v['benefit'],4) ?></td>
    <td><?= round($v['cost'],4) ?></td>
    <td><?= round($v['yi'],4) ?></td>
</tr>
<?php } ?>
</table>

<!-- Ranking Akhir -->
<h3>Ranking Akhir</h3>
<table>
<tr>
    <th>Rank</th>
    <th>Alternatif</th>
    <th>Yi</th>
</tr>
<?php $rank=1; foreach($hasil as $id_alt => $yi){ ?>
<tr>
    <td><?= $rank++ ?></td>
    <td><?= $alternatif[$id_alt] ?></td>
    <td><?= round($yi,4) ?></td>
</tr>
<?php } ?>
</table>

</div>
</body>
</html>
