<?php
session_start();
include('../includes/koneksidb.php');
include('../includes/navbar.php');

// Ambil daftar unit
$sql_unit = "SELECT * FROM unit";
$result_unit = mysqli_query($conn, $sql_unit);

$kelas_data = [];
$santri_data = [];
$presensi_data = [];
$bulan_filter = date('m'); // Default bulan
$tahun_filter = date('Y'); // Default tahun

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['simpan_presensi'])) {
    $id_unit = $_POST['id_unit'] ?? '';
    $id_kelas = $_POST['id_kelas'] ?? '';
    $tanggal = $_POST['tanggal'] ?? date('Y-m-d');

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
    }
}

// Menampilkan laporan presensi berdasarkan kelas, bulan dan tahun
if (isset($_POST['tampilkan_presensi'])) {
    $id_unit = $_POST['id_unit'] ?? '';
    $id_kelas = $_POST['id_kelas'] ?? '';
    $bulan_filter = $_POST['bulan_filter'] ?? date('m');
    $tahun_filter = $_POST['tahun_filter'] ?? date('Y');

    // Query untuk menampilkan presensi berdasarkan kelas dan bulan/tahun
    if (!empty($id_unit) && !empty($id_kelas)) {
        // Ambil semua tanggal dalam bulan yang dipilih
        $first_day_of_month = "$tahun_filter-$bulan_filter-01";
        $last_day_of_month = date("Y-m-t", strtotime($first_day_of_month)); // Tanggal terakhir bulan ini
        
        // Ambil presensi per santri untuk bulan tersebut
        $sql_presensi = "SELECT p.*, s.nama_santri, DAY(p.tanggal) as day
                         FROM presensi p
                         JOIN santri s ON p.id_santri = s.id
                         WHERE s.id_kelas = ? AND p.tanggal BETWEEN ? AND ? 
                         ORDER BY s.id, p.tanggal";
                         
        $stmt_presensi = mysqli_prepare($conn, $sql_presensi);
        mysqli_stmt_bind_param($stmt_presensi, 'iss', $id_kelas, $first_day_of_month, $last_day_of_month);
        mysqli_stmt_execute($stmt_presensi);
        $result_presensi = mysqli_stmt_get_result($stmt_presensi);

        // Grouping data per santri
        $presensi_grouped = [];
        while ($row = mysqli_fetch_assoc($result_presensi)) {
            $presensi_grouped[$row['id_santri']]['nama_santri'] = $row['nama_santri'];
            $presensi_grouped[$row['id_santri']]['presensi'][$row['day']] = $row['status'];
        }

        // Menyiapkan total kehadiran per status
        $presensi_data = [];
        foreach ($presensi_grouped as $santri_id => $santri_presensi) {
            $presensi_data[$santri_id] = $santri_presensi;
            $presensi_data[$santri_id]['total_hadir'] = 0;
            $presensi_data[$santri_id]['total_izin'] = 0;
            $presensi_data[$santri_id]['total_sakit'] = 0;
            $presensi_data[$santri_id]['total_alpha'] = 0;

            // Menghitung total untuk setiap status
            for ($day = 1; $day <= date('t', strtotime($first_day_of_month)); $day++) {
                $status = $santri_presensi['presensi'][$day] ?? null; // Jangan langsung default ke 'Alpha'
                if ($status === 'Hadir') {
                    $presensi_data[$santri_id]['total_hadir']++;
                } elseif ($status === 'Izin') {
                    $presensi_data[$santri_id]['total_izin']++;
                } elseif ($status === 'Sakit') {
                    $presensi_data[$santri_id]['total_sakit']++;
                } elseif ($status === 'Alpha') { // Hanya hitung jika memang Alpha
                    $presensi_data[$santri_id]['total_alpha']++;
                }
            }
            
        }
        mysqli_stmt_close($stmt_presensi);

        // Ambil nama unit dan kelas
        $unit_nama = '';
        $kelas_nama = '';

        $sql_unit_nama = "SELECT nama_unit FROM unit WHERE id = ?";
        $stmt_unit_nama = mysqli_prepare($conn, $sql_unit_nama);
        mysqli_stmt_bind_param($stmt_unit_nama, 'i', $id_unit);
        mysqli_stmt_execute($stmt_unit_nama);
        mysqli_stmt_bind_result($stmt_unit_nama, $unit_nama);
        mysqli_stmt_fetch($stmt_unit_nama);
        mysqli_stmt_close($stmt_unit_nama);

        $sql_kelas_nama = "SELECT nama_kelas FROM kelas WHERE id = ?";
        $stmt_kelas_nama = mysqli_prepare($conn, $sql_kelas_nama);
        mysqli_stmt_bind_param($stmt_kelas_nama, 'i', $id_kelas);
        mysqli_stmt_execute($stmt_kelas_nama);
        mysqli_stmt_bind_result($stmt_kelas_nama, $kelas_nama);
        mysqli_stmt_fetch($stmt_kelas_nama);
        mysqli_stmt_close($stmt_kelas_nama);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tampilkan Presensi Santri</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        table th, table td {
            text-align: center; /* Teks dalam tabel center */
            vertical-align: middle;
        }
    </style>
</head>
<body>
<div class="container mt-5 text-center">
    <!-- Form Pilih Unit, Kelas, Bulan, Tahun -->
    <form method="POST" class="mb-4">
        <div class="row justify-content-center">
            <div class="col-md-3">
                <label for="unit" class="form-label">Pilih Unit</label>
                <select name="id_unit" id="unit" class="form-select" onchange="this.form.submit()" required>
                    <option value="">-- Pilih Unit --</option>
                    <?php while ($unit = mysqli_fetch_assoc($result_unit)) { ?>
                        <option value="<?= $unit['id'] ?>" <?= ($_POST['id_unit'] ?? '') == $unit['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($unit['nama_unit']) ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-3">
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
            <div class="col-md-3">
                <label for="bulan" class="form-label">Pilih Bulan</label>
                <select name="bulan_filter" id="bulan" class="form-select" required>
                    <?php for ($i = 1; $i <= 12; $i++) { ?>
                        <option value="<?= $i ?>" <?= ($bulan_filter == $i) ? 'selected' : '' ?>>
                            <?= DateTime::createFromFormat('!m', $i)->format('F') ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="tahun" class="form-label">Pilih Tahun</label>
                <input type="number" name="tahun_filter" id="tahun" class="form-control" value="<?= htmlspecialchars($tahun_filter) ?>" required>
            </div>
        </div>
        <button type="submit" name="tampilkan_presensi" class="btn btn-primary mt-3">Tampilkan Presensi</button>
    </form>

    <!-- Tampilkan laporan presensi di bawah tombol -->
    <?php if (isset($_POST['tampilkan_presensi'])) { ?>
        <h2 class="mt-4">LAPORAN PRESENSI</h2>
        <h4><?= DateTime::createFromFormat('!m', $bulan_filter)->format('F') . " " . $tahun_filter ?></h4> <!-- Nama Bulan dan Tahun -->
        <p><strong>Unit:</strong> <?= htmlspecialchars($unit_nama) ?></p>
        <p><strong>Kelas:</strong> <?= htmlspecialchars($kelas_nama) ?></p>
    <?php } ?>

    <!-- Tabel Presensi -->
    <?php if (!empty($presensi_data)) { ?>
        <table class="table table-bordered table-striped mx-auto" style="max-width: 1000px;">
            <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Nama Santri</th>
                <th colspan="<?= date('t', strtotime($first_day_of_month)) ?>">Tanggal</th>
                <th rowspan="2">Total Hadir</th>
                <th rowspan="2">Total Izin</th>
                <th rowspan="2">Total Sakit</th>
                <th rowspan="2">Total Alpha</th>
            </tr>
            <tr>
                <?php for ($day = 1; $day <= date('t', strtotime($first_day_of_month)); $day++) { ?>
                    <th><?= $day ?></th>
                <?php } ?>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1; foreach ($presensi_data as $santri) { ?>
    <tr>
        <td><?= $no++ ?></td>
        <td><?= htmlspecialchars($santri['nama_santri']) ?></td>
        <?php for ($day = 1; $day <= date('t', strtotime($first_day_of_month)); $day++) { 
            $status = $santri['presensi'][$day] ?? '&nbsp;'; // Default kosong
        ?>
            <td><?= $status ?></td>
        <?php } ?>
        <td><?= $santri['total_hadir'] ?></td>
        <td><?= $santri['total_izin'] ?></td>
        <td><?= $santri['total_sakit'] ?></td>
        <td><?= $santri['total_alpha'] ?></td>
    </tr>
<?php } ?>
            </tbody>
        </table>
                <!-- Form untuk unduh PDF -->
<form method="POST" action="download_presensi_pdf.php" target="_blank">
    <input type="hidden" name="id_unit" value="<?= htmlspecialchars($id_unit) ?>">
    <input type="hidden" name="id_kelas" value="<?= htmlspecialchars($id_kelas) ?>">
    <input type="hidden" name="bulan_filter" value="<?= htmlspecialchars($bulan_filter) ?>">
    <input type="hidden" name="tahun_filter" value="<?= htmlspecialchars($tahun_filter) ?>">
    <button type="submit" class="btn btn-info">Unduh ke PDF</button>
</form>

    <?php } ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>