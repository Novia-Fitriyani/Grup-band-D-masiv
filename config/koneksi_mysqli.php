<?php
require_once __DIR__ . '/koneksi.php';   // memuat $host, $db, $user, $pass, BASE_URL, sesi, e(), set_flash()

mysqli_report(MYSQLI_REPORT_OFF);

$conn = mysqli_connect($host, $user, $pass, $db);

// check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');
