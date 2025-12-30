<?php
include 'koneksi.php';

/* =========================
   AMBIL KRITERIA
========================= */
$kriteria = [];
$qk = mysqli_query($conn,"SELECT * FROM kriteria");
while($k = mysqli_fetch_assoc($qk)){
    $kriteria[$k['id_kriteria']] = $k;
}

/* =========================
   AMBIL ALTERNATIF
========================= */
$alternatif = [];
$qa = mysqli_query($conn,"SELECT * FROM alternatif");
while($a = mysqli_fetch_assoc($qa)){
    $alternatif[$a['id_alternatif']] = $a['nama_kos'];
}

/* =========================
   MATRIKS KEPUTUSAN
========================= */
$X = [];
$q = mysqli_query($conn,"SELECT * FROM penilaian");
while($p = mysqli_fetch_assoc($q)){
    $X[$p['id_alternatif']][$p['id_kriteria']] = $p['nilai'];
}

/* =========================
   NORMALISASI
========================= */
$R = [];
foreach($kriteria as $idk => $k){
    $sum = 0;
    foreach($X as $alt){
        $sum += pow($alt[$idk] ?? 0, 2);
    }
    $akar = sqrt($sum);
    foreach($X as $ida => $alt){
        $R[$ida][$idk] = ($akar == 0) ? 0 : ($alt[$idk] ?? 0)/$akar;
    }
}

/* =========================
   HITUNG MOORA
========================= */
$hasil = [];
foreach($R as $ida => $alt){
    $benefit = 0;
    $cost = 0;
    foreach($alt as $idk => $nilai){
        $bobot = $kriteria[$idk]['bobot'];
        if($kriteria[$idk]['jenis'] == 'benefit'){
            $benefit += $nilai * $bobot;
        } else {
            $cost += $nilai * $bobot;
        }
    }
    $hasil[$ida] = $benefit - $cost;
}

arsort($hasil);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Rekomendasi Kos</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="user-container">
    <div class="card" style="text-align:center">
        <h2>Sistem Rekomendasi Pemilihan Kos</h2>
        <p>
            Rekomendasi kos terbaik berdasarkan perhitungan 
            <b>Metode MOORA</b> menggunakan beberapa kriteria penilaian.
        </p>
    </div>

    <div class="card">
        <h3>Hasil Perankingan Kos</h3>

        <table>
            <tr>
                <th>Ranking</th>
                <th>Nama Kos</th>
                <th>Skor MOORA</th>
                <th>Detail</th>
            </tr>

            <?php
            $rank = 1;
            foreach($hasil as $id => $skor):
            ?>
            <tr class="<?= ($rank == 1) ? 'rank-1' : '' ?>">
                <td><?= $rank ?></td>
                <td>
                    
                    <?= $alternatif[$id] ?>
                    <?php if($rank == 1): ?>
                        <span class="badge-best">⭐ Rekomendasi Terbaik</span>
                    <?php endif; ?>
                </td>
                <td><?= round($skor, 4) ?></td>
                <td>
                    <a href="user_detail.php?id=<?= $id ?>" class="btn">
                        Detail
                    </a>
                </td>
            </tr>
            <?php
            $rank++;
            endforeach;
            ?>
        </table>
    </div>

</div>

</body>
</html>
