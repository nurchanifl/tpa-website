<?php
// Config otomatis untuk lokal dan hosting
if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] == 'localhost') {
    // Lokal XAMPP
    $base_url = '/tpa';  // Ganti 'tpa' dengan folder XAMPP Anda jika beda
    $db_host = 'localhost';
    $db_user = 'root';
    $db_pass = '';
    $db_name = 'tpa_db';  // Nama DB lokal
} else {
    // Hosting
    $base_url = '';  // Root
    $db_host = 'localhost';
    $db_user = 'siputlai_tpa';
    $db_pass = 'nabilahjkt48';
    $db_name = 'siputlai_tpa_db';
}
?>
