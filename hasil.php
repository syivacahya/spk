
<?php
include 'koneksi.php';
include 'layout/header.php';

// ===============================
// LOGIKA PERHITUNGAN PHP (PERTAHANKAN)
// ===============================

// Ambil Alternatif
$alternatif = [];
$q_alt = mysqli_query($conn, "SELECT * FROM alternatif");
while($a = mysqli_fetch_assoc($q_alt)){
    $alternatif[$a['id_alternatif']] = $a['nama_kos'];
}

// Ambil Kriteria
$kriteria = [];
$q_krit = mysqli_query($conn, "SELECT * FROM kriteria");
while($k = mysqli_fetch_assoc($q_krit)){
    $kriteria[$k['id_kriteria']] = [
        'nama' => $k['nama_kriteria'],
        'jenis' => $k['jenis'],
        'bobot' => $k['bobot']
    ];
}

// Ambil Penilaian
$nilai = [];
$q_nilai = mysqli_query($conn, "SELECT * FROM penilaian");
while($d = mysqli_fetch_assoc($q_nilai)){
    $nilai[$d['id_alternatif']][$d['id_kriteria']] = $d['nilai'];
}

// Normalisasi
$normalisasi = [];
foreach($kriteria as $id_k => $k){
    $sum_sqr = 0;
    foreach($nilai as $id_alt => $alt){
        if(isset($alt[$id_k])) $sum_sqr += pow($alt[$id_k], 2);
    }
    $sqrt_sum = sqrt($sum_sqr);
    if($sqrt_sum == 0) $sqrt_sum = 1;

    foreach($nilai as $id_alt => $alt){
        $normalisasi[$id_alt][$id_k] = isset($alt[$id_k]) ? $alt[$id_k] / $sqrt_sum : 0;
    }
}

// Terbobot
$terbobot = [];
foreach($normalisasi as $id_alt => $alt){
    foreach($alt as $id_k => $n){
        $terbobot[$id_alt][$id_k] = $n * $kriteria[$id_k]['bobot'];
    }
}

// Hitung Yi & Ranking
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
    $yi_data[$id_alt] = ['yi' => $yi];
    $hasil[$id_alt] = $yi;
}

arsort($hasil);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Hasil Perankingan</title>
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

<div class="container">
    <h2 style="text-align: center; margin-bottom: 5px;">Hasil Perankingan</h2>
    <p style="text-align: center; color: #666; margin-top: 0;">Urutan Kos Terbaik (Metode MOORA)</p>
    
    <table>
        <thead>
            <tr>
                <th width="15%">Rank</th>
                <th width="60%">Nama Alternatif</th>
                <th width="25%">Nilai Yi</th>
            </tr>
        </thead>
        <tbody>
<?php 
$rank = 1; 
foreach($hasil as $id_alt => $yi) { 
    $class = ($rank == 1) ? 'top-rank' : '';
?>
<tr class="<?= $class ?>">
    <td><?= $rank++ ?></td>
    <td><?= isset($alternatif[$id_alt]) ? $alternatif[$id_alt] : "Alternatif ID $id_alt" ?></td>
    <td><?= round($yi, 4) ?></td>
</tr>
<?php } ?>
</tbody>

    </table>
</div>

</body>
</html>
```