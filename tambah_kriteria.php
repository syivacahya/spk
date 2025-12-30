<?php
include 'koneksi.php';
include 'layout/header.php';

// ambil total bobot saat ini
$q_bobot = mysqli_query($conn, "SELECT SUM(bobot) AS total FROM kriteria");
$data_bobot = mysqli_fetch_assoc($q_bobot);
$totalBobot = floatval($data_bobot['total']);
$sisaBobot  = round(1 - $totalBobot, 2);

if (isset($_POST['simpan'])) {
    $nama  = $_POST['nama_kriteria'];
    $jenis = $_POST['jenis'];

    // amankan input bobot
    $bobot = str_replace(',', '.', $_POST['bobot']);
    $bobot = floatval($bobot);

    // validasi total bobot
    if (round($totalBobot + $bobot, 2) > 1) {
        echo "
        <script>
            alert(
                'GAGAL!\\n' +
                'Total bobot tidak boleh melebihi 1.\\n' +
                'Sisa bobot tersedia: {$sisaBobot}'
            );
            window.history.back();
        </script>";
        exit;
    }

    mysqli_query($conn, "
        INSERT INTO kriteria (nama_kriteria, jenis, bobot)
        VALUES ('$nama', '$jenis', '$bobot')
    ");

    echo "
    <script>
        alert('Kriteria berhasil ditambahkan');
        window.location='kriteria.php';
    </script>";
}
?>

<style>
/* ===== CARD ===== */
.card {
    max-width: 520px;
    background: #ffffff;
    padding: 28px;
    border-radius: 16px;
    box-shadow: 0 12px 30px rgba(0,0,0,0.08);
}

/* ===== HEADER ===== */
.card h2 {
    margin-bottom: 18px;
    font-size: 24px;
}

/* ===== INFO BOX ===== */
.info-box {
    background: #f0fdfa;
    border-left: 6px solid #14b8a6;
    padding: 14px 18px;
    border-radius: 10px;
    font-size: 14px;
    margin-bottom: 22px;
}

/* ===== FORM ===== */
.form-group {
    margin-bottom: 16px;
}

label {
    font-weight: 600;
    font-size: 14px;
    display: block;
    margin-bottom: 6px;
}

input, select {
    width: 100%;
    padding: 10px 14px;
    border-radius: 8px;
    border: 1px solid #d1d5db;
    font-size: 14px;
}

input:focus, select:focus {
    outline: none;
    border-color: #2563eb;
}

/* ===== SMALL INFO ===== */
.small-text {
    font-size: 12px;
    color: #6b7280;
    margin-top: 6px;
}

/* ===== BUTTON ===== */
.btn {
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    background: #2563eb;
    color: #fff;
    border: none;
    cursor: pointer;
}

.btn-danger {
    background: #dc2626;
}

.btn-group {
    display: flex;
    gap: 12px;
    margin-top: 22px;
}
</style>

<div class="card">
    <h2>Tambah Kriteria</h2>

    <!-- <div class="info-box">
        <b>Total Bobot Saat Ini:</b> <?= number_format($totalBobot, 2) ?><br>
        <b>Sisa Bobot Tersedia:</b> <?= number_format($sisaBobot, 2) ?>
    </div> -->

    <form method="post">
        <div class="form-group">
            <label>Nama Kriteria</label>
            <input type="text" name="nama_kriteria" placeholder="Contoh: Keamanan" required>
        </div>

        <div class="form-group">
            <label>Jenis Kriteria</label>
            <select name="jenis" required>
                <option value="">-- Pilih Jenis --</option>
                <option value="benefit">Benefit</option>
                <option value="cost">Cost</option>
            </select>
        </div>

        <div class="form-group">
            <label>Bobot</label>
            <input type="number"
                   step="0.01"
                   min="0.01"
                   max="<?= $sisaBobot ?>"
                   name="bobot"
                   placeholder="Contoh: 0.20"
                   required>

        </div>

        <div class="btn-group">
            <button type="submit" name="simpan" class="btn">Simpan</button>
            <a href="kriteria.php" class="btn btn-danger">Batal</a>
        </div>
    </form>
</div>

<?php include 'layout/footer.php'; ?>
