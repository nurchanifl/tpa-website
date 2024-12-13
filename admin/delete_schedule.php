<?php
include('../includes/koneksidb.php'); 

if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php"); // Arahkan ke halaman beranda jika bukan admin
    exit();
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    
    // Query hapus data
    $sql = "DELETE FROM jadwal_kegiatan WHERE id = $id";
    if (mysqli_query($conn, $sql)) {
        header("Location: schedule.php?status=success");
        exit;
    } else {
        echo "Gagal menghapus jadwal: " . mysqli_error($conn);
    }
} else {
    echo "ID tidak valid.";
}
?>
