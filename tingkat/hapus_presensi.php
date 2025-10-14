<?php
session_start();
include('../includes/koneksidb.php');
include('../includes/navbar.php');

// Cek apakah user sudah login dan memiliki akses
if (!isset($_SESSION['id'])) {
    header('Location: ../login.php');
    exit();
}

// Ambil daftar unit
$sql_unit = "SELECT * FROM unit";
$result_unit = mysqli_query($conn, $sql_unit);

$kelas_data = [];
$santri_data = [];
$presensi_data = [];

// Handle form submission untuk memilih kelas/santri
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['hapus_presensi'])) {
    $id_unit = $_POST['id_unit'] ?? '';
    $id_kelas = $_POST['id_kelas'] ?? '';
    $mode = $_POST['mode'] ?? 'per_kelas';
    $tanggal = $_POST['tanggal'] ?? '';

    // Ambil data kelas berdasarkan unit
    if (!empty($id_unit)) {
        $sql_kelas = "SELECT * FROM kelas WHERE id_unit = ?";
        $stmt_kelas = mysqli_prepare($conn, $sql_kelas);
        mysqli_stmt_bind_param($stmt_kelas, 'i', $id_unit);
        mysqli_stmt_execute($stmt_kelas);
        $result_kelas = mysqli_stmt_get_result($stmt_kelas);
        $kelas_data = mysqli_fetch_all($result_kelas, MYSQLI_ASSOC);
        mysqli_stmt_close($stmt_kelas);
    }

    // Ambil data santri berdasarkan kelas
    if (!empty($id_kelas)) {
        $sql_santri = "SELECT * FROM santri WHERE id_kelas = ?";
        $stmt_santri = mysqli_prepare($conn, $sql_santri);
        mysqli_stmt_bind_param($stmt_santri, 'i', $id_kelas);
        mysqli_stmt_execute($stmt_santri);
        $result_santri = mysqli_stmt_get_result($stmt_santri);
        $santri_data = mysqli_fetch_all($result_santri, MYSQLI_ASSOC);
        mysqli_stmt_close($stmt_santri);

        // Jika mode per kelas dan tanggal dipilih, tampilkan presensi yang ada
        if ($mode == 'per_kelas' && !empty($tanggal)) {
            $sql_presensi = "SELECT p.*, s.nama_santri 
                           FROM presensi p
                           JOIN santri s ON p.id_santri = s.id
                           WHERE s.id_kelas = ? AND p.tanggal = ?
                           ORDER BY s.nama_santri";
            $stmt_presensi = mysqli_prepare($conn, $sql_presensi);
            mysqli_stmt_bind_param($stmt_presensi, 'is', $id_kelas, $tanggal);
            mysqli_stmt_execute($stmt_presensi);
            $result_presensi = mysqli_stmt_get_result($stmt_presensi);
            $presensi_data = mysqli_fetch_all($result_presensi, MYSQLI_ASSOC);
            mysqli_stmt_close($stmt_presensi);
        }
    }
}

// Handle penghapusan presensi
if (isset($_POST['hapus_presensi'])) {
    $mode = $_POST['mode'] ?? 'per_kelas';
    $id_kelas = $_POST['id_kelas'] ?? '';
    $id_santri = $_POST['id_santri'] ?? '';
    $tanggal = $_POST['tanggal'] ?? '';

    if ($mode == 'per_kelas' && !empty($id_kelas) && !empty($tanggal)) {
        // Hapus semua presensi untuk kelas pada tanggal tertentu
        $sql_delete = "DELETE p FROM presensi p
                      JOIN santri s ON p.id_santri = s.id
                      WHERE s.id_kelas = ? AND p.tanggal = ?";
        $stmt_delete = mysqli_prepare($conn, $sql_delete);
        mysqli_stmt_bind_param($stmt_delete, 'is', $id_kelas, $tanggal);
        
        if (mysqli_stmt_execute($stmt_delete)) {
            $affected_rows = mysqli_stmt_affected_rows($stmt_delete);
            $_SESSION['success_message'] = "Berhasil menghapus $affected_rows data presensi untuk kelas pada tanggal " . date('d-m-Y', strtotime($tanggal));
        } else {
            $_SESSION['error_message'] = "Gagal menghapus presensi: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt_delete);
        
    } elseif ($mode == 'per_santri' && !empty($id_santri) && !empty($tanggal)) {
        // Hapus presensi untuk santri tertentu pada tanggal tertentu
        $sql_delete = "DELETE FROM presensi WHERE id_santri = ? AND tanggal = ?";
        $stmt_delete = mysqli_prepare($conn, $sql_delete);
        mysqli_stmt_bind_param($stmt_delete, 'is', $id_santri, $tanggal);
        
        if (mysqli_stmt_execute($stmt_delete)) {
            $affected_rows = mysqli_stmt_affected_rows($stmt_delete);
            if ($affected_rows > 0) {
                $_SESSION['success_message'] = "Berhasil menghapus presensi santri pada tanggal " . date('d-m-Y', strtotime($tanggal));
            } else {
                $_SESSION['error_message'] = "Tidak ada data presensi yang dihapus. Mungkin data tidak ditemukan.";
            }
        } else {
            $_SESSION['error_message'] = "Gagal menghapus presensi: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt_delete);
    }
    
    header('Location: hapus_presensi.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hapus Presensi Santri</title>
    <link rel="manifest" href="../manifest.json">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid mt-5">
    <h2 class="text-center mb-4">Hapus Presensi Santri</h2>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success_message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error_message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <!-- Form Pilih Mode -->
    <form method="POST" class="mb-4">
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label fw-bold">Mode Penghapusan</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="mode" id="mode_kelas" value="per_kelas" 
                           <?= ($_POST['mode'] ?? 'per_kelas') == 'per_kelas' ? 'checked' : '' ?> onchange="this.form.submit()">
                    <label class="form-check-label" for="mode_kelas">Hapus per Kelas (Semua Santri)</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="mode" id="mode_santri" value="per_santri" 
                           <?= ($_POST['mode'] ?? '') == 'per_santri' ? 'checked' : '' ?> onchange="this.form.submit()">
                    <label class="form-check-label" for="mode_santri">Hapus per Santri</label>
                </div>
            </div>

            <div class="col-12 col-md-3">
                <label for="tanggal" class="form-label">Tanggal</label>
                <input type="date" name="tanggal" id="tanggal" class="form-control" 
                       value="<?= htmlspecialchars($_POST['tanggal'] ?? '') ?>" required>
            </div>

            <div class="col-12 col-md-3">
                <label for="unit" class="form-label">Pilih Unit</label>
                <select name="id_unit" id="unit" class="form-select" required>
                    <option value="">-- Pilih Unit --</option>
                    <?php 
                    mysqli_data_seek($result_unit, 0);
                    while ($unit = mysqli_fetch_assoc($result_unit)) { ?>
                        <option value="<?= $unit['id'] ?>" <?= ($_POST['id_unit'] ?? '') == $unit['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($unit['nama_unit']) ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-12 col-md-3">
                <label for="kelas" class="form-label">Pilih Kelas</label>
                <select name="id_kelas" id="kelas" class="form-select" onchange="this.form.submit()" required>
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach ($kelas_data as $kelas) { ?>
                        <option value="<?= $kelas['id'] ?>" <?= ($_POST['id_kelas'] ?? '') == $kelas['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($kelas['nama_kelas']) ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <?php if (($_POST['mode'] ?? 'per_kelas') == 'per_santri') { ?>
            <div class="col-12 col-md-3">
                <label for="santri" class="form-label">Pilih Santri</label>
                <select name="id_santri" id="santri" class="form-select" required>
                    <option value="">-- Pilih Santri --</option>
                    <?php foreach ($santri_data as $santri) { ?>
                        <option value="<?= $santri['id'] ?>" <?= ($_POST['id_santri'] ?? '') == $santri['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($santri['nama_santri']) ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <?php } ?>
        </div>
    </form>

    <!-- Tampilkan Data Presensi yang Akan Dihapus (untuk mode per kelas) -->
    <?php if (!empty($presensi_data) && ($_POST['mode'] ?? 'per_kelas') == 'per_kelas') { ?>
        <div class="card mb-4">
            <div class="card-header bg-warning">
                <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Data Presensi yang Akan Dihapus</h5>
            </div>
            <div class="card-body">
                <p><strong>Tanggal:</strong> <?= date('d-m-Y', strtotime($_POST['tanggal'])) ?></p>
                <p><strong>Jumlah Data:</strong> <?= count($presensi_data) ?> santri</p>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Santri</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($presensi_data as $presensi) { ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($presensi['nama_santri']) ?></td>
                                <td><?= htmlspecialchars($presensi['status']) ?></td>
                                <td><?= htmlspecialchars($presensi['keterangan'] ?? '-') ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <form method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus semua presensi kelas ini pada tanggal <?= date('d-m-Y', strtotime($_POST['tanggal'])) ?>? Tindakan ini tidak dapat dibatalkan!');">
                    <input type="hidden" name="mode" value="per_kelas">
                    <input type="hidden" name="id_kelas" value="<?= htmlspecialchars($_POST['id_kelas']) ?>">
                    <input type="hidden" name="tanggal" value="<?= htmlspecialchars($_POST['tanggal']) ?>">
                    <button type="submit" name="hapus_presensi" class="btn btn-danger w-100">
                        <i class="fas fa-trash"></i> Hapus Semua Presensi Kelas
                    </button>
                </form>
            </div>
        </div>
    <?php } ?>

    <!-- Form Hapus untuk mode per santri -->
    <?php if (($_POST['mode'] ?? 'per_kelas') == 'per_santri' && !empty($_POST['id_santri']) && !empty($_POST['tanggal'])) { ?>
        <div class="card mb-4">
            <div class="card-header bg-warning">
                <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Konfirmasi Penghapusan</h5>
            </div>
            <div class="card-body">
                <?php
                // Cek apakah ada data presensi untuk santri ini
                $sql_check = "SELECT p.*, s.nama_santri FROM presensi p
                             JOIN santri s ON p.id_santri = s.id
                             WHERE p.id_santri = ? AND p.tanggal = ?";
                $stmt_check = mysqli_prepare($conn, $sql_check);
                mysqli_stmt_bind_param($stmt_check, 'is', $_POST['id_santri'], $_POST['tanggal']);
                mysqli_stmt_execute($stmt_check);
                $result_check = mysqli_stmt_get_result($stmt_check);
                $data_presensi = mysqli_fetch_assoc($result_check);
                mysqli_stmt_close($stmt_check);

                if ($data_presensi) {
                ?>
                    <p><strong>Santri:</strong> <?= htmlspecialchars($data_presensi['nama_santri']) ?></p>
                    <p><strong>Tanggal:</strong> <?= date('d-m-Y', strtotime($_POST['tanggal'])) ?></p>
                    <p><strong>Status:</strong> <?= htmlspecialchars($data_presensi['status']) ?></p>
                    <p><strong>Keterangan:</strong> <?= htmlspecialchars($data_presensi['keterangan'] ?? '-') ?></p>

                    <form method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus presensi santri ini? Tindakan ini tidak dapat dibatalkan!');">
                        <input type="hidden" name="mode" value="per_santri">
                        <input type="hidden" name="id_santri" value="<?= htmlspecialchars($_POST['id_santri']) ?>">
                        <input type="hidden" name="tanggal" value="<?= htmlspecialchars($_POST['tanggal']) ?>">
                        <button type="submit" name="hapus_presensi" class="btn btn-danger w-100">
                            <i class="fas fa-trash"></i> Hapus Presensi Santri
                        </button>
                    </form>
                <?php } else { ?>
                    <div class="alert alert-info">
                        Tidak ada data presensi untuk santri ini pada tanggal <?= date('d-m-Y', strtotime($_POST['tanggal'])) ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>

    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> <strong>Informasi:</strong>
        <ul class="mb-0 mt-2">
            <li><strong>Hapus per Kelas:</strong> Menghapus semua data presensi untuk seluruh santri dalam kelas pada tanggal tertentu</li>
            <li><strong>Hapus per Santri:</strong> Menghapus data presensi untuk satu santri tertentu pada tanggal tertentu</li>
            <li>Data yang sudah dihapus tidak dapat dikembalikan</li>
        </ul>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const unitSelect = document.getElementById('unit');
        const kelasSelect = document.getElementById('kelas');

        unitSelect.addEventListener('change', function() {
            const id_unit = this.value;
            kelasSelect.innerHTML = '<option value="">-- Pilih Kelas --</option>';
            if (id_unit) {
                fetch(`get_kelas.php?id_unit=${id_unit}`)
                .then(response => response.text())
                .then(data => {
                    kelasSelect.innerHTML += data;
                })
                .catch(error => console.error('Error:', error));
            }
        });
    });

    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('../sw.js')
            .then(function(registration) {
                console.log('Service Worker registered successfully:', registration);
            })
            .catch(function(error) {
                console.log('Service Worker registration failed:', error);
            });
    }
</script>
</body>
</html>
