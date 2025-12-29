<?php
include 'koneksi.php';
include 'layout/header.php';


/* =========================
   AMBIL DATA KRITERIA
========================= */
$kriteria = [];
$qk = mysqli_query($conn, "SELECT * FROM kriteria ORDER BY id_kriteria");
while ($row = mysqli_fetch_assoc($qk)) {
    $kriteria[$row['id_kriteria']] = $row;
}

/* =========================
   AMBIL DATA ALTERNATIF
========================= */
$alternatif = [];
$qa = mysqli_query($conn, "SELECT * FROM alternatif ORDER BY id_alternatif");
while ($row = mysqli_fetch_assoc($qa)) {
    $alternatif[$row['id_alternatif']] = $row;
}

/* =========================
   AMBIL DATA PENILAIAN
========================= */
$X = [];
$qp = mysqli_query($conn, "SELECT * FROM penilaian");
while ($row = mysqli_fetch_assoc($qp)) {
    $X[$row['id_alternatif']][$row['id_kriteria']] = $row['nilai'];
}

/* =========================
   NORMALISASI
========================= */
$normal = [];
$nilaiPembagi = [];
foreach ($kriteria as $idk => $k) {
    $sum = 0;
    foreach ($X as $idA => $vals) {
        $sum += isset($vals[$idk]) ? pow($vals[$idk], 2) : 0;
    }
    $nilaiPembagi[$idk] = sqrt($sum);
    foreach ($X as $idA => $vals) {
        $normal[$idA][$idk] = isset($vals[$idk]) ? $vals[$idk]/$nilaiPembagi[$idk] : 0;
    }
}

/* =========================
   TERBOBOT
========================= */
$terbobot = [];
foreach ($normal as $idA => $vals) {
    foreach ($kriteria as $idk => $k) {
        $terbobot[$idA][$idk] = $vals[$idk] * $k['bobot'];
    }
}

/* =========================
   HITUNG Yi
========================= */
$yi = [];
$yi_rincian = [];
foreach ($terbobot as $idA => $vals) {
    $benefit = 0;
    $cost = 0;
    foreach ($vals as $idk => $v) {
        if ($kriteria[$idk]['jenis'] == 'benefit') $benefit += $v;
        else $cost += $v;
    }
    $yi[$idA] = $benefit - $cost;
    $yi_rincian[$idA] = [
        'benefit'=>$benefit,
        'cost'=>$cost,
        'yi'=>$yi[$idA]
    ];
}

arsort($yi);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Perhitungan MOORA Lengkap</title>
    <style>
        body { font-family: Arial; margin:20px; }
        table { border-collapse: collapse; margin-top:20px; width:90%; }
        th, td { border:1px solid #333; padding:8px 10px; text-align:center; }
        th { background:#f2f2f2; }
        h2 { margin-top:40px; }
    </style>
</head>
<body>

<h2>1. Matriks Keputusan (X)</h2>
<table>
    <tr>
        <th>Alternatif</th>
        <?php foreach ($kriteria as $k) echo "<th>{$k['kode']}</th>"; ?>
    </tr>
    <?php foreach ($alternatif as $idA => $a) { ?>
    <tr>
        <td><?= $a['nama_kos'] ?></td>
        <?php foreach ($kriteria as $idk => $k) { ?>
            <td><?= isset($X[$idA][$idk]) ? $X[$idA][$idk] : 0 ?></td>
        <?php } ?>
    </tr>
    <?php } ?>
</table>

<h2>2. Matriks Normalisasi</h2>
<table>
    <tr>
        <th>Alternatif</th>
        <?php foreach ($kriteria as $k) echo "<th>{$k['kode']}</th>"; ?>
    </tr>
    <?php foreach ($alternatif as $idA => $a) { ?>
    <tr>
        <td><?= $a['nama_kos'] ?></td>
        <?php foreach ($kriteria as $idk => $k) { ?>
            <td><?= round($normal[$idA][$idk],4) ?></td>
        <?php } ?>
    </tr>
    <?php } ?>
</table>

<h2>3. Matriks Normalisasi Terbobot</h2>
<table>
    <tr>
        <th>Alternatif</th>
        <?php foreach ($kriteria as $k) echo "<th>{$k['kode']}</th>"; ?>
    </tr>
    <?php foreach ($alternatif as $idA => $a) { ?>
    <tr>
        <td><?= $a['nama_kos'] ?></td>
        <?php foreach ($kriteria as $idk => $k) { ?>
            <td><?= round($terbobot[$idA][$idk],4) ?></td>
        <?php } ?>
    </tr>
    <?php } ?>
</table>

<h2>4. Perhitungan Yi (Benefit - Cost)</h2>
<table>
    <tr>
        <th>Alternatif</th>
        <th>Σ Benefit</th>
        <th>Σ Cost</th>
        <th>Yi</th>
    </tr>
    <?php foreach ($alternatif as $idA => $a) { ?>
    <tr>
        <td><?= $a['nama_kos'] ?></td>
        <td><?= round($yi_rincian[$idA]['benefit'],4) ?></td>
        <td><?= round($yi_rincian[$idA]['cost'],4) ?></td>
        <td><?= round($yi_rincian[$idA]['yi'],4) ?></td>
    </tr>
    <?php } ?>
</table>

<h2>5. Ranking Akhir</h2>
<table>
    <tr>
        <th>Rank</th>
        <th>Alternatif</th>
        <th>Yi</th>
    </tr>
    <?php
    $rank = 1;
    foreach ($yi as $idA => $v) { ?>
    <tr>
        <td><?= $rank ?></td>
        <td><?= $alternatif[$idA]['nama_kos'] ?></td>
        <td><?= round($v,4) ?></td>
    </tr>
    <?php
        $rank++;
    } ?>
</table>

</body>
</html>