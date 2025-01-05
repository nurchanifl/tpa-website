<?php
session_start();
include('../includes/navbar.php');
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

// Mengecek apakah ada ID pengguna yang dikirimkan untuk diedit
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    
    // Mendapatkan data pengguna dari database
    $query = "SELECT id, username, role FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 0) {
        die("User tidak ditemukan.");
    }
    
    $user = mysqli_fetch_assoc($result);
} else {
    die("ID pengguna tidak diberikan.");
}

// Proses saat form disubmit
if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $role = $_POST['role'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validasi password baru
    if (!empty($new_password) && !empty($confirm_password)) {
        if ($new_password !== $confirm_password) {
            $error_message = "Password baru dan konfirmasi password tidak cocok.";
        } elseif (strlen($new_password) < 6) {
            $error_message = "Password baru harus memiliki minimal 6 karakter.";
        } else {
            // Enkripsi password baru
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $password_query = ", password = ?"; // Menandakan ada perubahan password
            $bind_types = 'sssi'; // Menambahkan password ke tipe data yang diikat
            $bind_values = [$username, $role, $new_password_hash, $user_id];
        }
    } else {
        $password_query = ""; // Tidak ada perubahan password
        $bind_types = 'ssi'; // Hanya username dan role yang diikat
        $bind_values = [$username, $role, $user_id];
    }

    // Update data pengguna
    $update_query = "UPDATE users SET username = ?, role = ? $password_query WHERE id = ?";
    $update_stmt = mysqli_prepare($conn, $update_query);

    // Mengikat parameter dengan urutan yang benar
    mysqli_stmt_bind_param($update_stmt, $bind_types, ...$bind_values);
    
    if (mysqli_stmt_execute($update_stmt)) {
        header("Location: manage_users.php"); // Kembali ke halaman pengelolaan user
        exit();
    } else {
        $error_message = "Gagal memperbarui data pengguna.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-primary text-white py-3">
        <div class="container">
            <h1 class="text-center">Edit User</h1>
        </div>
    </header>

    <main class="container py-5">
        <h2>Edit Informasi Pengguna</h2>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" name="username" class="form-control" value="<?php echo $user['username']; ?>" required>
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select id="role" name="role" class="form-select" required>
                    <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                    <option value="user" <?php echo ($user['role'] == 'user') ? 'selected' : ''; ?>>User</option>
                </select>
            </div>

            <!-- Form untuk mengganti password -->
            <div class="mb-3">
                <label for="new_password" class="form-label">Password Baru</label>
                <input type="password" id="new_password" name="new_password" class="form-control" placeholder="Kosongkan jika tidak ingin mengganti password">
            </div>

            <div class="mb-3">
                <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Kosongkan jika tidak ingin mengganti password">
            </div>

            <button type="submit" name="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="manage_users.php" class="btn btn-secondary">Batal</a>
        </form>
    </main>

    <footer class="mt-5">
        <?php include '../includes/footer.php'; ?>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
