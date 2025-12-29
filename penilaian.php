<?php
include 'koneksi.php';
include 'layout/header.php';


// ===============================
// SIMPAN PENILAIAN
// ===============================
if (isset($_POST['simpan'])) {
    $id_alternatif = $_POST['id'];
    $nilai = $_POST['nilai'];

    // Hapus penilaian lama
    mysqli_query($conn, "DELETE FROM penilaian WHERE id_alternatif='$id_alternatif'");

    foreach ($nilai as $id_kriteria => $n) {
        mysqli_query($conn, "
            INSERT INTO penilaian (id_alternatif, id_kriteria, nilai)
            VALUES ('$id_alternatif', '$id_kriteria', '$n')
        ");
    }

    echo "<script>alert('Penilaian berhasil disimpan'); window.location='penilaian.php';</script>";
    exit;
}

// ===============================
// AMBIL DATA ALTERNATIF
// ===============================
$alternatif = [];
$q_alt = mysqli_query($conn, "SELECT * FROM alternatif");
if (!$q_alt) die("Query alternatif gagal: " . mysqli_error($conn));
while($a = mysqli_fetch_assoc($q_alt)){
    $alternatif[$a['id_alternatif']] = $a['nama_kos'];
}

// ===============================
// AMBIL DATA KRITERIA
// ===============================
$kriteria = [];
$q_krit = mysqli_query($conn, "SELECT * FROM kriteria");
if (!$q_krit) die("Query kriteria gagal: " . mysqli_error($conn));
while($k = mysqli_fetch_assoc($q_krit)){
    $kriteria[$k['id_kriteria']] = [
        'nama' => $k['nama_kriteria'],
        'jenis' => $k['jenis'], // benefit / cost
        'bobot' => $k['bobot']
    ];
}

// ===============================
// AMBIL SEMUA PENILAIAN
// ===============================
$nilai_all = [];
foreach($alternatif as $id_alt => $nama){
    $nilai_all[$id_alt] = [];
    $q = mysqli_query($conn, "
        SELECT p.id_kriteria AS id_k, p.nilai, k.jenis, k.bobot 
        FROM penilaian p 
        JOIN kriteria k ON p.id_kriteria = k.id_kriteria
        WHERE p.id_alternatif='$id_alt'
    ");
    if ($q) {
        while($n = mysqli_fetch_assoc($q)){
            $nilai_all[$id_alt][$n['id_k']] = [
                'nilai' => $n['nilai'],
                'jenis' => $n['jenis'],
                'bobot' => $n['bobot']
            ];
        }
    } else {
        echo "<p>Error query penilaian: ".mysqli_error($conn)."</p>";
    }
}

// ===============================
// PERHITUNGAN MOORA
// ===============================

// 1. Normalisasi
$normalisasi = [];
foreach($kriteria as $id_k => $k){
    $sum_sqr = 0;
    foreach($nilai_all as $alt){
        if(isset($alt[$id_k])){
            $sum_sqr += pow($alt[$id_k]['nilai'], 2);
        }
    }
    $sqrt_sum = sqrt($sum_sqr);
    if($sqrt_sum == 0) continue; // mencegah pembagian 0

    foreach($nilai_all as $id_alt => $alt){
        $normalisasi[$id_alt][$id_k] = isset($alt[$id_k]) ? $alt[$id_k]['nilai'] / $sqrt_sum : 0;
    }
}

// 2. Nilai terbobot
$terbobot = [];
foreach($normalisasi as $id_alt => $vals){
    foreach($vals as $id_k => $r){
        $terbobot[$id_alt][$id_k] = $r * $kriteria[$id_k]['bobot'];
    }
}

// 3. Skor MOORA
$skor = [];
foreach($terbobot as $id_alt => $vals){
    $benefit = $cost = 0;
    foreach($vals as $id_k => $v){
        if($kriteria[$id_k]['jenis'] == 'benefit') $benefit += $v;
        else $cost += $v;
    }
    $skor[$id_alt] = $benefit - $cost;
}

// 4. Ranking
arsort($skor);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Penilaian Kos - SPK MOORA</title>
    <style>
        body { font-family: Arial; margin:20px; }
        label { display:block; margin-top:10px; }
        input, select, button { padding:6px; width:300px; margin-top:5px; }
        button { margin-top:15px; }
        table { border-collapse: collapse; margin-top:20px; width: 50%; }
        th, td { border:1px solid #333; padding:8px; text-align:center; }
        th { background:#f2f2f2; }
    </style>
</head>
<body>

<h2>Input Penilaian Kos</h2>
<form method="POST">
    <label>Nama Kos</label>
    <select name="id" required>
        <option value="">-- Pilih Kos --</option>
        <?php foreach($alternatif as $id => $nama){ ?>
            <option value="<?= $id; ?>"><?= $nama; ?></option>
        <?php } ?>
    </select>

    <hr>
    <?php foreach($kriteria as $id_k => $k){ ?>
        <label><?= $k['nama']; ?> (<?= strtoupper($k['jenis']); ?>)</label>
        <input type="number" name="nilai[<?= $id_k; ?>]" required>
    <?php } ?>

    <button type="submit" name="simpan">Simpan Penilaian</button>
</form>

<hr>

<h2>Hasil Ranking Kos (MOORA)</h2>
<table>
    <tr>
        <th>Ranking</th>
        <th>Nama Kos</th>
        <th>Skor MOORA</th>
    </tr>
    <?php
    $rank = 1;
    foreach($skor as $id_alt => $s){
        echo "<tr>
                <td>$rank</td>
                <td>".$alternatif[$id_alt]."</td>
                <td>".round($s,4)."</td>
              </tr>";
        $rank++;
    }
    ?>
</table>

</body>
</html>
