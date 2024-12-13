<?php include('../includes/koneksidb.php');  ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Jadwal Kegiatan</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <h1>Manajemen Jadwal Kegiatan</h1>
        <nav>
            <a href="index.php">Beranda</a>
            <a href="add_schedule.php">Tambah Jadwal</a>
            <a href="manage_schedule.php">Kelola Jadwal</a>
        </nav>
    </header>
    <main>
        <section>
            <h2>Daftar Jadwal Kegiatan</h2>
            <?php
            $sql = "SELECT * FROM jadwal_kegiatan ORDER BY tanggal, waktu";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
                echo "<table border='1' cellpadding='10'>";
                echo "<tr><th>Tanggal</th><th>Waktu</th><th>Nama Kegiatan</th><th>Tempat</th><th>Aksi</th></tr>";
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['tanggal'] . "</td>";
                    echo "<td>" . $row['waktu'] . "</td>";
                    echo "<td>" . $row['nama_kegiatan'] . "</td>";
                    echo "<td>" . $row['tempat'] . "</td>";
                    echo "<td>
                            <a href='edit_schedule.php?id=" . $row['id'] . "'>Edit</a> |
                            <a href='delete_schedule.php?id=" . $row['id'] . "' onclick='return confirm(\"Yakin ingin menghapus jadwal ini?\")'>Hapus</a>
                          </td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p>Belum ada jadwal kegiatan.</p>";
            }
            ?>
        </section>
    </main>
    <footer>
        <p>© 2024 TPA - Semua Hak Dilindungi</p>
    </footer>
</body>
</html>
