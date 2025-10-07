<?php
session_start();
include('../includes/koneksidb.php');
include('../includes/navbar.php');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kegiatan</title>
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
        <section>
            <h2 class="text-center mb-4">Daftar Kegiatan</h2>
            <div class="row g-4">
                <?php
                $sql = "SELECT * FROM kegiatan";
                $result = mysqli_query($conn, $sql);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 shadow card-hover">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($row['nama_kegiatan']) ?></h5>
                                    <p class="card-text"><?= nl2br(htmlspecialchars($row['deskripsi'])) ?></p>
                                    <small class="text-muted">Tanggal: <?= htmlspecialchars($row['tanggal']) ?></small>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo "<p class='text-center'>Belum ada kegiatan.</p>";
                }
                ?>
            </div>
        </section>
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
