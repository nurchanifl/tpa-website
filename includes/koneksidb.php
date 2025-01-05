<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tpa_db"; // Ganti dengan nama database Anda jika berbeda

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); // Jika gagal, tampilkan pesan
}
?>
