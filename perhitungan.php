<?php
include 'cek_login.php';
include 'koneksi.php';
include 'layout/header.php';

/* =========================
   AMBIL DATA KRITERIA
========================= */
$kriteria = [];
$qk = mysqli_query($conn, "SELECT * FROM kriteria ORDER BY id_kriteria") or die(mysqli_error($conn));
while ($row = mysqli_fetch_assoc($qk)) {
    $kriteria[$row['id_kriteria']] = $row;
}

/* =========================
   AMBIL DATA ALTERNATIF
========================= */
$alternatif = [];
$qa = mysqli_query($conn, "SELECT * FROM alternatif ORDER BY id_alternatif") or die(mysqli_error($conn));
while ($row = mysqli_fetch_assoc($qa)) {
    $alternatif[$row['id_alternatif']] = $row;
}

/* =========================
   AMBIL DATA PENILAIAN
========================= */
$X = [];
$qp = mysqli_query($conn, "SELECT * FROM penilaian") or die(mysqli_error($conn));
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
    foreach ($alternatif as $idA => $a) {
        $nilai = isset($X[$idA][$idk]) ? $X[$idA][$idk] : 0;
        $sum += pow($nilai, 2);
    }
    $nilaiPembagi[$idk] = sqrt($sum);
    foreach ($alternatif as $idA => $a) {
        $nilai = isset($X[$idA][$idk]) ? $X[$idA][$idk] : 0;
        $normal[$idA][$idk] = $nilaiPembagi[$idk] != 0 ? $nilai / $nilaiPembagi[$idk] : 0;
    }
}

/* =========================
   TERBOBOT
========================= */
$terbobot = [];
foreach ($alternatif as $idA => $a) {
    foreach ($kriteria as $idk => $k) {
        $nilai = isset($normal[$idA][$idk]) ? $normal[$idA][$idk] : 0;
        $terbobot[$idA][$idk] = $nilai * $k['bobot'];
    }
}

/* =========================
   HITUNG Yi
========================= */
$yi = [];
$yi_rincian = [];
foreach ($alternatif as $idA => $a) {
    $benefit = 0;
    $cost = 0;
    foreach ($kriteria as $idk => $k) {
        $nilai = isset($terbobot[$idA][$idk]) ? $terbobot[$idA][$idk] : 0;
        if ($k['jenis'] == 'benefit') $benefit += $nilai;
        else $cost += $nilai;
    }
    $yi[$idA] = $benefit - $cost;
    $yi_rincian[$idA] = [
        'benefit' => $benefit,
        'cost' => $cost,
        'yi' => $yi[$idA]
    ];
}

arsort($yi);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Perhitungan MOORA Lengkap</title>
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
        <td><?= htmlspecialchars($a['nama_kos']) ?></td>
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
        <td><?= htmlspecialchars($a['nama_kos']) ?></td>
        <?php foreach ($kriteria as $idk => $k) { ?>
            <td><?= round($normal[$idA][$idk],3) ?></td>
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
        <td><?= htmlspecialchars($a['nama_kos']) ?></td>
        <?php foreach ($kriteria as $idk => $k) { ?>
            <td><?= round($terbobot[$idA][$idk],3) ?></td>
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
        <td><?= htmlspecialchars($a['nama_kos']) ?></td>
        <td><?= round($yi_rincian[$idA]['benefit'],3) ?></td>
        <td><?= round($yi_rincian[$idA]['cost'],3) ?></td>
        <td><?= round($yi_rincian[$idA]['yi'],3) ?></td>
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
        <td><?= htmlspecialchars($alternatif[$idA]['nama_kos']) ?></td>
        <td><?= round($v,3) ?></td>
    </tr>
    <?php
        $rank++;
    } ?>
</table>

</body>
</html>
