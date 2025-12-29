<?php
include 'koneksi.php';
include 'layout/header.php';
?>

<div class="card">
    <h2>Data Kriteria</h2>
    <a href="tambah_kriteria.php" class="btn">+ Tambah Kriteria</a>
    <br><br>

    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>No</th>
            <th>Kode</th>
            <th>Nama Kriteria</th>
            <th>Jenis</th>
            <th>Bobot</th>
            <th>Aksi</th>
        </tr>

        <?php
        $no = 1;
        $query = mysqli_query($conn, "SELECT * FROM kriteria ORDER BY id_kriteria");
        while ($d = mysqli_fetch_assoc($query)) {
        ?>
        <tr>
            <td><?= $no ?></td>
            <td><?= 'C' . $no ?></td>
            <td><?= $d['nama_kriteria'] ?></td>
            <td><?= ucfirst($d['jenis']) ?></td>
            <td><?= $d['bobot'] ?></td>
            <td>
                <!-- EDIT -->
                <a href="edit_kriteria.php?id_kriteria=<?= $d['id_kriteria'] ?>"
                   class="btn">
                   Edit
                </a>

                <!-- HAPUS -->
                <a href="hapus.php?id=<?= $d['id_kriteria'] ?>&tabel=kriteria"
                   class="btn btn-danger"
                   onclick="return confirm('Hapus kriteria ini?')">
                   Hapus
                </a>
            </td>
        </tr>
        <?php
            $no++;
        }
        ?>
    </table>
</div>

<?php include 'layout/footer.php'; ?>
