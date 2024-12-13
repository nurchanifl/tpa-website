<?php
session_start();
include('../includes/koneksidb.php'); 

// Pastikan hanya admin yang dapat mengakses halaman ini
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Proses hapus testimoni
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $sql_hapus = "DELETE FROM rating_testimoni WHERE id = $id";
    if (mysqli_query($conn, $sql_hapus)) {
        echo "Testimoni berhasil dihapus.";
    } else {
        echo "Gagal menghapus testimoni: " . mysqli_error($conn);
    }
}

// Ambil data testimoni
$sql = "SELECT * FROM rating_testimoni ORDER BY tanggal DESC";
$result = mysqli_query($conn, $sql);

$sql = "SELECT rt.id, rt.rating, rt.testimoni, rt.tanggal, u.nama AS nama_pengguna
        FROM rating_testimoni rt
        JOIN users u ON rt.pengguna_id = u.id
        ORDER BY rt.tanggal DESC";
echo '<td>' . htmlspecialchars($row['nama_pengguna']) . '</td>';


?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Testimoni</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Kelola Testimoni</h1>
        <?php
        if (mysqli_num_rows($result) > 0) {
            echo '<table class="table">';
            echo '<thead><tr><th>Nama</th><th>Rating</th><th>Testimoni</th><th>Aksi</th></tr></thead><tbody>';
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['nama_pengguna']) . '</td>';
                echo '<td>' . $row['rating'] . '/5</td>';
                echo '<td>' . htmlspecialchars($row['testimoni']) . '</td>';
                echo '<td><a href="?hapus=' . $row['id'] . '" class="btn btn-danger btn-sm">Hapus</a></td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        } else {
            echo '<p>Belum ada testimoni.</p>';
        }
        ?>
    </div>
</body>
</html>
