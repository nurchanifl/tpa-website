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
    <title>Daftar Pesan Kontak</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <main class="container py-5">
        <h2 class="text-center mb-4">Pesan yang Diterima</h2>
        <?php
        $sql = "SELECT * FROM kontak ORDER BY tanggal_kirim DESC";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            echo '<div class="table-responsive">';
            echo '<table class="table table-striped table-hover">';
            echo '<thead class="table-primary">';
            echo '<tr>';
            echo '<th>ID</th>';
            echo '<th>Nama</th>';
            echo '<th>Email</th>';
            echo '<th>Pesan</th>';
            echo '<th>Tanggal Kirim</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['id']) . '</td>';
                echo '<td>' . htmlspecialchars($row['nama']) . '</td>';
                echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                echo '<td>' . htmlspecialchars($row['pesan']) . '</td>';
                echo '<td>' . htmlspecialchars($row['tanggal_kirim']) . '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
            echo '</div>';
        } else {
            echo '<div class="alert alert-info text-center">Belum ada pesan yang diterima.</div>';
        }
        ?>
    </main>
    <footer class="mt-5">
        <?php include '../includes/footer.php'; ?>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
