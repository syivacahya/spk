<?php
include 'koneksi.php';

// ===============================
// CEK PARAMETER
// ===============================
if (!isset($_GET['id']) || !isset($_GET['tabel'])) {
    die('Parameter tidak lengkap');
}

$id    = $_GET['id'];
$tabel = $_GET['tabel'];

// ===============================
// WHITELIST TABEL
// ===============================
$allowed = ['kriteria', 'alternatif'];
if (!in_array($tabel, $allowed)) {
    die('Tabel tidak diizinkan');
}

// ===============================
// TENTUKAN PRIMARY KEY
// ===============================
if ($tabel == 'kriteria') {
    $kolom_id = 'id_kriteria';
} else {
    $kolom_id = 'id_alternatif';
}

// ===============================
// HAPUS DATA
// ===============================
$query = mysqli_query(
    $conn,
    "DELETE FROM $tabel WHERE $kolom_id = '$id'"
);

if (!$query) {
    die("Gagal menghapus data: " . mysqli_error($conn));
}

// ===============================
// REDIRECT
// ===============================
header("Location: {$tabel}.php");
exit;
