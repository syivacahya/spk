<?php
include 'koneksi.php';
$data = mysqli_query($conn, "SELECT * FROM kos");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Perhitungan MOORA</title>
</head>
<body>

<h2>Metode MOORA (Multi-Objective Optimization on the Basis of Ratio Analysis)</h2>

<hr>

<h3>1. Matriks Keputusan (X)</h3>
<p>Matriks keputusan berisi nilai awal setiap alternatif terhadap kriteria.</p>

<table border="1" cellpadding="8">
<tr>
    <th>Alternatif</th>
    <th>Harga</th>
    <th>Jarak</th>
    <th>Fasilitas</th>
    <th>Luas</th>
</tr>

<?php
mysqli_data_seek($data, 0);
while ($d = mysqli_fetch_assoc($data)) {
    echo "<tr>
        <td>{$d['nama_kos']}</td>
        <td>{$d['harga']}</td>
        <td>{$d['jarak']}</td>
        <td>{$d['fasilitas']}</td>
        <td>{$d['luas']}</td>
    </tr>";
}
?>
</table>

<hr>

<h3>2. Normalisasi Matriks</h3>
<p>Rumus normalisasi MOORA:</p>

<p style="font-size:18px;">
x<sup>*</sup><sub>ij</sub> =
x<sub>ij</sub> /
√(∑ x<sub>ij</sub><sup>2</sup>)
</p>

<p>
Rumus ini digunakan untuk menyamakan skala nilai antar kriteria.
</p>

<hr>

<h3>3. Normalisasi Terbobot</h3>
<p>Rumus normalisasi terbobot:</p>

<p style="font-size:18px;">
y<sub>ij</sub> = x<sup>*</sup><sub>ij</sub> × w<sub>j</sub>
</p>

<p>
Nilai hasil normalisasi dikalikan dengan bobot masing-masing kriteria.
</p>

<hr>

<h3>4. Nilai Preferensi (Yi)</h3>
<p>Rumus nilai preferensi MOORA:</p>

<p style="font-size:18px;">
Y<sub>i</sub> = (∑ Benefit) − (∑ Cost)
</p>

<p>
Kriteria benefit: fasilitas, luas kamar<br>
Kriteria cost: harga, jarak
</p>

<hr>

<h3>5. Perangkingan Alternatif</h3>
<p>
Alternatif dengan nilai preferensi (Yi) tertinggi merupakan kos terbaik.
</p>

<a href="moora.php">Lihat Ranking Kos</a> |
<a href="index.php">Kembali ke Data Kos</a>

</body>
</html>
