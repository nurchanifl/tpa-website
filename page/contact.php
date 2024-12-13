<?php include('../includes/koneksidb.php');  ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontak Kami</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="bg-primary text-white py-3">
        <div class="container">
            <h1 class="text-center">Kontak Kami</h1>
            <nav class="nav justify-content-center">
                <a class="nav-link text-white" href="../index.php">Beranda</a>
                <a class="nav-link text-white" href="activities.php">Kegiatan</a>
                <a class="nav-link text-white" href="schedule.php">Jadwal</a>
                <a class="nav-link text-white" href="add_schedule.php">Tambah Jadwal</a>
                <a class="nav-link text-white" href="contact.php">Kontak</a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container py-5">
        <h2 class="mb-4">Hubungi Kami</h2>
        <form action="contact_process.php" method="POST">
            <div class="mb-3">
                <label for="nama" class="form-label">Nama</label>
                <input type="text" class="form-control" id="nama" name="nama" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="pesan" class="form-label">Pesan</label>
                <textarea class="form-control" id="pesan" name="pesan" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Kirim</button>
        </form>
    </main>

    <!-- Footer -->
    <footer class="bg-primary text-white text-center py-3">
        <p>© 2024 TPA - Semua Hak Dilindungi</p>
    </footer>
</body>
</html>
