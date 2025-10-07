<?php
require('../includes/fpdf.php');
require('../includes/koneksidb.php');

// Mulai sesi untuk mendapatkan nama pengguna
session_start();
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Unknown User';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mode = $_POST['mode'] ?? 'per_kelas';
    $id_unit = $_POST['id_unit'] ?? '';
    $id_kelas = $_POST['id_kelas'] ?? '';
    $bulan_filter = $_POST['bulan_filter'] ?? date('m');
    $tahun_filter = $_POST['tahun_filter'] ?? date('Y');

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

    // Ambil data presensi
    $first_day_of_month = "$tahun_filter-$bulan_filter-01";
    $last_day_of_month = date("Y-m-t", strtotime($first_day_of_month));

    if ($mode == 'per_kelas') {
        $sql_presensi = "SELECT p.*, s.nama_santri, DAY(p.tanggal) as day
                         FROM presensi p
                         JOIN santri s ON p.id_santri = s.id
                         WHERE s.id_kelas = ? AND p.tanggal BETWEEN ? AND ?
                         ORDER BY s.id, p.tanggal";

        $stmt_presensi = mysqli_prepare($conn, $sql_presensi);
        mysqli_stmt_bind_param($stmt_presensi, 'iss', $id_kelas, $first_day_of_month, $last_day_of_month);
        mysqli_stmt_execute($stmt_presensi);
        $result_presensi = mysqli_stmt_get_result($stmt_presensi);

        $presensi_grouped = [];
        $total_kelas_hadir = 0;
        $total_kelas_izin = 0;
        $total_kelas_sakit = 0;
        $total_kelas_alpha = 0;
        while ($row = mysqli_fetch_assoc($result_presensi)) {
            $presensi_grouped[$row['id_santri']]['nama_santri'] = $row['nama_santri'];
            $presensi_grouped[$row['id_santri']]['presensi'][$row['day']] = $row['status'];
        }
        mysqli_stmt_close($stmt_presensi);
    } elseif ($mode == 'per_santri') {
        $id_santri = $_POST['id_santri'] ?? '';
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
        $total_santri_hadir = 0;
        $total_santri_izin = 0;
        $total_santri_sakit = 0;
        $total_santri_alpha = 0;
        while ($row = mysqli_fetch_assoc($result_presensi)) {
            $presensi_data[] = $row;
            if ($row['status'] === 'Hadir') {
                $total_santri_hadir++;
            } elseif ($row['status'] === 'Izin') {
                $total_santri_izin++;
            } elseif ($row['status'] === 'Sakit') {
                $total_santri_sakit++;
            } elseif ($row['status'] === 'Alpha') {
                $total_santri_alpha++;
            }
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

    // Buat PDF
    $orientation = ($mode == 'per_santri') ? 'P' : 'L'; // Portrait untuk per santri, Landscape untuk per kelas
    $pdf = new FPDF($orientation, 'mm', 'A4');
    $pdf->SetAutoPageBreak(true, 10);
    $pdf->AddPage();

    // Judul Laporan
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, "LAPORAN PRESENSI SANTRI", 0, 1, 'C');
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 7, "Unit: $unit_nama | Kelas: $kelas_nama", 0, 1, 'C');
    if ($mode == 'per_santri') {
        $pdf->Cell(0, 7, "Santri: $santri_nama", 0, 1, 'C');
    }
    $pdf->Cell(0, 7, "Bulan: " . DateTime::createFromFormat('!m', $bulan_filter)->format('F') . " $tahun_filter", 0, 1, 'C');

    if ($mode == 'per_kelas') {
        // Header Tabel
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(10, 10, 'No', 1, 0, 'C');
        $pdf->Cell(50, 10, 'Nama Santri', 1, 0, 'C');

        // Header Tanggal
        for ($day = 1; $day <= date('t', strtotime($first_day_of_month)); $day++) {
            $pdf->Cell(5, 10, $day, 1, 0, 'C');
        }

        // Header Total
        $pdf->Cell(15, 10, 'Hadir', 1, 0, 'C');
        $pdf->Cell(15, 10, 'Izin', 1, 0, 'C');
        $pdf->Cell(15, 10, 'Sakit', 1, 0, 'C');
        $pdf->Cell(15, 10, 'Alpha', 1, 1, 'C');

        // Data Presensi
        $pdf->SetFont('Arial', '', 10);
        $no = 1;
        foreach ($presensi_grouped as $santri) {
            $pdf->Cell(10, 7, $no++, 1, 0, 'C');
            $pdf->Cell(50, 7, $santri['nama_santri'], 1, 0, 'L');

            $total_hadir = $total_izin = $total_sakit = $total_alpha = 0;

            for ($day = 1; $day <= date('t', strtotime($first_day_of_month)); $day++) {
                $status = $santri['presensi'][$day] ?? '';
                $short_status = '';

                if ($status == 'Hadir') {
                    $short_status = 'H';
                    $total_hadir++;
                    $total_kelas_hadir++;
                } elseif ($status == 'Izin') {
                    $short_status = 'I';
                    $total_izin++;
                    $total_kelas_izin++;
                } elseif ($status == 'Sakit') {
                    $short_status = 'S';
                    $total_sakit++;
                    $total_kelas_sakit++;
                } elseif ($status == 'Alpha') {
                    $short_status = 'A';
                    $total_alpha++;
                    $total_kelas_alpha++;
                }

                $pdf->Cell(5, 7, $short_status, 1, 0, 'C');
            }

            $pdf->Cell(15, 7, $total_hadir, 1, 0, 'C');
            $pdf->Cell(15, 7, $total_izin, 1, 0, 'C');
            $pdf->Cell(15, 7, $total_sakit, 1, 0, 'C');
            $pdf->Cell(15, 7, $total_alpha, 1, 1, 'C');
        }
    } elseif ($mode == 'per_santri') {
        // Header Tabel per Santri
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(15, 10, 'No', 1, 0, 'C');
        $pdf->Cell(35, 10, 'Tanggal', 1, 0, 'C');
        $pdf->Cell(25, 10, 'Status', 1, 0, 'C');
        $pdf->Cell(100, 10, 'Keterangan', 1, 1, 'C');

        // Data Presensi per Santri
        $pdf->SetFont('Arial', '', 10);
        $no = 1;
        foreach ($presensi_data as $presensi) {
            $pdf->Cell(15, 7, $no++, 1, 0, 'C');
            $pdf->Cell(35, 7, $presensi['tanggal_formatted'], 1, 0, 'C');
            $pdf->Cell(25, 7, $presensi['status'], 1, 0, 'C');
            $pdf->Cell(100, 7, $presensi['keterangan'] ?? '-', 1, 1, 'L');
        }

        // Ringkasan Kehadiran Santri
        $pdf->Ln(10);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Ringkasan Kehadiran Santri', 0, 1, 'C');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(30, 7, 'Total Hadir:', 0, 0, 'L');
        $pdf->Cell(15, 7, $total_santri_hadir, 0, 0, 'L');
        $pdf->Cell(30, 7, 'Total Izin:', 0, 0, 'L');
        $pdf->Cell(15, 7, $total_santri_izin, 0, 0, 'L');
        $pdf->Cell(30, 7, 'Total Sakit:', 0, 0, 'L');
        $pdf->Cell(15, 7, $total_santri_sakit, 0, 0, 'L');
        $pdf->Cell(30, 7, 'Total Alpha:', 0, 0, 'L');
        $pdf->Cell(15, 7, $total_santri_alpha, 0, 1, 'L');
    }

    // Menambahkan nama pengguna di bawah tabel
    $pdf->Ln(5); // Spasi setelah tabel
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 10, 'Wali Kelas: ' . $username, 0, 1, 'L'); // Nama user di bawah tabel

    // Generate dynamic filename
    $bulan_nama = DateTime::createFromFormat('!m', $bulan_filter)->format('F');
    if ($mode == 'per_kelas') {
        $filename = 'Laporan Presensi ' . $unit_nama . ' ' . $kelas_nama . ' ' . $bulan_nama . ' ' . $tahun_filter . '.pdf';
    } elseif ($mode == 'per_santri') {
        $filename = 'Laporan Presensi ' . $santri_nama . ' ' . $bulan_nama . ' ' . $tahun_filter . '.pdf';
    }

    $pdf->Output('D', $filename);
    exit;
} else {
    die('Metode tidak valid.');
}
?>