<?php
session_start();
include('includes/koneksidb.php');

// Logika login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $remember_me = isset($_POST['remember_me']);

    $query = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $query->bind_param("s", $username);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $user['role'];
            $_SESSION['success_message'] = "Selamat datang, $username!";

            // Handle remember me
            if ($remember_me) {
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+30 days'));
                $insert_token = $conn->prepare("INSERT INTO user_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
                $insert_token->bind_param("iss", $user['id'], $token, $expires);
                $insert_token->execute();
                $insert_token->close();

                setcookie('remember_token', $token, strtotime('+30 days'), '/', '', false, true);
            }

            // Arahkan berdasarkan role
            if ($_SESSION['role'] === 'admin') {
                header("Location: admin/dashboard.php");
            } else if ($_SESSION['role'] === 'user') {
                header("Location: page/dashboard_user.php");
            } else {
                header("Location: index.php");
            }
            exit;
        } else {
            $error_message = "Password salah!";
        }
    } else {
        $error_message = "Username tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="manifest" href="manifest.json">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Login</h1>
        
        <?php
        if (isset($error_message)) {
            echo "<div class='alert alert-danger'>$error_message</div>";
        }
        ?>

        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="remember_me" id="remember_me" class="form-check-input">
                <label for="remember_me" class="form-check-label">Ingat Saya</label>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('sw.js')
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
