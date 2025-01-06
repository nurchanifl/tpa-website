<?php
session_start();
include('../includes/koneksidb.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo '<div class="alert alert-danger">Akses ditolak! Hanya admin yang dapat menghapus file.</div>';
    exit;
}

// Periksa apakah parameter ID ada di URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo '<div class="alert alert-danger">ID tidak valid!</div>';
    exit;
}

$id = intval($_GET['id']);

// Cari file di database berdasarkan ID
$query = "SELECT foto FROM galeri WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    // Menyusun jalur absolut untuk file utama dan thumbnail
    $file_path = '../uploads/' . $row['foto'];
    $file_ext = pathinfo($row['foto'], PATHINFO_EXTENSION);
    $thumbnail_path = '../thumbnails/' . basename($row['foto'], '.' . $file_ext) . '.png';

    // Hapus file utama jika ada
    if (file_exists($file_path)) {
        if (unlink($file_path)) {
            echo '<div class="alert alert-success">File utama berhasil dihapus!</div>';
        } else {
            echo '<div class="alert alert-danger">Gagal menghapus file utama!</div>';
        }
    } else {
        echo '<div class="alert alert-warning">File utama tidak ditemukan!</div>';
    }

    // Hapus file thumbnail jika ada
    if (file_exists($thumbnail_path)) {
        if (unlink($thumbnail_path)) {
            echo '<div class="alert alert-success">Thumbnail berhasil dihapus!</div>';
        } else {
            echo '<div class="alert alert-danger">Gagal menghapus thumbnail!</div>';
        }
    } else {
        echo '<div class="alert alert-warning">Thumbnail tidak ditemukan!</div>';
    }

    // Hapus entri dari database
    $delete_query = "DELETE FROM galeri WHERE id = ?";
    $delete_stmt = mysqli_prepare($conn, $delete_query);
    mysqli_stmt_bind_param($delete_stmt, 'i', $id);

    if (mysqli_stmt_execute($delete_stmt)) {
        echo '<div class="alert alert-success">File dan entri database berhasil dihapus!</div>';
        header("Location: galeri.php?status=deleted");
        exit;
    } else {
        echo '<div class="alert alert-danger">Gagal menghapus entri dari database!</div>';
    }
} else {
    echo '<div class="alert alert-danger">File tidak ditemukan!</div>';
}

// Tutup koneksi
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>