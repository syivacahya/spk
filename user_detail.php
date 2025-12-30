<?php
include 'koneksi.php';

/* ==============================
   VALIDASI ID
============================== */
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die("Data tidak valid");
}

/* ==============================
   DATA KOS
============================== */
$kos = mysqli_fetch_assoc(
    mysqli_query($conn,"
        SELECT id_alternatif, nama_kos
        FROM alternatif
        WHERE id_alternatif = '$id'
    ")
);
if (!$kos) {
    die("Data kos tidak ditemukan");
}

/* ==============================
   AMBIL KRITERIA
============================== */
$kriteria = [];
$qk = mysqli_query($conn,"SELECT * FROM kriteria");
while($k = mysqli_fetch_assoc($qk)){
    $kriteria[$k['id_kriteria']] = $k;
}

/* ==============================
   AMBIL SEMUA NILAI PENILAIAN
============================== */
$nilaiAll = [];
$qp = mysqli_query($conn,"SELECT * FROM penilaian");
while($p = mysqli_fetch_assoc($qp)){
    $nilaiAll[$p['id_alternatif']][$p['id_kriteria']] = $p['nilai'];
}

/* ==============================
   HITUNG SKOR MOORA (1 ALTERNATIF)
============================== */
$benefit = 0;
$cost = 0;

foreach($kriteria as $idk => $k){
    $sum = 0;
    foreach($nilaiAll as $alt){
        $sum += pow($alt[$idk] ?? 0, 2);
    }
    $akar = sqrt($sum);

    $nilai = $nilaiAll[$id][$idk] ?? 0;
    $r = ($akar == 0) ? 0 : $nilai / $akar;
    $v = $r * $k['bobot'];

    if($k['jenis'] == 'benefit'){
        $benefit += $v;
    } else {
        $cost += $v;
    }
}

$skorMoora = $benefit - $cost;

/* ==============================
   DETAIL NILAI KRITERIA (TABEL)
============================== */
$qDetail = mysqli_query($conn,"
    SELECT 
        k.nama_kriteria,
        k.jenis,
        k.bobot,
        p.nilai
    FROM penilaian p
    JOIN kriteria k 
        ON p.id_kriteria = k.id_kriteria
    WHERE p.id_alternatif = '$id'
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Detail Kos</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="user-container">

    <div class="card">
        <h2>Detail Kos</h2>
        <h3><?= $kos['nama_kos']; ?></h3>

        <p>
            <b>Skor MOORA:</b> <?= round($skorMoora, 4); ?>
        </p>
    </div>

    <div class="card">
        <h3>Detail Penilaian</h3>
        <table>
            <tr>
                <th>Kriteria</th>
                <th>Jenis</th>
                <th>Bobot</th>
                <th>Nilai</th>
            </tr>

            <?php while($r = mysqli_fetch_assoc($qDetail)): ?>
            <tr>
                <td><?= $r['nama_kriteria']; ?></td>
                <td><?= strtoupper($r['jenis']); ?></td>
                <td><?= $r['bobot']; ?></td>
                <td><?= $r['nilai']; ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <a href="user.php" class="btn">← Kembali</a>

</div>

</body>
</html>
