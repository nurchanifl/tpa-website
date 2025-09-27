<?php
$servername = "localhost";
$username = "siputlai_tpa";
$password = "nabilahjkt48";
$dbname = "siputlai_tpa_db";

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
