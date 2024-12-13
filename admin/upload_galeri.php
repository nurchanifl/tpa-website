<?php
include('../includes/koneksidb.php'); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $foto = $_FILES['foto']['name'];

    // Validasi kategori
    $kategori_valid = ['Kegiatan', 'Lomba', 'Pentas Seni'];
    if (!in_array($kategori, $kategori_valid)) {
        die('<div class="alert alert-danger" role="alert">Kategori tidak valid!</div>');
    }

    // Folder penyimpanan
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true); // Buat folder jika belum ada
    }

    // Validasi file
    $target_file = $target_dir . time() . '_' . basename($foto);
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
    $file_ext = strtolower(pathinfo($foto, PATHINFO_EXTENSION));
    $max_size = 20 * 1024 * 1024; // 20MB

    if (!in_array($file_ext, $allowed_ext)) {
        die('<div class="alert alert-danger" role="alert">Hanya file gambar yang diperbolehkan (JPG, JPEG, PNG, GIF).</div>');
    }
    if ($_FILES['foto']['size'] > $max_size) {
        die('<div class="alert alert-danger" role="alert">Ukuran file terlalu besar! Maksimal 20MB.</div>');
    }
    if (file_exists($target_file)) {
        die('<div class="alert alert-danger" role="alert">File dengan nama yang sama sudah ada. Harap ubah nama file Anda.</div>');
    }

    // Pindahkan file yang diunggah
    if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
        $sql = "INSERT INTO galeri (judul, deskripsi, kategori, foto, tanggal_upload) 
                VALUES (?, ?, ?, ?, NOW())";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ssss', $judul, $deskripsi, $kategori, $target_file);

        if (mysqli_stmt_execute($stmt)) {
            echo '<div class="alert alert-success" role="alert">Foto berhasil diunggah!</div>';
        } else {
            echo '<div class="alert alert-danger" role="alert">Gagal menyimpan ke database: ' . htmlspecialchars(mysqli_error($conn)) . '</div>';
        }

        mysqli_stmt_close($stmt);
    } else {
        echo '<div class="alert alert-danger" role="alert">Gagal mengunggah foto.</div>';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Foto Galeri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <h1 class="text-center mb-4">Unggah Foto ke Galeri</h1>
        <form action="upload_galeri.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="judul" class="form-label">Judul</label>
                <input type="text" class="form-control" id="judul" name="judul" required>
            </div>
            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="kategori" class="form-label">Kategori</label>
                <select class="form-select" id="kategori" name="kategori" required>
                    <option value="">Pilih Kategori</option>
                    <option value="Kegiatan">Kegiatan</option>
                    <option value="Lomba">Lomba</option>
                    <option value="Pentas Seni">Pentas Seni</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="foto" class="form-label">Upload Foto</label>
                <input type="file" class="form-control" id="foto" name="foto" accept="image/*" required>
            </div>
            <button type="submit" class="btn btn-primary">Unggah</button>
        </form>
    </div>
</body>
</html>

