<?php 
include ('../includes/navbar.php'); 
include('../includes/koneksidb.php');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Kegiatan TPA</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
</head>
<body>
    <header class="bg-primary text-white py-3">
        <div class="container">
            <h1 class="text-center">Pengumuman</h1>
        </div>
    </header>
    <main class="container py-5">
        <h2 class="mb-4">Daftar Pengumuman</h2>
        <?php
        $sql = "SELECT * FROM pengumuman ORDER BY tanggal_dibuat DESC";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<div class="card mb-4">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . $row['judul'] . '</h5>';
                echo '<p class="card-text">' . $row['isi'] . '</p>';
                echo '<p class="text-muted">' . $row['tanggal_dibuat'] . '</p>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo '<p class="text-center">Belum ada pengumuman.</p>';
        }
        ?>
    </main>
    <footer class="mt-5">
        <?php include '../includes/footer.php'; ?>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            easing: 'ease-in-out',
            once: true,
        });
    </script>
</body>
</html>
