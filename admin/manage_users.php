<?php
session_start();
include('../includes/navbar.php');
include('../includes/koneksidb.php'); // Menyertakan koneksi database

// Pengecekan apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Pengecekan apakah pengguna adalah admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Query untuk mendapatkan data pengguna
$query = "SELECT id, username, role FROM users";
$result = mysqli_query($conn, $query);

// Memeriksa apakah query berhasil
if (!$result) {
    die("Query gagal: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengguna</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-primary text-white py-3">
        <div class="container">
            <h1 class="text-center">Kelola Pengguna</h1>
        </div>
    </header>
    <main class="container py-5">
        <h2>Daftar Pengguna</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Menampilkan data pengguna
                while ($user = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                        <td>{$user['id']}</td>
                        <td>{$user['username']}</td>
                        <td>{$user['role']}</td>
                        <td>
                            <a href='edit_user.php?id={$user['id']}' class='btn btn-warning btn-sm'>Edit</a>
                            <a href='delete_user.php?id={$user['id']}' class='btn btn-danger btn-sm'>Delete</a>
                        </td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Tombol untuk menambah pengguna -->
        <a href="tambah_user.php" class="btn btn-primary mt-3">Tambah Pengguna</a>
    </main>
    <footer class="mt-5">
        <?php include '../includes/footer.php'; ?>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Menutup koneksi
mysqli_close($conn);
?>
