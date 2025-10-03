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
?>
