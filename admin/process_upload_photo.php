<?php
include('../includes/koneksidb.php'); 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);

    // Proses file upload
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["foto"]["name"]);
    $uploadOk = 1;

    // Cek apakah file adalah gambar
    $check = getimagesize($_FILES["foto"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "<script>alert('File bukan gambar!'); window.history.back();</script>";
        $uploadOk = 0;
    }

    // Cek jika $uploadOk adalah 1
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
            $sql = "INSERT INTO galeri (judul, foto, deskripsi) VALUES ('$judul', '$target_file', '$deskripsi')";
            if (mysqli_query($conn, $sql)) {
                echo "<script>alert('Foto berhasil diupload!'); window.location.href = 'upload_photo.php';</script>";
            } else {
                echo "<script>alert('Gagal menyimpan ke database.'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('Gagal mengupload file.'); window.history.back();</script>";
        }
    }
}
?>
