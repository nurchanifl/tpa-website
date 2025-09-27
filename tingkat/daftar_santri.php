<?php
session_start();
include('../includes/koneksidb.php');
include('../includes/navbar.php');

// Ambil data unit dari database
$sql_unit = "SELECT * FROM unit";
$result_unit = mysqli_query($conn, $sql_unit);

// Ambil data santri berdasarkan kelas (jika ada kelas yang dipilih)
$santri_data = [];
$unit_nama = 'Unit tidak dipilih';
$kelas_nama = 'Kelas tidak dipilih';

// Proses data yang dikirimkan dari form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['unit_id']) && !empty($_POST['unit_id'])) {
        $unit_id = $_POST['unit_id'];

        // Ambil nama unit berdasarkan unit_id
        $sql_unit_nama = "SELECT nama_unit FROM unit WHERE id = ?";
        $stmt_unit = mysqli_prepare($conn, $sql_unit_nama);
        mysqli_stmt_bind_param($stmt_unit, 'i', $unit_id);
        mysqli_stmt_execute($stmt_unit);
        $result_unit_nama = mysqli_stmt_get_result($stmt_unit);
        $unit_nama = mysqli_fetch_assoc($result_unit_nama)['nama_unit'] ?? 'Unit tidak ditemukan';
        mysqli_stmt_close($stmt_unit);

        // Ambil data kelas berdasarkan unit_id
        $sql_kelas = "SELECT * FROM kelas WHERE id_unit = ?";
        $stmt_kelas = mysqli_prepare($conn, $sql_kelas);
        mysqli_stmt_bind_param($stmt_kelas, 'i', $unit_id);
        mysqli_stmt_execute($stmt_kelas);
        $result_kelas = mysqli_stmt_get_result($stmt_kelas);
        mysqli_stmt_close($stmt_kelas);
    }

    if (isset($_POST['kelas_id']) && !empty($_POST['kelas_id'])) {
        $kelas_id = $_POST['kelas_id'];

        // Ambil nama kelas berdasarkan kelas_id
        $sql_kelas_nama = "SELECT nama_kelas FROM kelas WHERE id = ?";
        $stmt_kelas_nama = mysqli_prepare($conn, $sql_kelas_nama);
        mysqli_stmt_bind_param($stmt_kelas_nama, 'i', $kelas_id);
        mysqli_stmt_execute($stmt_kelas_nama);
        $result_kelas_nama = mysqli_stmt_get_result($stmt_kelas_nama);
        $kelas_nama = mysqli_fetch_assoc($result_kelas_nama)['nama_kelas'] ?? 'Kelas tidak ditemukan';
        mysqli_stmt_close($stmt_kelas_nama);

        // Ambil data santri berdasarkan kelas_id
        $sql_santri = "SELECT * FROM santri WHERE id_kelas = ?";
        $stmt_santri = mysqli_prepare($conn, $sql_santri);
        mysqli_stmt_bind_param($stmt_santri, 'i', $kelas_id);
        mysqli_stmt_execute($stmt_santri);
        $result_santri = mysqli_stmt_get_result($stmt_santri);
        while ($santri = mysqli_fetch_assoc($result_santri)) {
            $santri_data[] = $santri;
        }
        mysqli_stmt_close($stmt_santri);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Santri</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>Daftar Santri</h1>

    <!-- Form Pilih Unit -->
    <form method="POST">
        <label for="unit">Pilih Unit:</label>
        <select name="unit_id" id="unit" class="form-select" onchange="this.form.submit()">
            <option value="">--Pilih Unit--</option>
            <?php while ($unit = mysqli_fetch_assoc($result_unit)) { ?>
                <option value="<?= $unit['id'] ?>" <?= isset($_POST['unit_id']) && $_POST['unit_id'] == $unit['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($unit['nama_unit']) ?>
                </option>
            <?php } ?>
        </select>
    </form>

    <!-- Form Pilih Kelas -->
    <?php if (isset($result_kelas)) { ?>
        <form method="POST">
            <input type="hidden" name="unit_id" value="<?= htmlspecialchars($unit_id) ?>">
            <label for="kelas">Pilih Kelas:</label>
            <select name="kelas_id" id="kelas" class="form-select" onchange="this.form.submit()">
                <option value="">--Pilih Kelas--</option>
                <?php while ($kelas = mysqli_fetch_assoc($result_kelas)) { ?>
                    <option value="<?= $kelas['id'] ?>" <?= isset($_POST['kelas_id']) && $_POST['kelas_id'] == $kelas['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($kelas['nama_kelas']) ?>
                    </option>
                <?php } ?>
            </select>
        </form>
    <?php } ?>

    <!-- Tampilkan Daftar Santri -->
    <?php if (!empty($santri_data)) { ?>
        <h3>Santri di Unit: <?= htmlspecialchars($unit_nama) ?>, Kelas: <?= htmlspecialchars($kelas_nama) ?></h3>
        <table class="table table-bordered mt-3">
            <thead>
            <tr>
                <th>No</th>
                <th>Nama Santri</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($santri_data as $index => $santri) { ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($santri['nama_santri']) ?></td>
                    <td>
                        <a href="edit_santri.php?id=<?= $santri['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="delete_santri.php?id=<?= $santri['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus santri ini?')">Hapus</a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php } ?>
</div>
</body>
</html>
