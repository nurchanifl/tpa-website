<?php
session_start();
include('../includes/koneksidb.php');
include('../includes/navbar.php');

// Proses penambahan hari libur
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tanggal = $_POST['tanggal'] ?? '';
    $keterangan = $_POST['keterangan'] ?? '';

    // Validasi input
    if (!empty($tanggal) && !empty($keterangan)) {
        $sql_insert = "INSERT INTO hari_libur (tanggal, keterangan) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $sql_insert);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ss', $tanggal, $keterangan);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            $success_message = "Hari libur berhasil ditambahkan!";
        } else {
            $error_message = "Terjadi kesalahan saat menyimpan data.";
        }
    } else {
        $error_message = "Harap mengisi semua field yang diperlukan.";
    }
}

// Ambil data hari libur untuk ditampilkan di tabel
$sql_hari_libur = "SELECT * FROM hari_libur ORDER BY tanggal ASC";
$result_hari_libur = mysqli_query($conn, $sql_hari_libur);
$hari_libur_data = mysqli_fetch_all($result_hari_libur, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Hari Libur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Tambah Hari Libur</h2>

    <?php if (!empty($success_message)) { ?>
        <div class="alert alert-success"> <?= $success_message ?> </div>
    <?php } ?>

    <?php if (!empty($error_message)) { ?>
        <div class="alert alert-danger"> <?= $error_message ?> </div>
    <?php } ?>

    <!-- Form Tambah Hari Libur -->
    <form method="POST" class="mb-4">
        <div class="mb-3">
            <label for="tanggal" class="form-label">Tanggal</label>
            <input type="date" name="tanggal" id="tanggal" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="keterangan" class="form-label">Keterangan</label>
            <input type="text" name="keterangan" id="keterangan" class="form-control" placeholder="Misal: Hari Raya, Libur Nasional" required>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>

    <!-- Tabel Hari Libur -->
    <h3>Daftar Hari Libur</h3>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Keterangan</th>
            <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($hari_libur_data)) { ?>
            <?php foreach ($hari_libur_data as $index => $hari_libur) { ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($hari_libur['tanggal']) ?></td>
                    <td><?= htmlspecialchars($hari_libur['keterangan']) ?></td>
                    <td>
                        <form method="POST" action="hapus_hari_libur.php" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $hari_libur['id'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus hari libur ini?')">Hapus</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="4" class="text-center">Belum ada data hari libur</td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
