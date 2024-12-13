<?php 

session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['role'] != 'admin') {
    echo "<script>alert('Anda tidak memiliki hak akses!'); window.location.href = 'index.php';</script>";
    exit();
}


include('../includes/koneksidb.php');  ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pengumuman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-primary text-white py-3">
        <div class="container">
            <h1 class="text-center">Tambah Pengumuman</h1>
        </div>
    </header>
    <main class="container py-5">
        <h2 class="mb-4">Tambah Pengumuman Baru</h2>
        <form action="process_announcement.php" method="POST">
            <div class="mb-3">
                <label for="judul" class="form-label">Judul Pengumuman</label>
                <input type="text" class="form-control" id="judul" name="judul" required>
            </div>
            <div class="mb-3">
                <label for="isi" class="form-label">Isi Pengumuman</label>
                <textarea class="form-control" id="isi" name="isi" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Tambah Pengumuman</button>
        </form>
    </main>
    <footer class="bg-primary text-white text-center py-3">
        <p>© 2024 TPA - Semua Hak Dilindungi</p>
    </footer>
</body>
</html>
