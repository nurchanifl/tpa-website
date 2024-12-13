<?php
session_start();
include('../includes/koneksidb.php');
include ('../includes/navbar.php'); 

// Pengecekan apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

// Ambil jadwal kegiatan dari database dengan prepared statement
$sql = "SELECT * FROM jadwal_kegiatan ORDER BY tanggal, waktu";
$result = mysqli_query($conn, $sql);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Kegiatan TPA</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <style>
       
    </style>
</head>
<body>
    <header class="bg-primary text-white py-3">
        <div class="container">
            <h1 class="text-center">Jadwal Kegiatan TPA</h1>
        </div>
    </header>
    <main class="container py-5">
        <h2 class="mb-4">Daftar Jadwal Kegiatan</h2>
        
        <?php if ($_SESSION['role'] == 'admin'): ?>
            <!-- Tombol untuk menambah jadwal hanya untuk admin -->
            <div class="d-flex justify-content-end mb-3">
                <a href="add_schedule.php" class="btn btn-success">+ Tambah Jadwal</a>
            </div>
        <?php endif; ?>

        <?php
        if (mysqli_num_rows($result) > 0): ?>
            <table class="table table-bordered table-hover text-center">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Waktu</th>
                        <th>Nama Kegiatan</th>
                        <th>Tempat</th>
                        <th>Deskripsi</th>
                        <?php if ($_SESSION['role'] == 'admin'): ?>
                            <th>Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['tanggal']); ?></td>
                            <td><?= htmlspecialchars($row['waktu']); ?></td>
                            <td><?= htmlspecialchars($row['nama_kegiatan']); ?></td>
                            <td><?= htmlspecialchars($row['tempat']); ?></td>
                            <td><?= htmlspecialchars($row['deskripsi']); ?></td>
                            <?php if ($_SESSION['role'] == 'admin'): ?>
                                <td>
                                    <a href="edit_schedule.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="delete_schedule.php?id=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?')">Hapus</a>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info">
                Belum ada jadwal kegiatan.
            </div>
        <?php endif; ?>
    </main>
    <footer class="mt-5">
        <?php include '../includes/footer.php'; ?>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
        });
    </script>
</body>
</html>
