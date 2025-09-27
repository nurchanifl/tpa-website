<?php
include '../config.php';  // Include config

// Membuat koneksi menggunakan config
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
