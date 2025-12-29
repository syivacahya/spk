<?php
include 'koneksi.php';
include 'layout/header.php';

// ambil total bobot
$q_bobot = mysqli_query($conn, "SELECT SUM(bobot) AS total_bobot FROM kriteria");
$data_bobot = mysqli_fetch_assoc($q_bobot);
$totalBobot = floatval($data_bobot['total_bobot']);
?>

<style>
/* ===== CARD ===== */
.card {
    background: #ffffff;
    padding: 24px;
    border-radius: 14px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.05);
}

/* ===== HEADER ===== */
.card h2 {
    margin-bottom: 10px;
    font-size: 22px;
}

/* ===== BUTTON ===== */
.btn {
    padding: 10px 18px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    background: #2563eb;
    color: #fff;
}

.btn-danger {
    background: #dc2626;
}

.btn-disabled {
    background: #9ca3af;
    cursor: not-allowed;
    pointer-events: none;
}

/* ===== INFO BOBOT ===== */
.info-bobot {
    margin: 18px 0;
    padding: 14px 18px;
    border-radius: 10px;
    font-size: 14px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.info-ok {
    background: #e6fffa;
    color: #065f46;
    border-left: 6px solid #10b981;
}

.info-warning {
    background: #fff7ed;
    color: #92400e;
    border-left: 6px solid #f59e0b;
}

.badge {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    color: #fff;
}

.badge-ok {
    background: #10b981;
}

.badge-warning {
    background: #f59e0b;
}

/* ===== TABLE ===== */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

table th {
    background: #3498db;
    padding: 12px;
    text-align: left;
    font-size: 14px;
}

table td {
    padding: 12px;
    border-top: 1px solid #e5e7eb;
    font-size: 14px;
}

table tr:hover {
    background: #f9fafb;
}

/* tombol aksi di tabel */
.table-action .btn {
    margin-right: 6px;
    display: inline-block;
}

/* ===== AKSI BUTTON ===== */
.aksi-btn {
    display: flex;
    gap: 10px; /* jarak antar tombol */
}
</style>

<div class="card">
    <h2>Data Kriteria</h2>

    <!-- INFO TOTAL BOBOT -->
    <?php if ($totalBobot == 1): ?>
        <div class="info-bobot info-ok">
            <span>
                <b>Total Bobot Kriteria:</b>
                <?= number_format($totalBobot, 2) ?>
                (Sudah sesuai ketentuan)
            </span>
            <span class="badge badge-ok">VALID</span>
        </div>
    <?php else: ?>
        <div class="info-bobot info-warning">
            <span>
                <b>Total Bobot Kriteria:</b>
                <?= number_format($totalBobot, 2) ?>
                (Ketentuan: total bobot harus bernilai 1)
            </span>
            <span class="badge badge-warning">BELUM VALID</span>
        </div>
    <?php endif; ?>

    <!-- BUTTON TAMBAH -->
    <?php if ($totalBobot < 1): ?>
        <a href="tambah_kriteria.php" class="btn">+ Tambah Kriteria</a>
    <?php else: ?>
        <a href="#" class="btn btn-disabled">+ Tambah Kriteria</a>
    <?php endif; ?>

    <br><br>

    <!-- TABLE -->
    <table>
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
        while ($d = mysqli_fetch_assoc($query)) :
        ?>
        <tr>
            <td><?= $no ?></td>
            <td><?= 'C' . $no ?></td>
            <td><?= $d['nama_kriteria'] ?></td>
            <td><?= ucfirst($d['jenis']) ?></td>
            <td><?= $d['bobot'] ?></td>
            <td>
                <div class="aksi-btn">
                    <a href="edit_kriteria.php?id_kriteria=<?= $d['id_kriteria'] ?>" class="btn">
                        Edit
                    </a>
                    <a href="hapus.php?id=<?= $d['id_kriteria'] ?>&tabel=kriteria"
                    class="btn btn-danger"
                    onclick="return confirm('Hapus kriteria ini?')">
                    Hapus
                    </a>
                </div>
            </td>
        </tr>
        <?php
            $no++;
        endwhile;
        ?>
    </table>
</div>

<?php include 'layout/footer.php'; ?>
