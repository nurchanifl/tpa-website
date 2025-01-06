<?php
session_start();
include('../includes/koneksidb.php');
include('../includes/navbar.php');

// Cek apakah pengguna sudah login dan memiliki peran yang sesuai
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'user')) {
    header("Location: ../index.php");
    exit();
}

// Fungsi untuk membuat thumbnail dari gambar
function buatThumbnailGambar($file_path, $thumbnail_path, $lebar_thumbnail = 200) {
    list($lebar_asli, $tinggi_asli, $tipe_gambar) = getimagesize($file_path);

    switch ($tipe_gambar) {
        case IMAGETYPE_JPEG:
            $gambar_asli = imagecreatefromjpeg($file_path);
            break;
        case IMAGETYPE_PNG:
            $gambar_asli = imagecreatefrompng($file_path);
            break;
        case IMAGETYPE_GIF:
            $gambar_asli = imagecreatefromgif($file_path);
            break;
        default:
            return false; // Format tidak didukung
    }

    $tinggi_thumbnail = ($lebar_thumbnail / $lebar_asli) * $tinggi_asli;
    $thumbnail = imagecreatetruecolor($lebar_thumbnail, $tinggi_thumbnail);

    imagecopyresampled($thumbnail, $gambar_asli, 0, 0, 0, 0, $lebar_thumbnail, $tinggi_thumbnail, $lebar_asli, $tinggi_asli);
    
    // Simpan thumbnail berdasarkan tipe gambar
    switch ($tipe_gambar) {
        case IMAGETYPE_JPEG:
            imagejpeg($thumbnail, $thumbnail_path);
            break;
        case IMAGETYPE_PNG:
            imagepng($thumbnail, $thumbnail_path);
            break;
        case IMAGETYPE_GIF:
            imagegif($thumbnail, $thumbnail_path);
            break;
    }

    imagedestroy($gambar_asli);
    imagedestroy($thumbnail);
    return true;
}

// Fungsi untuk membuat thumbnail dari video
function buatThumbnailVideo($video_path, $thumbnail_path) {
    $command = "C:/ffmpeg/bin/ffmpeg -i \"$video_path\" -ss 00:00:01.000 -vframes 1 \"$thumbnail_path\"";
    exec($command, $output, $return_var);
    return $return_var === 0;
}

// Proses upload file
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);

    $target_dir = "../uploads/";
    $thumbnail_dir = "../thumbnails/";

    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
    if (!is_dir($thumbnail_dir)) mkdir($thumbnail_dir, 0777, true);

    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'avi', 'mov'];

    $errors = [];
    $success_count = 0;

    foreach ($_FILES['file']['name'] as $key => $file_name) {
        $tmp_name = $_FILES['file']['tmp_name'][$key];
        $file_size = $_FILES['file']['size'][$key];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (!in_array($file_ext, $allowed_ext)) {
            $errors[] = "File $file_name memiliki format tidak didukung.";
            continue;
        }

        $new_file_name = time() . '_' . uniqid() . '.' . $file_ext;
        $target_file = $target_dir . $new_file_name;
        $thumbnail_path = $thumbnail_dir . pathinfo($new_file_name, PATHINFO_FILENAME) . '.png';

        if (move_uploaded_file($tmp_name, $target_file)) {
            // Buat thumbnail jika file adalah gambar
            if (in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                if (!buatThumbnailGambar($target_file, $thumbnail_path)) {
                    $errors[] = "Gagal membuat thumbnail untuk $file_name.";
                }
            }

            // Buat thumbnail jika file adalah video
            if (in_array($file_ext, ['mp4', 'avi', 'mov'])) {
                if (!buatThumbnailVideo($target_file, $thumbnail_path)) {
                    $errors[] = "Gagal membuat thumbnail untuk $file_name.";
                }
            }

            // Simpan data ke database
            $sql = "INSERT INTO galeri (judul, deskripsi, kategori, foto, tanggal_upload) 
                    VALUES (?, ?, ?, ?, NOW())";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'ssss', $judul, $deskripsi, $kategori, $new_file_name);

            if (mysqli_stmt_execute($stmt)) {
                $success_count++;
            } else {
                $errors[] = "Gagal menyimpan $file_name ke database.";
            }

            mysqli_stmt_close($stmt);
        } else {
            $errors[] = "Gagal mengunggah file $file_name.";
        }
    }

    // Menampilkan hasil
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
    <title>Upload Galeri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container py-5">
        <h1 class="text-center mb-4">Upload Galeri</h1>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="judul" class="form-label">Judul</label>
                <input type="text" name="judul" id="judul" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea name="deskripsi" id="deskripsi" class="form-control" required></textarea>
            </div>
            <div class="mb-3">
                <label for="kategori" class="form-label">Kategori</label>
                <select name="kategori" id="kategori" class="form-select" required>
                    <option value="">Pilih Kategori</option>
                    <?php
                    $kategori_query = "SELECT nama FROM galeri_kategori ORDER BY nama ASC";
                    $kategori_result = mysqli_query($conn, $kategori_query);
                    while ($row = mysqli_fetch_assoc($kategori_result)) {
                        echo '<option value="' . htmlspecialchars($row['nama']) . '">' . htmlspecialchars($row['nama']) . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="file" class="form-label">Pilih File (Gambar/Video)</label>
                <input type="file" name="file[]" id="file" class="form-control" accept=".jpg,.jpeg,.png,.gif,.mp4,.avi,.mov" multiple required>
            </div>
            <button type="submit" class="btn btn-primary">Unggah</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>