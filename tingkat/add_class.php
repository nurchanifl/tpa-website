<?php
session_start();
include('../includes/koneksidb.php');

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

// Proses saat formulir disubmit
if (isset($_POST['submit'])) {
    $nama_tingkat = mysqli_real_escape_string($conn, $_POST['nama_tingkat']);
    
    $sql = "INSERT INTO tingkat (nama_tingkat) VALUES ('$nama_tingkat')";
    
    if (mysqli_query($conn, $sql)) {
        $_SESSION['success_message'] = "Kelas berhasil ditambahkan!";
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
    <title>Tambah Kelas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-primary text-white py-3">
        <div class="container">
            <h1 class="text-center">Tambah Kelas</h1>
        </div>
    </header>

    <main class="container py-5">
        <h2>Formulir Tambah Kelas</h2>

        <form method="POST">
            <div class="mb-3">
                <label for="nama_tingkat" class="form-label">Nama Kelas</label>
                <input type="text" id="nama_tingkat" name="nama_tingkat" class="form-control" required>
            </div>

            <button type="submit" name="submit" class="btn btn-primary">Tambah Kelas</button>
            <a href="kelas.php" class="btn btn-secondary">Kembali</a>
        </form>
    </main>

    <footer class="bg-primary text-white text-center py-3">
        <p>© 2024 TPA - Semua Hak Dilindungi</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
