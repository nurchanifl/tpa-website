<?php
session_start();
include('../includes/db_connection.php');

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

// Menghapus pengguna berdasarkan id
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $query = "DELETE FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'i', $user_id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: manage_users.php"); // Kembali ke halaman pengelolaan user
        exit();
    } else {
        echo "Gagal menghapus pengguna.";
    }
} else {
    echo "ID pengguna tidak ditemukan.";
}
?>
