<?php 
session_start();
include('../includes/koneksidb.php');
include('../includes/navbar.php');

// Pengecekan apakah pengguna adalah admin
if ($_SESSION['role'] != 'admin') {
    // Jika bukan admin, arahkan ke halaman beranda atau halaman lain
    header("Location: ../index.php");
    exit();
}

  ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Jadwal Kegiatan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <main class="container py-5">
        <section>     
            <h2 class="mb-4">Tambah Jadwal Baru</h2>
            <form action="add_schedule_process.php" method="POST">
                <div class="mb-3">
                    <label for="nama_kegiatan" class="form-label">Nama Kegiatan</label>
                    <input type="text" class="form-control" id="nama_kegiatan" name="nama_kegiatan" required>
                </div>
                <div class="mb-3">
                    <label for="tanggal" class="form-label">Tanggal</label>
                    <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                </div>
                <div class="mb-3">
                    <label for="waktu" class="form-label">Waktu</label>
                    <input type="time" class="form-control" id="waktu" name="waktu" required>
                </div>
                <div class="mb-3">
                    <label for="tempat" class="form-label">Tempat</label>
                    <input type="text" class="form-control" id="tempat" name="tempat" required>
                </div>
                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Tambah Jadwal</button>
            </form>
            <?php
            if (isset($_POST['submit'])) {
                $nama_kegiatan = $_POST['nama_kegiatan'];
                $tanggal = $_POST['tanggal'];
                $waktu = $_POST['waktu'];
                $tempat = $_POST['tempat'];
                $deskripsi = $_POST['deskripsi'];

                $sql = "INSERT INTO jadwal_kegiatan (nama_kegiatan, tanggal, waktu, tempat, deskripsi) 
                        VALUES ('$nama_kegiatan', '$tanggal', '$waktu', '$tempat', '$deskripsi')";

                if (mysqli_query($conn, $sql)) {
                    echo "<p>Jadwal kegiatan berhasil ditambahkan!</p>";
                } else {
                    echo "<p>Terjadi kesalahan: " . mysqli_error($conn) . "</p>";
                }
            }
            ?>
        </section>
    </main>
    <footer class="mt-5">
        <?php include '../includes/footer.php'; ?>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
