<?php
include('../includes/koneksidb.php');
include('../includes/navbar.php');

// Ambil semua kategori dari tabel galeri_kategori
$kategori_result = mysqli_query($conn, "SELECT * FROM galeri_kategori");
$kategori_list = [];
while ($row = mysqli_fetch_assoc($kategori_result)) {
    $kategori_list[] = $row['nama'];
}

// Fungsi membuat thumbnail
function buatThumbnail($src, $dest, $max_size = 200) {
    list($width, $height) = getimagesize($src);
    $ratio = min($max_size / $width, $max_size / $height);
    $new_width = $width * $ratio;
    $new_height = $height * $ratio;

    $thumb = imagecreatetruecolor($new_width, $new_height);
    $image = imagecreatefromstring(file_get_contents($src));
    imagecopyresampled($thumb, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
    imagejpeg($thumb, $dest, 90); // Simpan thumbnail dengan kualitas 90%
    imagedestroy($thumb);
    imagedestroy($image);
}

// Proses pengunggahan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);

    if (!in_array($kategori, $kategori_list)) {
        die('<div class="alert alert-danger" role="alert">Kategori tidak valid!</div>');
    }

    $target_dir = "../uploads/";
    $thumbnail_dir = "../thumbnails/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
    if (!is_dir($thumbnail_dir)) mkdir($thumbnail_dir, 0777, true);

    $errors = [];
    $success_count = 0;
    $foto_files = is_array($_FILES['foto']['name']) ? $_FILES['foto']['name'] : [$_FILES['foto']['name']];
    $tmp_names = is_array($_FILES['foto']['tmp_name']) ? $_FILES['foto']['tmp_name'] : [$_FILES['foto']['tmp_name']];

    foreach ($foto_files as $key => $filename) {
        $foto = basename($filename);
        $tmp_name = $tmp_names[$key];
        $target_file = $target_dir . time() . '_' . $foto;
        $thumbnail_file = $thumbnail_dir . time() . '_' . $foto;
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        $file_ext = strtolower(pathinfo($foto, PATHINFO_EXTENSION));

        if (!in_array($file_ext, $allowed_ext)) {
            $errors[] = "File $foto tidak valid.";
            continue;
        }

        if (move_uploaded_file($tmp_name, $target_file)) {
            buatThumbnail($target_file, $thumbnail_file); // Buat thumbnail

            $sql = "INSERT INTO galeri (judul, deskripsi, kategori, foto, thumbnail, tanggal_upload) 
                    VALUES (?, ?, ?, ?, ?, NOW())";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'sssss', $judul, $deskripsi, $kategori, $target_file, $thumbnail_file);

            if (mysqli_stmt_execute($stmt)) {
                $success_count++;
            } else {
                $errors[] = "Gagal menyimpan $foto ke database.";
            }

            mysqli_stmt_close($stmt);
        } else {
            $errors[] = "Gagal mengunggah file $foto.";
        }
    }

    if ($success_count > 0) {
        echo '<div class="alert alert-success">' . $success_count . ' file berhasil diunggah!</div>';
    }
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo '<div class="alert alert-danger">' . htmlspecialchars($error) . '</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Folder ke Galeri</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <h1 class="text-center mb-4">Unggah Folder ke Galeri</h1>
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
                    <?php foreach ($kategori_list as $kategori): ?>
                        <option value="<?= htmlspecialchars($kategori) ?>"><?= htmlspecialchars($kategori) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="foto" class="form-label">Pilih Folder</label>
                <input type="file" class="form-control" id="foto" name="foto[]" accept="image/*" webkitdirectory multiple required>
            </div>
            <button type="submit" class="btn btn-primary">Unggah</button>
        </form>
    </div>
</body>
</html>