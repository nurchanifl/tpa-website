<?php
session_start();
include('../includes/koneksidb.php');

// Pengecekan apakah pengguna sudah login dan memiliki hak akses
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Proses update data santri
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id']) && isset($_POST['kelas_id'])) {
        $id = $_POST['id'];
        $kelas_id = $_POST['kelas_id'];

        // Update data santri
        $sql = "UPDATE santri SET id_kelas = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ii', $kelas_id, $id);
        
        if (mysqli_stmt_execute($stmt)) {
            header("Location: daftar_santri.php?success=updated");
        } else {
            header("Location: edit_santri.php?id=$id&error=updatefailed");
        }

        mysqli_stmt_close($stmt);
    }
}
?>
