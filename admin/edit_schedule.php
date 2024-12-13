<?php
include('../includes/koneksidb.php'); 

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php"); // Arahkan ke halaman beranda jika bukan admin
    exit();
}

// Ambil data berdasarkan ID
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM jadwal_kegiatan WHERE id = $id";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
    } else {
        echo "Jadwal tidak ditemukan.";
        exit;
    }
}

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $nama_kegiatan = $_POST['nama_kegiatan'];
    $tanggal = $_POST['tanggal'];
    $waktu = $_POST['waktu'];
    $tempat = $_POST['tempat'];
    $deskripsi = $_POST['deskripsi'];

    // Query update data
    $sql = "UPDATE jadwal_kegiatan 
            SET nama_kegiatan = '$nama_kegiatan', tanggal = '$tanggal', waktu = '$waktu', tempat = '$tempat', deskripsi = '$deskripsi' 
            WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
        header("Location: schedule.php?status=updated");
        exit;
    } else {
        echo "Gagal memperbarui jadwal: " . mysqli_error($conn);
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <h1>Edit Jadwal Kegiatan</h1>
    <form action="edit_schedule.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

        <label for="nama_kegiatan">Nama Kegiatan:</label>
        <input type="text" name="nama_kegiatan" id="nama_kegiatan" value="<?php echo $row['nama_kegiatan']; ?>" required><br>

        <label for="tanggal">Tanggal:</label>
        <input type="date" name="tanggal" id="tanggal" value="<?php echo $row['tanggal']; ?>" required><br>

        <label for="waktu">Waktu:</label>
        <input type="time" name="waktu" id="waktu" value="<?php echo $row['waktu']; ?>" required><br>

        <label for="tempat">Tempat:</label>
        <input type="text" name="tempat" id="tempat" value="<?php echo $row['tempat']; ?>" required><br>

        <label for="deskripsi">Deskripsi:</label>
        <textarea name="deskripsi" id="deskripsi"><?php echo $row['deskripsi']; ?></textarea><br>

        <button type="submit" name="update">Update</button>
    </form>
</body>
</html>
