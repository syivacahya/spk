<?php
include 'koneksi.php';
include 'layout/header.php';
?>

<div class="card">
    <h2>Data Alternatif (Kos)</h2>
    <a href="tambah_alternatif.php" class="btn btn-tambah">+ Tambah Alternatif Kos</a>
    <br><br>

    <table border="1" cellpadding="8">
        <tr>
            <th>No</th>
            <th>Kode</th>
            <th>Nama Kos</th>
            <th>Aksi</th>
        </tr>

        <?php
        $no = 1;
        // Ambil kode dan nama kos saja
        $q = mysqli_query($conn, "SELECT id_alternatif, kode, nama_kos FROM alternatif ORDER BY id_alternatif");
        while ($d = mysqli_fetch_assoc($q)) {
        ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $d['kode'] ?></td>
            <td><?= $d['nama_kos'] ?></td>
            <td>
                <a href="edit_alternatif.php?id=<?= $d['id_alternatif'] ?>" class="btn">Edit</a>
                <a href="hapus.php?id=<?= $d['id_alternatif'] ?>&tabel=alternatif" 
                   class="btn btn-danger" 
                   onclick="return confirm('Hapus data ini?')">Hapus</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>

<?php include 'layout/footer.php'; ?>
