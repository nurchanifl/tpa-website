<?php
session_start();
include('../includes/koneksidb.php');

// Cek apakah ada parameter 'file' di URL
if (isset($_GET['file']) && !empty($_GET['file'])) {
    // Ambil nama file dari parameter URL
    $file = urldecode($_GET['file']);
    $file_path = '../uploads/' . $file; // Perbarui path ke folder uploads

    // Cek apakah file tersebut ada
    if (file_exists($file_path)) {
        // Ambil informasi tentang file
        $file_name = basename($file_path);
        $file_size = filesize($file_path);
        
        // Tentukan tipe file berdasarkan ekstensi
        $file_ext = pathinfo($file_path, PATHINFO_EXTENSION);

        // Tentukan MIME type berdasarkan ekstensi file
        $mime_types = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'mp4' => 'video/mp4',
            'avi' => 'video/x-msvideo',
            'mov' => 'video/quicktime',
            'pdf' => 'application/pdf',
            // Tambahkan tipe MIME lainnya jika perlu
        ];

        if (array_key_exists($file_ext, $mime_types)) {
            $mime_type = $mime_types[$file_ext];
        } else {
            // Jika tipe MIME tidak dikenal, gunakan 'application/octet-stream' sebagai fallback
            $mime_type = 'application/octet-stream';
        }

        // Set header untuk mendownload file
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $mime_type);
        header('Content-Disposition: attachment; filename="' . $file_name . '"');
        header('Content-Length: ' . $file_size);
        header('Pragma: no-cache');
        header('Expires: 0');
        readfile($file_path); // Baca dan kirim file ke browser
        exit;
    } else {
        echo 'File tidak ditemukan di server: ' . $file_path;
    }
} else {
    echo 'Parameter file tidak ditemukan!';
}
?>