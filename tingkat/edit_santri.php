<?php
session_start();
include('../includes/koneksidb.php');
include('../includes/navbar.php');

// Pengecekan apakah pengguna sudah login dan memiliki hak akses
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Cek apakah parameter 'id' ada di URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];

    // Ambil data santri berdasarkan 'id'
    $sql = "SELECT * FROM santri WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $santri = mysqli_fetch_assoc($result);
        $id_unit = $santri['id_unit']; // Ambil id_unit dari data santri
    } else {
        header("Location: daftar_santri.php?error=notfound");
        exit();
    }

    mysqli_stmt_close($stmt);
} else {
    header("Location: daftar_santri.php?error=invalidid");
    exit();
}

// Ambil data unit untuk pilihan di form
$sql_unit = "SELECT * FROM unit";
$result_unit = mysqli_query($conn, $sql_unit);

// Ambil data kelas berdasarkan id_unit yang terkait dengan santri
$sql_kelas = "SELECT * FROM kelas WHERE id_unit = ?";
$stmt_kelas = mysqli_prepare($conn, $sql_kelas);
mysqli_stmt_bind_param($stmt_kelas, 'i', $id_unit);
mysqli_stmt_execute($stmt_kelas);
$result_kelas = mysqli_stmt_get_result($stmt_kelas);
mysqli_stmt_close($stmt_kelas);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Santri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Edit Data Santri</h2>
    <form method="POST" action="update_santri.php">
        <input type="hidden" name="id" value="<?= htmlspecialchars($santri['id']); ?>">

        <!-- Form Nama Santri -->
        <div class="form-group mb-3">
            <label for="nama">Nama Santri</label>
            <input type="text" name="nama" id="nama" class="form-control" value="<?= htmlspecialchars($santri['nama_santri']); ?>" required>
        </div>

        <!-- Dropdown Unit, pastikan unit yang sesuai dipilih -->
        <div class="form-group mb-3">
            <label for="unit">Unit</label>
            <select name="id_unit" id="unit" class="form-select" disabled>
                <?php while ($unit = mysqli_fetch_assoc($result_unit)) { ?>
                    <option value="<?= $unit['id'] ?>" <?= $unit['id'] == $id_unit ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($unit['nama_unit']) ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <!-- Dropdown Kelas, hanya menampilkan kelas sesuai dengan unit yang dipilih -->
        <div class="form-group mb-3">
            <label for="kelas">Kelas</label>
            <select name="kelas_id" id="kelas" class="form-select" required>
                <option value="">--Pilih Kelas--</option>
                <?php while ($kelas = mysqli_fetch_assoc($result_kelas)) { ?>
                    <option value="<?= $kelas['id'] ?>" <?= $kelas['id'] == $santri['id_kelas'] ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($kelas['nama_kelas']) ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="daftar_santri.php" class="btn btn-secondary">Batal</a>
    </form>
</div>
</body>
</html>
