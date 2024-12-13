<?php include('../includes/koneksidb.php'); 
// Default nilai untuk pagination
$limit = 9;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$kategori = isset($_GET['kategori']) ? $_GET['kategori'] : '';
$sql = "SELECT * FROM galeri";
if ($kategori != '') {
    $sql .= " WHERE kategori = '" . mysqli_real_escape_string($conn, $kategori) . "'";
}
$sql .= " ORDER BY tanggal_upload DESC LIMIT $offset, $limit";
$result = mysqli_query($conn, $sql);

$total_sql = "SELECT COUNT(*) AS total FROM galeri";
if ($kategori != '') {
    $total_sql .= " WHERE kategori = '" . mysqli_real_escape_string($conn, $kategori) . "'";
}
$total_result = mysqli_query($conn, $total_sql);
$total = mysqli_fetch_assoc($total_result)['total'];
$total_pages = ceil($total / $limit);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri Kegiatan</title>
    <link href="assets/lightbox/css/lightbox.min.css" rel="stylesheet">
    <script src="assets/lightbox/js/lightbox.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="bg-primary text-white py-3">
        <div class="container">
            <h1 class="text-center">Galeri Kegiatan</h1>
        </div>
    </header>
    <main class="container py-5">
        <form method="GET">
            <select name="kategori" class="form-select mb-4" onchange="this.form.submit()">
                <option value="">Semua Kategori</option>
                <option value="Kegiatan" <?= $kategori == 'Kegiatan' ? 'selected' : '' ?>>Kegiatan</option>
                <option value="Lomba" <?= $kategori == 'Lomba' ? 'selected' : '' ?>>Lomba</option>
            </select>
        </form>
        <div class="row">
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<div class="col-md-4 mb-4">';
                    echo '<div class="card">';
                    echo '<a href="' . $row['foto'] . '" data-lightbox="galeri" data-title="' . htmlspecialchars($row['judul']) . '">';
                    echo '<img src="' . $row['foto'] . '" class="card-img-top" alt="' . htmlspecialchars($row['judul']) . '">';
                    echo '</a>';
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title">' . htmlspecialchars($row['judul']) . '</h5>';
                    echo '<p class="card-text">' . htmlspecialchars($row['deskripsi']) . '</p>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="col-12"><p class="text-center">Belum ada foto yang diunggah.</p></div>';
            }
            ?>
        </div>
        <nav>
            <ul class="pagination justify-content-center">
                <?php
                for ($i = 1; $i <= $total_pages; $i++) {
                    $active = $i == $page ? 'active' : '';
                    echo '<li class="page-item ' . $active . '"><a class="page-link" href="?page=' . $i . '&kategori=' . urlencode($kategori) . '">' . $i . '</a></li>';
                }
                ?>
            </ul>
        </nav>
    </main>
    <footer class="bg-primary text-white text-center py-3">
        <p>© 2024 TPA - Semua Hak Dilindungi</p>
    </footer>
</body>
</html>
