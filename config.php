<?php
if ($_SERVER['HTTP_HOST'] == 'localhost') {
    $base_url = 'http://localhost/tpa';
    $db_host = 'localhost';
    $db_user = 'root';
    $db_pass = '';
    $db_name = 'tpa_db';
} else {
    $base_url = 'https://siputlaili.my.id';
    $db_host = 'localhost';
    $db_user = 'siputlai';
    $db_pass = 'nabilahjkt48';
    $db_name = 'siputlai_tpa';
}
?>
