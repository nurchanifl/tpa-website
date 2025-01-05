<?php
include('../includes/koneksidb.php'); 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pesan = mysqli_real_escape_string($conn, $_POST['pesan']);

    $sql = "INSERT INTO kontak (nama, email, pesan) VALUES ('$nama', '$email', '$pesan')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Pesan berhasil dikirim!'); window.location.href = 'contact.php';</script>";
    } else {
        echo "<script>alert('Gagal mengirim pesan. Silakan coba lagi.'); window.location.href = 'contact.php';</script>";
    }
}
?>
