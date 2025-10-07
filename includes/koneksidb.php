<?php
include_once __DIR__ . '/../config.php';

$servername = $db_host;
$username = $db_user;
$password = $db_pass;
$dbname = $db_name;

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); // Jika gagal, tampilkan pesan
}

// Fungsi untuk check remember me
function check_remember_login($conn) {
    if (!isset($_SESSION['id']) && isset($_COOKIE['remember_token'])) {
        $token = $_COOKIE['remember_token'];
        $query = $conn->prepare("SELECT u.id, u.username, u.role FROM user_tokens ut JOIN users u ON ut.user_id = u.id WHERE ut.token = ? AND ut.expires_at > NOW()");
        $query->bind_param("s", $token);
        $query->execute();
        $result = $query->get_result();
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
        } else {
            // Token invalid, delete cookie
            setcookie('remember_token', '', time() - 3600, '/');
        }
        $query->close();
    }
}

// Panggil fungsi jika session belum ada
if (session_status() == PHP_SESSION_ACTIVE) {
    check_remember_login($conn);
}
?>
