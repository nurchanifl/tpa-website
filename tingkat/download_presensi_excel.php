<?php
require('../includes/koneksidb.php');

// Mulai sesi untuk mendapatkan nama pengguna
session_start();
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Unknown User';

if (!empty($_REQUEST)) {
    $mode = $_REQUEST['mode'] ?? 'per_kelas';
    $id_unit = $_REQUEST['id_unit'] ?? '';
    $id_kelas = $_REQUEST['id_kelas'] ?? '';
    $filter_type = $_REQUEST['filter_type'] ?? 'bulan';
    $bulan_filter = $_REQUEST['bulan_filter'] ?? date('m');
    $tahun_filter = $_REQUEST['tahun_filter'] ?? date('Y');
    $tanggal_dari = $_REQUEST['tanggal_dari'] ?? '';
    $tanggal_sampai = $_REQUEST['tanggal_sampai'] ?? '';

    // Validasi input
    if (empty($id_unit) || empty($id_kelas)) {
        die('Unit dan Kelas harus dipilih.');
    }

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

    // Tentukan range tanggal berdasarkan tipe filter
    if ($filter_type == 'bulan') {
        $first_day_of_month = "$tahun_filter-$bulan_filter-01";
        $last_day_of_month = date("Y-m-t", strtotime($first_day_of_month));
        $periode_text = DateTime::createFromFormat('!m', $bulan_filter)->format('F') . " $tahun_filter";
    } else {
        $first_day_of_month = $tanggal_dari;
        $last_day_of_month = $tanggal_sampai;
        $periode_text = date('d-m-Y', strtotime($tanggal_dari)) . " s/d " . date('d-m-Y', strtotime($tanggal_sampai));
    }

    // Hitung jumlah hari dalam range
    $start_date = new DateTime($first_day_of_month);
    $end_date = new DateTime($last_day_of_month);
    $interval = $start_date->diff($end_date);
    $total_days = $interval->days + 1;

    if ($mode == 'per_kelas') {
        $sql_presensi = "SELECT p.*, s.nama_santri, p.tanggal
                         FROM presensi p
                         JOIN santri s ON p.id_santri = s.id
                         WHERE s.id_kelas = ? AND p.tanggal BETWEEN ? AND ?
                         ORDER BY s.id, p.tanggal";

        $stmt_presensi = mysqli_prepare($conn, $sql_presensi);
        mysqli_stmt_bind_param($stmt_presensi, 'iss', $id_kelas, $first_day_of_month, $last_day_of_month);
        mysqli_stmt_execute($stmt_presensi);
        $result_presensi = mysqli_stmt_get_result($stmt_presensi);

        $presensi_grouped = [];
        while ($row = mysqli_fetch_assoc($result_presensi)) {
            $presensi_grouped[$row['id_santri']]['nama_santri'] = $row['nama_santri'];
            $presensi_grouped[$row['id_santri']]['presensi'][$row['tanggal']] = $row['status'];
        }
        mysqli_stmt_close($stmt_presensi);
    } elseif ($mode == 'per_santri') {
        $id_santri = $_REQUEST['id_santri'] ?? '';
        if (empty($id_santri)) {
            die('Santri harus dipilih.');
        }

        $sql_presensi = "SELECT p.*, s.nama_santri, DATE_FORMAT(p.tanggal, '%d-%m-%Y') as tanggal_formatted, p.keterangan
                         FROM presensi p
                         JOIN santri s ON p.id_santri = s.id
                         WHERE p.id_santri = ? AND p.tanggal BETWEEN ? AND ?
                         ORDER BY p.tanggal";

        $stmt_presensi = mysqli_prepare($conn, $sql_presensi);
        mysqli_stmt_bind_param($stmt_presensi, 'iss', $id_santri, $first_day_of_month, $last_day_of_month);
        mysqli_stmt_execute($stmt_presensi);
        $result_presensi = mysqli_stmt_get_result($stmt_presensi);

        $presensi_data = [];
        while ($row = mysqli_fetch_assoc($result_presensi)) {
            $presensi_data[] = $row;
        }
        mysqli_stmt_close($stmt_presensi);

        // Ambil nama santri
        $santri_nama = '';
        $sql_santri_nama = "SELECT nama_santri FROM santri WHERE id = ?";
        $stmt_santri_nama = mysqli_prepare($conn, $sql_santri_nama);
        mysqli_stmt_bind_param($stmt_santri_nama, 'i', $id_santri);
        mysqli_stmt_execute($stmt_santri_nama);
        mysqli_stmt_bind_result($stmt_santri_nama, $santri_nama);
        mysqli_stmt_fetch($stmt_santri_nama);
        mysqli_stmt_close($stmt_santri_nama);
    }

    // Buat Excel
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="Laporan_Presensi_' . $unit_nama . '_' . $kelas_nama . '_' . str_replace(' ', '_', $periode_text) . '.xls"');
    header('Cache-Control: max-age=0');

    echo "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel' xmlns='http://www.w3.org/TR/REC-html40'>";
    echo "<head>";
    echo "<meta charset='UTF-8'>";
    echo "<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Laporan Presensi</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->";
    echo "<style>";
    echo "table { border-collapse: collapse; width: 100%; table-layout: fixed; }";
    echo "th, td { border: 1px solid #000; padding: 8px; text-align: center; vertical-align: middle; font-size: 12px; }";
    echo "th { background-color: #f0f0f0; font-weight: bold; position: sticky; left: 0; z-index: 1; }";
    echo "td:first-child, th:first-child { position: sticky; left: 0; background-color: #fff; z-index: 2; }";
    echo "td:nth-child(2), th:nth-child(2) { position: sticky; left: 50px; background-color: #fff; z-index: 2; }";
    echo "</style>";
    echo "</head>";
    echo "<body>";

    // Judul Laporan
    echo "<table style='border: none; margin-bottom: 20px;'>";
    echo "<tr><td colspan='2' style='border: none; text-align: center; font-size: 16px; font-weight: bold;'>LAPORAN PRESENSI SANTRI</td></tr>";
    echo "<tr><td style='border: none; width: 150px;'><strong>Unit:</strong></td><td style='border: none;'>$unit_nama</td></tr>";
    echo "<tr><td style='border: none;'><strong>Kelas:</strong></td><td style='border: none;'>$kelas_nama</td></tr>";
    if ($mode == 'per_santri') {
        echo "<tr><td style='border: none;'><strong>Santri:</strong></td><td style='border: none;'>$santri_nama</td></tr>";
    }
    echo "<tr><td style='border: none;'><strong>Periode:</strong></td><td style='border: none;'>$periode_text</td></tr>";
    echo "<tr><td style='border: none;'><strong>Dibuat oleh:</strong></td><td style='border: none;'>$username</td></tr>";
    echo "</table>";
    echo "<br>";

    if ($mode == 'per_kelas') {
        echo "<table>";
        echo "<thead>";
        echo "<tr>";
        echo "<th style='width: 50px;'>No</th>";
        echo "<th style='width: 200px; text-align: left;'>Nama Santri</th>";

        // Header Tanggal
        $current_date = clone $start_date;
        while ($current_date <= $end_date) {
            echo "<th style='width: 60px;'>" . $current_date->format('d/m') . "</th>";
            $current_date->modify('+1 day');
        }

        echo "<th style='width: 80px;'>Total Hadir</th>";
        echo "<th style='width: 80px;'>Total Izin</th>";
        echo "<th style='width: 80px;'>Total Sakit</th>";
        echo "<th style='width: 80px;'>Total Alpha</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";

        $no = 1;
        foreach ($presensi_grouped as $santri) {
            echo "<tr>";
            echo "<td style='width: 50px;'>$no</td>";
            echo "<td style='width: 200px; text-align: left;'>" . htmlspecialchars($santri['nama_santri']) . "</td>";

            $total_hadir = $total_izin = $total_sakit = $total_alpha = 0;

            $current_date = clone $start_date;
            while ($current_date <= $end_date) {
                $date_str = $current_date->format('Y-m-d');
                $status = $santri['presensi'][$date_str] ?? '';

                if ($status == 'Hadir') {
                    $total_hadir++;
                } elseif ($status == 'Izin') {
                    $total_izin++;
                } elseif ($status == 'Sakit') {
                    $total_sakit++;
                } elseif ($status == 'Alpha') {
                    $total_alpha++;
                }

                echo "<td style='width: 60px;'>$status</td>";
                $current_date->modify('+1 day');
            }

            echo "<td style='width: 80px;'>$total_hadir</td>";
            echo "<td style='width: 80px;'>$total_izin</td>";
            echo "<td style='width: 80px;'>$total_sakit</td>";
            echo "<td style='width: 80px;'>$total_alpha</td>";
            echo "</tr>";
            $no++;
        }

        echo "</tbody>";
        echo "</table>";
    } elseif ($mode == 'per_santri') {
        echo "<table>";
        echo "<thead>";
        echo "<tr>";
        echo "<th style='width: 50px;'>No</th>";
        echo "<th style='width: 100px;'>Tanggal</th>";
        echo "<th style='width: 80px;'>Status</th>";
        echo "<th style='width: 200px; text-align: left;'>Keterangan</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";

        $no = 1;
        foreach ($presensi_data as $presensi) {
            echo "<tr>";
            echo "<td style='width: 50px;'>$no</td>";
            echo "<td style='width: 100px;'>" . htmlspecialchars($presensi['tanggal_formatted']) . "</td>";
            echo "<td style='width: 80px;'>" . htmlspecialchars($presensi['status']) . "</td>";
            echo "<td style='width: 200px; text-align: left;'>" . htmlspecialchars($presensi['keterangan'] ?? '-') . "</td>";
            echo "</tr>";
            $no++;
        }

        echo "</tbody>";
        echo "</table>";
    }

    echo "</body>";
    echo "</html>";
    exit;
} else {
    die('Metode tidak valid.');
}
?>
