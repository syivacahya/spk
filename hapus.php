<?php
include 'koneksi.php';

// cek parameter
if (!isset($_GET['id']) || !isset($_GET['tabel'])) {
    die('Parameter tidak lengkap');
}

$id    = $_GET['id'];
$tabel = $_GET['tabel'];

// whitelist tabel yang boleh dihapus
$allowed = ['kriteria','alternatif'];

if (!in_array($tabel, $allowed)) {
    die('Tabel tidak diizinkan');
}

// tentukan kolom primary key sesuai tabel
$kolom_id = ($tabel == 'kriteria') ? 'id_kriteria' : 'id_alternatif';

// hapus data
$q = mysqli_query($conn, "DELETE FROM $tabel WHERE $kolom_id='$id'");
if (!$q) {
    die("Gagal menghapus data: " . mysqli_error($conn));
}

// redirect ke halaman tabel masing-masing
header("location:{$tabel}.php");
exit;
?>
