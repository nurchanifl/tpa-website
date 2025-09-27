<?php
include('../includes/koneksidb.php'); 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $isi = mysqli_real_escape_string($conn, $_POST['isi']);

    $sql = "INSERT INTO pengumuman (judul, isi) VALUES ('$judul', '$isi')";
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Pengumuman berhasil ditambahkan!'); window.location.href = 'add_announcement.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan pengumuman.'); window.history.back();</script>";
    }
}
?>
