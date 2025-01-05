<?php
session_start();
include('../includes/koneksidb.php');
include('../includes/navbar.php');

// Ambil daftar unit
$sql_unit = "SELECT * FROM unit";
$result_unit = mysqli_query($conn, $sql_unit);

$kelas_data = [];
$antri_data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['simpan_presensi'])) {
    $id_unit = $_POST['id_unit'] ?? '';
    $id_kelas = $_POST['id_kelas'] ?? '';
    $tanggal = $_POST['tanggal'] ?? '';

    // Validasi tanggal dipilih
    if (empty($tanggal)) {
        die("Tanggal harus dipilih.");
    }

    // Ambil data kelas berdasarkan unit
    if (!empty($id_unit)) {
        $sql_kelas = "SELECT * FROM kelas WHERE id_unit = ?";
        $stmt_kelas = mysqli_prepare($conn, $sql_kelas);
        mysqli_stmt_bind_param($stmt_kelas, 'i', $id_unit);
        mysqli_stmt_execute($stmt_kelas);
        $result_kelas = mysqli_stmt_get_result($stmt_kelas);
        $kelas_data = mysqli_fetch_all($result_kelas, MYSQLI_ASSOC);
        mysqli_stmt_close($stmt_kelas);
    }

    // Ambil data santri berdasarkan kelas
    if (!empty($id_kelas)) {
        $sql_santri = "SELECT * FROM santri WHERE id_kelas = ?";
        $stmt_santri = mysqli_prepare($conn, $sql_santri);
        mysqli_stmt_bind_param($stmt_santri, 'i', $id_kelas);
        mysqli_stmt_execute($stmt_santri);
        $result_santri = mysqli_stmt_get_result($stmt_santri);
        $santri_data = mysqli_fetch_all($result_santri, MYSQLI_ASSOC);
        mysqli_stmt_close($stmt_santri);
    }
}

// Simpan data presensi
if (isset($_POST['simpan_presensi'])) {
    $status_data = $_POST['status'];
    $keterangan_data = $_POST['keterangan'];
    $tanggal = $_POST['tanggal'] ?? '';

    // Validasi format tanggal
    if (empty($tanggal) || !strtotime($tanggal)) {
        die("Tanggal tidak valid atau belum dipilih.");
    }

    foreach ($status_data as $id_santri => $status) {
        $keterangan = $keterangan_data[$id_santri] ?? '';
        $sql_insert = "INSERT INTO presensi (id_santri, tanggal, status, keterangan)
                       VALUES (?, ?, ?, ?)
                       ON DUPLICATE KEY UPDATE status = VALUES(status), keterangan = VALUES(keterangan)";
        $stmt = mysqli_prepare($conn, $sql_insert);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'isss', $id_santri, $tanggal, $status, $keterangan);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        } else {
            die("Kesalahan pada query: " . mysqli_error($conn));
        }
    }
    header("Location: presensi_santri.php?success=true");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Presensi Santri Per Kelas</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Presensi Santri Per Kelas</h2>

    <?php if (isset($_GET['success']) && $_GET['success'] === 'true') { ?>
        <div class="alert alert-success">Presensi berhasil disimpan!</div>
    <?php } ?>

    <!-- Form Pilih Unit, Kelas, dan Tanggal -->
    <form method="POST" class="mb-4">
        <div class="row">
        <div class="col-md-4">
                <label for="tanggal" class="form-label">Tanggal</label>
                <input type="date" name="tanggal" id="tanggal" class="form-control" value="<?= htmlspecialchars($_POST['tanggal'] ?? '') ?>" required>
            </div>
            <div class="col-md-4">
                <label for="unit" class="form-label">Pilih Unit</label>
                <select name="id_unit" id="unit" class="form-select" onchange="this.form.submit()" required>
                    <option value="">-- Pilih Unit --</option>
                    <?php while ($unit = mysqli_fetch_assoc($result_unit)) { ?>
                        <option value="<?= $unit['id'] ?>" <?= ($_POST['id_unit'] ?? '') == $unit['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($unit['nama_unit']) ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="kelas" class="form-label">Pilih Kelas</label>
                <select name="id_kelas" id="kelas" class="form-select" onchange="this.form.submit()" required>
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach ($kelas_data as $kelas) { ?>
                        <option value="<?= $kelas['id'] ?>" <?= ($_POST['id_kelas'] ?? '') == $kelas['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($kelas['nama_kelas']) ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
           
        </div>
    </form>

    <!-- Tabel Presensi -->
    <?php if (!empty($santri_data)) { ?>
        <form method="POST">
            <input type="hidden" name="id_unit" value="<?= htmlspecialchars($_POST['id_unit']) ?>">
            <input type="hidden" name="id_kelas" value="<?= htmlspecialchars($_POST['id_kelas']) ?>">
            <input type="hidden" name="tanggal" value="<?= htmlspecialchars($_POST['tanggal']) ?>">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Santri</th>
                    <th>Status</th>
                    <th>Keterangan</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($santri_data as $index => $santri) { ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($santri['nama_santri']) ?></td>
                        <td>
                            <select name="status[<?= $santri['id'] ?>]" class="form-select">
                                <option value="Hadir">Hadir</option>
                                <option value="Izin">Izin</option>
                                <option value="Sakit">Sakit</option>
                                <option value="Alpha">Alpha</option>
                            </select>
                        </td>
                        <td>
                            <input type="text" name="keterangan[<?= $santri['id'] ?>]" class="form-control">
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <button type="submit" name="simpan_presensi" class="btn btn-success">Simpan Presensi</button>
        </form>
    <?php } ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
