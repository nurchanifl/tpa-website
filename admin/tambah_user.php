<?php
session_start();
include('../includes/navbar.php');
include('../includes/koneksidb.php'); // Menyertakan koneksi database;

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

// Proses saat form disubmit
if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Validasi password
    if (strlen($password) < 6) {
        $error_message = "Password harus memiliki minimal 6 karakter.";
    } else {
        // Enkripsi password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Menyimpan data pengguna ke database
        $query = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);

        // Mengikat parameter dan mengeksekusi query
        mysqli_stmt_bind_param($stmt, 'sss', $username, $password_hash, $role);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: manage_users.php"); // Kembali ke halaman pengelolaan user
            exit();
        } else {
            $error_message = "Gagal menambahkan pengguna.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-primary text-white py-3">
        <div class="container">
            <h1 class="text-center">Tambah User</h1>
        </div>
    </header>

    <main class="container py-5">
        <h2>Tambah Pengguna Baru</h2>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select id="role" name="role" class="form-select" required>
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <button type="submit" name="submit" class="btn btn-primary">Tambah Pengguna</button>
            <a href="manage_users.php" class="btn btn-secondary">Batal</a>
        </form>
    </main>

    <footer class="mt-5">
        <?php include '../includes/footer.php'; ?>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
