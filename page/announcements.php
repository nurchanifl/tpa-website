<?php
session_start();
include('../includes/navbar.php');
include('../includes/koneksidb.php');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengumuman</title>
    <link rel="manifest" href="../manifest.json">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <style>
        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <main class="container py-5">
        <h2 class="text-center mb-4">Daftar Pengumuman</h2>
        <div class="row">
            <?php
            $sql = "SELECT * FROM pengumuman ORDER BY tanggal_dibuat DESC";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo '<div class="col-md-6 mb-4">';
                    echo '<div class="card h-100 shadow card-hover">';
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title">' . htmlspecialchars($row['judul']) . '</h5>';
                    echo '<p class="card-text">' . nl2br(htmlspecialchars($row['isi'])) . '</p>';
                    echo '<small class="text-muted"><i class="fas fa-calendar-alt me-1"></i>' . htmlspecialchars($row['tanggal_dibuat']) . '</small>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="col-12"><p class="text-center alert alert-info">Belum ada pengumuman.</p></div>';
            }
            ?>
        </div>
    </main>
    <footer class="mt-5">
        <?php include '../includes/footer.php'; ?>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
