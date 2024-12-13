<?php
include('../includes/koneksidb.php'); 

// Proses penghapusan santri
if (isset($_GET['id'])) {
    $santri_id = $_GET['id'];

    // Hapus data santri berdasarkan ID
    $sql_delete = "DELETE FROM santri WHERE id = '$santri_id'";
    if (mysqli_query($conn, $sql_delete)) {
        echo "Santri berhasil dihapus!";
    } else {
        echo "Gagal menghapus santri: " . mysqli_error($conn);
    }
}

echo '<br><a href="daftar_santri.php" class="btn btn-secondary">Kembali ke Daftar Santri</a>';
?>
