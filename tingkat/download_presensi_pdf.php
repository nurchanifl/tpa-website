<?php
require('../includes/fpdf.php');
require('../includes/koneksidb.php');

// Mulai sesi untuk mendapatkan nama pengguna
session_start();
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Unknown User';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    while ($row = mysqli_fetch_assoc($result_presensi)) {
        $presensi_grouped[$row['id_santri']]['nama_santri'] = $row['nama_santri'];
        $presensi_grouped[$row['id_santri']]['presensi'][$row['day']] = $row['status'];
    }
    mysqli_stmt_close($stmt_presensi);

    // Buat PDF
    $pdf = new FPDF('L', 'mm', 'A4');
    $pdf->SetAutoPageBreak(true, 10);
    $pdf->AddPage();

    // Judul Laporan
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, "LAPORAN PRESENSI SANTRI", 0, 1, 'C');
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 7, "Unit: $unit_nama | Kelas: $kelas_nama", 0, 1, 'C');
    $pdf->Cell(0, 7, "Bulan: " . DateTime::createFromFormat('!m', $bulan_filter)->format('F') . " $tahun_filter", 0, 1, 'C');

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
            } elseif ($status == 'Izin') {
                $short_status = 'I';
                $total_izin++;
            } elseif ($status == 'Sakit') {
                $short_status = 'S';
                $total_sakit++;
            } elseif ($status == 'Alpha') {
                $short_status = 'A';
                $total_alpha++;
            }

            $pdf->Cell(5, 7, $short_status, 1, 0, 'C');
        }

        $pdf->Cell(15, 7, $total_hadir, 1, 0, 'C');
        $pdf->Cell(15, 7, $total_izin, 1, 0, 'C');
        $pdf->Cell(15, 7, $total_sakit, 1, 0, 'C');
        $pdf->Cell(15, 7, $total_alpha, 1, 1, 'C');
    }
// Menambahkan nama pengguna di bawah tabel
$pdf->Ln(5); // Spasi setelah tabel
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 10, 'Wali Kelas: ' . $username, 0, 1, 'L'); // Nama user di bawah tabel

    $pdf->Output('I', 'laporan_presensi.pdf');
    exit;
} else {
    die('Metode tidak valid.');
}
?>