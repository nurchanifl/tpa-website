<?php
include('../includes/koneksidb.php'); 

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
        echo "Testimoni berhasil disimpan!";
    } else {
        echo "Gagal menyimpan testimoni: " . mysqli_error($conn);
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Testimoni Pengguna</h1>
        <h2>Rata-rata Rating: <?= $rata_rata_rating ?>/5</h2>

        

        <?php
        // Menampilkan data testimoni
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<div class="card mb-3">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . htmlspecialchars($row['nama_pengguna']) . '</h5>';
                echo '<p class="card-text"><strong>Rating:</strong> ' . tampilkan_rating($row['rating']) . '</p>';  // Menampilkan bintang
                // Mengganti newline dengan <br> saat menampilkan testimoni
                echo '<p class="card-text">' . nl2br(htmlspecialchars($row['testimoni'])) . '</p>';
                echo '<p class="card-text"><small class="text-muted">Dikirim pada ' . date('d F Y', strtotime($row['tanggal'])) . '</small></p>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo '<p>Belum ada testimoni.</p>';
        }
        ?>
    </div>
</body>
</html>
