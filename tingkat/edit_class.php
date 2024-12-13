<?php
session_start();
include('../includes/koneksidb.php');
include('../includes/navbar.php');

// Pengecekan apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Pengecekan apakah pengguna adalah admin
if ($_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Ambil ID tingkat yang akan diedit
if (isset($_GET['id'])) {
    $id_tingkat = $_GET['id'];

    // Ambil data tingkat dari database
    $sql = "SELECT * FROM tingkat WHERE id_tingkat = '$id_tingkat'";
    $result = mysqli_query($conn, $sql);
    $tingkat = mysqli_fetch_assoc($result);

    if (!$tingkat) {
        echo "<p>Kelas tidak ditemukan.</p>";
        exit();
    }
}

// Proses saat formulir disubmit
if (isset($_POST['submit'])) {
    $nama_tingkat = mysqli_real_escape_string($conn, $_POST['nama_tingkat']);
    
    $sql = "UPDATE tingkat SET nama_tingkat = '$nama_tingkat' WHERE id_tingkat = '$id_tingkat'";
    
    if (mysqli_query($conn, $sql)) {
        $_SESSION['success_message'] = "Kelas berhasil diperbarui!";
        header("Location: kelas.php");
        exit();
    } else {
        echo "<p>Error: " . mysqli_error($conn) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kelas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-primary text-white py-3">
        <div class="container">
            <h1 class="text-center">Edit Kelas</h1>
        </div>
    </header>

    <main class="container py-5">
        <h2>Formulir Edit Kelas</h2>

        <form method="POST">
            <div class="mb-3">
                <label for="nama_tingkat" class="form-label">Nama Kelas</label>
                <input type="text" id="nama_tingkat" name="nama_tingkat" class="form-control" value="<?php echo $tingkat['nama_tingkat']; ?>" required>
            </div>

            <button type="submit" name="submit" class="btn btn-primary">Perbarui Kelas</button>
            <a href="kelas.php" class="btn btn-secondary">Kembali</a>
        </form>
    </main>

    <footer class="bg-primary text-white text-center py-3">
        <p>© 2024 TPA - Semua Hak Dilindungi</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
