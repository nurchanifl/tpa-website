<?php
session_start();
include('../includes/koneksidb.php');  // Memastikan koneksi sudah ada
include('../includes/navbar.php'); 

// Periksa jika koneksi berhasil
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Jika pengguna belum login, arahkan ke halaman login
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

// Proses penyimpanan rating dan testimoni
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pengguna_id = $_SESSION['id'];
    $nama_pengguna = isset($_SESSION['email']) ? $_SESSION['email'] : 'Anonym';
    $rating = intval($_POST['rating']);
    $testimoni = mysqli_real_escape_string($conn, $_POST['testimoni']);

    // Validasi rating
    if ($rating < 1 || $rating > 5) {
        echo "Rating harus antara 1 dan 5.";
    } else {
        // Menggunakan prepared statement untuk mencegah SQL injection
        $sql = $conn->prepare("INSERT INTO rating_testimoni (pengguna_id, nama_pengguna, rating, testimoni) 
                               VALUES (?, ?, ?, ?)");
        $sql->bind_param("isis", $pengguna_id, $nama_pengguna, $rating, $testimoni);

        if ($sql->execute()) {
            echo "Terima kasih atas ulasan Anda!";
        } else {
            echo "Error: " . $sql->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berikan Testimoni</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Berikan Rating dan Testimoni</h1>
        <form method="POST" action="add_testimoni.php">
            <div class="mb-3">
                <label for="rating" class="form-label">Rating (1-5)</label>
                <select id="rating" name="rating" class="form-select" required>
                    <option value="1">1 - Sangat Buruk</option>
                    <option value="2">2 - Buruk</option>
                    <option value="3">3 - Cukup</option>
                    <option value="4">4 - Baik</option>
                    <option value="5">5 - Sangat Baik</option>
                </select>
            </div>
            
            <!-- Hapus input email karena kita sudah menggunakan email dari session -->
            <div class="mb-3">
                <label for="testimoni" class="form-label">Testimoni</label>
                <textarea class="form-control" id="testimoni" name="testimoni" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Kirim Testimoni</button>
        </form>
    </div>
</body>
</html>
