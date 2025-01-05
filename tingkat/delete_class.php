<?php
session_start();
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

// Ambil ID tingkat yang akan dihapus
if (isset($_GET['id'])) {
    $id_tingkat = $_GET['id'];

    // Hapus data tingkat dari database
    $sql = "DELETE FROM tingkat WHERE id_tingkat = '$id_tingkat'";
    
    if (mysqli_query($conn, $sql)) {
        $_SESSION['success_message'] = "Kelas berhasil dihapus!";
        header("Location: kelas.php");
        exit();
    } else {
        echo "<p>Error: " . mysqli_error($conn) . "</p>";
    }
}
?>
