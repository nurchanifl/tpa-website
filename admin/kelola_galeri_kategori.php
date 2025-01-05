<?php
include('../includes/koneksidb.php');
include('../includes/navbar.php'); 

// Tambah Kategori
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_kategori'])) {
    $nama_kategori = mysqli_real_escape_string($conn, $_POST['nama_kategori']);
    if (!empty($nama_kategori)) {
        $sql = "INSERT INTO galeri_kategori (nama) VALUES (?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 's', $nama_kategori);
        if (mysqli_stmt_execute($stmt)) {
            echo '<div class="alert alert-success">Kategori berhasil ditambahkan!</div>';
        } else {
            echo '<div class="alert alert-danger">Gagal menambahkan kategori!</div>';
        }
        mysqli_stmt_close($stmt);
    }
}

// Edit Kategori
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_kategori'])) {
    $id_kategori = intval($_POST['id_kategori']);
    $nama_kategori = mysqli_real_escape_string($conn, $_POST['nama_kategori']);
    if (!empty($nama_kategori)) {
        $sql = "UPDATE galeri_kategori SET nama = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'si', $nama_kategori, $id_kategori);
        if (mysqli_stmt_execute($stmt)) {
            echo '<div class="alert alert-success">Kategori berhasil diperbarui!</div>';
        } else {
            echo '<div class="alert alert-danger">Gagal memperbarui kategori!</div>';
        }
        mysqli_stmt_close($stmt);
    }
}

// Hapus Kategori
if (isset($_GET['hapus_id'])) {
    $hapus_id = intval($_GET['hapus_id']);
    $sql = "DELETE FROM galeri_kategori WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $hapus_id);
    if (mysqli_stmt_execute($stmt)) {
        echo '<div class="alert alert-success">Kategori berhasil dihapus!</div>';
    } else {
        echo '<div class="alert alert-danger">Gagal menghapus kategori!</div>';
    }
    mysqli_stmt_close($stmt);
}

// Ambil semua kategori
$sql = "SELECT * FROM galeri_kategori ORDER BY id ASC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kategori Galeri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <h1 class="text-center">Kelola Kategori Galeri</h1>
    <div class="mb-4">
        <h2>Tambah Kategori</h2>
        <form method="POST" class="d-flex gap-2">
            <input type="text" name="nama_kategori" class="form-control" placeholder="Nama Kategori" required>
            <button type="submit" name="tambah_kategori" class="btn btn-primary">Tambah</button>
        </form>
    </div>
    <div>
        <h2>Daftar Kategori</h2>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Aksi</th>
            </tr>
            </thead>
            <tbody>
            <?php 
            $no = 1; // Inisialisasi nomor urut
            while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= $no++ ?></td> <!-- Tampilkan nomor urut -->
                    <td>
                        <form method="POST" class="d-flex gap-2">
                            <input type="hidden" name="id_kategori" value="<?= htmlspecialchars($row['id']) ?>">
                            <input type="text" name="nama_kategori" class="form-control" value="<?= htmlspecialchars($row['nama']) ?>" required>
                            <button type="submit" name="edit_kategori" class="btn btn-warning btn-sm">Simpan</button>
                        </form>
                    </td>
                    <td>
                        <a href="?hapus_id=<?= htmlspecialchars($row['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>