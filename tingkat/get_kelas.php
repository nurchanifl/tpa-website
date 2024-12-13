<?php
include('../includes/koneksidb.php');  // Pastikan file koneksi sudah benar

if (isset($_GET['id_unit'])) {
    $id_unit = mysqli_real_escape_string($conn, $_GET['id_unit']);

    // Query untuk mendapatkan kelas berdasarkan unit
    $sql = "SELECT id, nama_kelas FROM kelas WHERE id_unit = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $id_unit);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Output data kelas dalam format <option>
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['nama_kelas']) . '</option>';
        }
    } else {
        echo '<option value="">Kelas tidak tersedia</option>';
    }

    mysqli_stmt_close($stmt);
}
?>