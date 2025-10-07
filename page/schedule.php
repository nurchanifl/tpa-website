<?php
session_start();
include('../includes/koneksidb.php');
include('../includes/navbar.php');

// Ambil jadwal kegiatan dari database
$sql = "SELECT * FROM jadwal_kegiatan ORDER BY tanggal, waktu";
$result = mysqli_query($conn, $sql);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Kegiatan TPA</title>
    <link rel="manifest" href="../manifest.json">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <main class="container py-5">
        <h2 class="text-center mb-4">Daftar Jadwal Kegiatan</h2>

        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
            <!-- Tombol untuk menambah jadwal hanya untuk admin -->
            <div class="d-flex justify-content-end mb-3">
                <a href="../admin/add_schedule.php" class="btn btn-success">+ Tambah Jadwal</a>
            </div>
        <?php endif; ?>

        <?php
        if (mysqli_num_rows($result) > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center">
                    <thead class="table-primary">
                        <tr>
                            <th>Tanggal</th>
                            <th>Waktu</th>
                            <th>Nama Kegiatan</th>
                            <th>Tempat</th>
                            <th>Deskripsi</th>
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
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
                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                                    <td>
                                        <a href="../admin/edit_schedule.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <a href="../admin/delete_schedule.php?id=<?= $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?')">Hapus</a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                Belum ada jadwal kegiatan.
            </div>
        <?php endif; ?>
    </main>
    <footer class="mt-5">
        <?php include '../includes/footer.php'; ?>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('../sw.js')
                .then(function(registration) {
                    console.log('Service Worker registered successfully:', registration);
                })
                .catch(function(error) {
                    console.log('Service Worker registration failed:', error);
                });
        }
    </script>
</body>
</html>
