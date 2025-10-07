<?php
// Force local config
$_SERVER['HTTP_HOST'] = 'localhost';
include('config.php');
include('includes/koneksidb.php');

$sql = "CREATE TABLE IF NOT EXISTS user_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

if (mysqli_query($conn, $sql)) {
    echo "Tabel user_tokens berhasil dibuat.";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>
