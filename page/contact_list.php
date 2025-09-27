<?php include('../includes/koneksidb.php');  ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pesan Kontak</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-primary text-white py-3">
        <div class="container">
            <h1 class="text-center">Daftar Pesan Kontak</h1>
        </div>
    </header>
    <main class="container py-5">
        <h2 class="mb-4">Pesan yang Diterima</h2>
        <?php
        $sql = "SELECT * FROM kontak ORDER BY tanggal_kirim DESC";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
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
                echo '<td>' . $row['id'] . '</td>';
                echo '<td>' . $row['nama'] . '</td>';
                echo '<td>' . $row['email'] . '</td>';
                echo '<td>' . $row['pesan'] . '</td>';
                echo '<td>' . $row['tanggal_kirim'] . '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<div class="alert alert-info">Belum ada pesan yang diterima.</div>';
        }
        ?>
    </main>
    <footer class="bg-primary text-white text-center py-3">
        <p>© 2024 TPA - Semua Hak Dilindungi</p>
    </footer>
</body>
</html>
