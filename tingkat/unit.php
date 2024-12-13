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

// Ambil data unit dari database
$sql = "SELECT * FROM unit ORDER BY nama_unit";
$result = mysqli_query($conn, $sql);

// Cek apakah query berhasil
if (!$result) {
    die("Error: " . mysqli_error($conn)); // Menampilkan error jika query gagal
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kelas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <header class="bg-primary text-white py-3">
        <div class="container">
            <h1 class="text-center">Kelola Unit</h1>
        </div>
    </header>

    <main class="container py-5">
        <h2>Daftar Unit</h2>
        
        <!-- Tombol untuk menambah unit -->
        <a href="add_class.php" class="btn btn-success mb-3">Tambah Unit</a>

        <?php
        if (mysqli_num_rows($result) > 0) {
            echo "<table class='table table-bordered'>";
            echo "<thead><tr><th>ID</th><th>Nama Tingkat</th><th>Aksi</th></tr></thead><tbody>";
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['id']) . "</td>"; // Menggunakan htmlspecialchars untuk mencegah XSS
                echo "<td>" . htmlspecialchars($row['nama_unit']) . "</td>";
                echo "<td>
                    <a href='edit_class.php?id=" . htmlspecialchars($row['id']) . "' class='btn btn-warning btn-sm'>Edit</a>
                    <a href='delete_class.php?id=" . htmlspecialchars($row['id']) . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Apakah Anda yakin ingin menghapus Unit ini?\")'>Hapus</a>
                </td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p>Belum ada unit yang terdaftar.</p>";
        }
        ?>
    </main>

    <footer class="bg-primary text-white text-center py-3">
        <p>© 2024 TPA - Semua Hak Dilindungi</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
