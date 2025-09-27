<?php
session_start();
include('../includes/koneksidb.php');
include('../includes/navbar.php');

// Pengecekan apakah pengguna adalah admin
if ($_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// Ambil data berdasarkan ID
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM jadwal_kegiatan WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
    } else {
        echo "<div class='alert alert-danger'>Jadwal tidak ditemukan.</div>";
        exit();
    }
} else {
    echo "<div class='alert alert-danger'>ID tidak valid.</div>";
    exit();
}

// Proses update data
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $nama_kegiatan = $_POST['nama_kegiatan'];
    $tanggal = $_POST['tanggal'];
    $waktu = $_POST['waktu'];
    $tempat = $_POST['tempat'];
    $deskripsi = $_POST['deskripsi'];

    $sql = "UPDATE jadwal_kegiatan 
            SET nama_kegiatan = ?, tanggal = ?, waktu = ?, tempat = ?, deskripsi = ? 
            WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'sssssi', $nama_kegiatan, $tanggal, $waktu, $tempat, $deskripsi, $id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../page/schedule.php?status=updated");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Gagal memperbarui jadwal: " . mysqli_error($conn) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Jadwal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <h1 class="mb-4">Edit Jadwal Kegiatan</h1>
        <form action="edit_schedule.php?id=<?php echo $row['id']; ?>" method="POST">
            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

            <div class="mb-3">
                <label for="nama_kegiatan" class="form-label">Nama Kegiatan:</label>
                <input type="text" name="nama_kegiatan" id="nama_kegiatan" class="form-control" value="<?php echo htmlspecialchars($row['nama_kegiatan']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="tanggal" class="form-label">Tanggal:</label>
                <input type="date" name="tanggal" id="tanggal" class="form-control" value="<?php echo htmlspecialchars($row['tanggal']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="waktu" class="form-label">Waktu:</label>
                <input type="time" name="waktu" id="waktu" class="form-control" value="<?php echo htmlspecialchars($row['waktu']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="tempat" class="form-label">Tempat:</label>
                <input type="text" name="tempat" id="tempat" class="form-control" value="<?php echo htmlspecialchars($row['tempat']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi:</label>
                <textarea name="deskripsi" id="deskripsi" class="form-control" required><?php echo htmlspecialchars($row['deskripsi']); ?></textarea>
            </div>

            <button type="submit" name="update" class="btn btn-primary">Update</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>