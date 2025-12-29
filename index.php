<?php
include 'cek_login.php';
include 'koneksi.php';
?>

<?php
include 'koneksi.php';
include 'layout/header.php';

// hitung kriteria
$q_kriteria = mysqli_query($conn, "SELECT * FROM kriteria");
$total_kriteria = $q_kriteria ? mysqli_num_rows($q_kriteria) : 0;

// hitung kos
$q_kos = mysqli_query($conn, "SELECT * FROM alternatif");
$total_kos = $q_kos ? mysqli_num_rows($q_kos) : 0;

// hitung penilaian
$q_penilaian = mysqli_query($conn, "SELECT * FROM penilaian");
$total_penilaian = $q_penilaian ? mysqli_num_rows($q_penilaian) : 0;
?>

<h1>Dashboard</h1>

<p>
Sistem Pendukung Keputusan Pemilihan Rumah Kos  
menggunakan Metode <b>MOORA</b>
</p>

<div class="card">
    <p>Total Kriteria</p>
    <h2><?= $total_kriteria ?></h2>
</div>

<div class="card">
    <p>Total Kos</p>
    <h2><?= $total_kos ?></h2>
</div>

<div class="card">
    <p>Total Penilaian</p>
    <h2><?= $total_penilaian ?></h2>
</div>

<?php include 'layout/footer.php'; ?>
