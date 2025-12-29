<?php
include 'cek_login.php';
include 'koneksi.php';
include 'layout/header.php';

// jumlah kriteria
$q_kriteria = mysqli_query($conn, "SELECT COUNT(*) AS total FROM kriteria");
$data_kriteria = mysqli_fetch_assoc($q_kriteria);

// jumlah alternatif kos
$q_kos = mysqli_query($conn, "SELECT COUNT(*) AS total FROM alternatif");
$data_kos = mysqli_fetch_assoc($q_kos);

// jumlah kos yang sudah dinilai
$q_penilaian = mysqli_query(
    $conn,
    "SELECT COUNT(DISTINCT id_alternatif) AS total FROM penilaian"
);
$data_penilaian = mysqli_fetch_assoc($q_penilaian);
?>

<style>
.dashboard-header {
    margin-bottom: 20px;
}

.dashboard-header p {
    color: #666;
}

.alert {
    background: #e8f7f1;
    color: #256d5b;
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 25px;
}

.cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 20px;
}

.card {
    background: #fff;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.06);
    position: relative;
}

.card::before {
    content: "";
    position: absolute;
    left: 0;
    top: 0;
    width: 6px;
    height: 100%;
    border-radius: 12px 0 0 12px;
}

.kriteria::before { background: #4e73df; }
.alternatif::before { background: #1cc88a; }
.penilaian::before { background: #f6c23e; }

.card h3 {
    font-size: 15px;
    color: #666;
    margin-bottom: 8px;
}

.card .angka {
    font-size: 36px;
    font-weight: bold;
    color: #2c3e50;
}

.card .desc {
    font-size: 13px;
    color: #888;
}

.action {
    margin-top: 30px;
    text-align: right;
}

.btn {
    background: #145b53ff;
    color: #fff;
    padding: 12px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: bold;
}

.btn:hover {
    background: #2a96a5;
}
</style>

<div class="dashboard-header">
    <h2>Dashboard</h2>
</div>

<div class="alert">
    Selamat datang <b>ADMIN</b>! Anda dapat mengelola data dan melakukan perhitungan MOORA melalui menu di samping.
</div>

<div class="cards">

    <div class="card kriteria">
        <h3>Jumlah Kriteria</h3>
        <div class="angka"><?= $data_kriteria['total']; ?></div>
        <div class="desc">Total kriteria penilaian kos</div>
    </div>

    <div class="card alternatif">
        <h3>Jumlah Alternatif Kos</h3>
        <div class="angka"><?= $data_kos['total']; ?></div>
        <div class="desc">Total kos yang terdaftar</div>
    </div>

    <div class="card penilaian">
        <h3>Kos Sudah Dinilai</h3>
        <div class="angka"><?= $data_penilaian['total']; ?></div>
        <div class="desc">Kos yang memiliki data penilaian</div>
    </div>

</div>

<div class="action">
    <a href="perhitungan.php" class="btn">
        Proses Perhitungan MOORA
    </a>
</div>

<?php include 'layout/footer.php'; ?>
