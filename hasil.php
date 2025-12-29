<?php
include 'koneksi.php';

// ===============================
// AMBIL DATA
// ===============================
$alternatif = mysqli_query($conn, "SELECT * FROM alternatif");
$kriteria   = mysqli_query($conn, "SELECT * FROM kriteria");

// ===============================
// BENTUK MATRIKS KEPUTUSAN
// ===============================
$nilai = [];
$q_nilai = mysqli_query($conn, "
    SELECT * FROM penilaian
    ORDER BY id, id
");

while ($d = mysqli_fetch_assoc($q_nilai)) {
    $nilai[$d['id']][$d['id']] = $d['nilai'];
}

// ===============================
// NORMALISASI
// ===============================
$normalisasi = [];
foreach ($kriteria as $k) {
    $id_k = $k['id'];
    $sum = 0;

    foreach ($nilai as $alt) {
        $sum += pow($alt[$id_k], 2);
    }

    foreach ($nilai as $id_alt => $alt) {
        $normalisasi[$id_alt][$id_k] = $alt[$id_k] / sqrt($sum);
    }
}

// ===============================
// HITUNG NILAI MOORA
// ===============================
$hasil = [];
foreach ($normalisasi as $id_alt => $alt) {
    $nilai_moora = 0;

    foreach ($kriteria as $k) {
        $id_k = $k['id'];
        if ($k['jenis'] == 'benefit') {
            $nilai_moora += $k['bobot'] * $alt[$id_k];
        } else {
            $nilai_moora -= $k['bobot'] * $alt[$id_k];
        }
    }

    $hasil[$id_alt] = $nilai_moora;
}

// ===============================
// URUTKAN HASIL
// ===============================
arsort($hasil);
?>

<h2>Hasil Perhitungan MOORA</h2>

<table border="1" cellpadding="5">
<tr>
    <th>Ranking</th>
    <th>Nama Kos</th>
    <th>Nilai MOORA</th>
</tr>

<?php
$rank = 1;
foreach ($hasil as $id_alt => $nilai) {
    $a = mysqli_fetch_assoc(mysqli_query(
        $conn,
        "SELECT * FROM alternatif WHERE id='$id_alt'"
    ));
?>
<tr>
    <td><?= $rank++ ?></td>
    <td><?= $a['nama_kos'] ?></td>
    <td><?= round($nilai, 5) ?></td>
</tr>
<?php } ?>
</table>
