<?php
session_start();
include('../includes/koneksidb.php');
include('../includes/navbar.php');

// Ambil data rating dan testimoni dari form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_pengguna = mysqli_real_escape_string($conn, $_POST['nama_pengguna']);
    $rating = mysqli_real_escape_string($conn, $_POST['rating']);

    // Ambil input dari form dan ganti newline
    $testimoni = str_replace(array("\r\n", "\r"), "\n", $_POST['testimoni']);

    // Mengamankan input
    $testimoni = mysqli_real_escape_string($conn, $testimoni);

    // Simpan ke dalam database
    $sql = "INSERT INTO rating_testimoni (nama_pengguna, rating, testimoni, tanggal)
            VALUES (?, ?, ?, NOW())";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'sis', $nama_pengguna, $rating, $testimoni);

    if (mysqli_stmt_execute($stmt)) {
        $success_message = "Terima kasih atas testimoni Anda!";
    } else {
        $error_message = "Gagal menyimpan testimoni: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);

}

// Ambil data rating dan testimoni dari database
$sql = "SELECT * FROM rating_testimoni ORDER BY tanggal DESC";
$result = mysqli_query($conn, $sql);

// Periksa apakah query berhasil dijalankan
if (!$result) {
    die("Error: " . mysqli_error($conn));
}

// Menghitung rata-rata rating
$avg_sql = "SELECT AVG(rating) AS rata_rata FROM rating_testimoni";
$avg_result = mysqli_query($conn, $avg_sql);
if (!$avg_result) {
    die("Error: " . mysqli_error($conn));
}
$avg_row = mysqli_fetch_assoc($avg_result);
$rata_rata_rating = $avg_row['rata_rata'] ? round($avg_row['rata_rata'], 2) : 0;

// Fungsi untuk menampilkan bintang rating
function tampilkan_rating($rating) {
    $full_stars = floor($rating);
    $half_star = ($rating - $full_stars) >= 0.5 ? true : false;
    $empty_stars = 5 - $full_stars - ($half_star ? 1 : 0);
    
    $stars = '';
    for ($i = 0; $i < $full_stars; $i++) {
        $stars .= '<span class="bi bi-star-fill text-warning"></span>';
    }
    if ($half_star) {
        $stars .= '<span class="bi bi-star-half text-warning"></span>';
    }
    for ($i = 0; $i < $empty_stars; $i++) {
        $stars .= '<span class="bi bi-star text-muted"></span>';
    }

    return $stars;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testimoni</title>
    <link rel="manifest" href="../manifest.json">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Testimoni Pengguna</h1>
        <div class="text-center mb-5">
            <h3>Rata-rata Rating: <span class="badge bg-warning text-dark fs-5"><?= $rata_rata_rating ?>/5</span></h3>
            <div class="mb-4">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <span class="bi bi-star<?= $i <= round($rata_rata_rating) ? '-fill' : '' ?> text-warning fs-4"></span>
                <?php endfor; ?>
            </div>
        </div>

        <!-- Form untuk beri testimoni -->
        <div class="card shadow mb-5">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Berikan Testimoni Anda</h5>
            </div>
            <div class="card-body">
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success"><?= $success_message ?></div>
                <?php endif; ?>
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger"><?= $error_message ?></div>
                <?php endif; ?>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="nama_pengguna" class="form-label">Nama Anda</label>
                        <input type="text" class="form-control" id="nama_pengguna" name="nama_pengguna" required>
                    </div>
                    <div class="mb-3">
                        <label for="rating" class="form-label">Rating</label>
                        <select id="rating" name="rating" class="form-select" required>
                            <option value="">Pilih Rating</option>
                            <option value="5">5 - Sangat Baik</option>
                            <option value="4">4 - Baik</option>
                            <option value="3">3 - Cukup</option>
                            <option value="2">2 - Buruk</option>
                            <option value="1">1 - Sangat Buruk</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="testimoni" class="form-label">Testimoni</label>
                        <textarea class="form-control" id="testimoni" name="testimoni" rows="4" placeholder="Tulis testimoni Anda di sini..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i> Kirim Testimoni</button>
                </form>
            </div>
        </div>

        <h2 class="mb-4">Testimoni Terbaru</h2>
        <?php
        // Menampilkan data testimoni
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<div class="card mb-3 shadow-sm testimonial-card">';
                echo '<div class="card-body">';
                echo '<div class="d-flex align-items-center mb-2">';
                echo '<h5 class="card-title me-3">' . htmlspecialchars($row['nama_pengguna']) . '</h5>';
                echo '<div>' . tampilkan_rating($row['rating']) . '</div>';
                echo '</div>';
                echo '<p class="card-text">' . nl2br(htmlspecialchars($row['testimoni'])) . '</p>';
                echo '<p class="card-text"><small class="text-muted"><i class="bi bi-calendar"></i> ' . date('d F Y', strtotime($row['tanggal'])) . '</small></p>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo '<div class="alert alert-info">Belum ada testimoni. Jadilah yang pertama!</div>';
        }
        ?>
    </div>

    <style>
        .testimonial-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .testimonial-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
    </style>
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('../sw.js')
                .then(function(registration) {
                    console.log('Service Worker registered successfully:', registration);
                })
                .catch(function(error) {
                    console.log('Service Worker registration failed:', error);
                });
        }
    </script>
</body>
</html>
