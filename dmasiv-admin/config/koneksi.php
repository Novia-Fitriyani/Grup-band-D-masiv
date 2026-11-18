<?php
// Pengaturan koneksi database dan konstanta dasar
define('BASE_URL', '/dmasiv-admin'); // nama folder proyek di htdocs / www

$host = 'localhost';
$db   = 'dmasiv_db';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $ex) {
    exit('Koneksi database gagal. Periksa config/koneksi.php');
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Escape output agar aman dari XSS
function e($teks) {
    return htmlspecialchars((string)$teks, ENT_QUOTES, 'UTF-8');
}
function redirect($path) {
    header('Location: ' . BASE_URL . $path);
    exit;
}
function set_flash($type, $msg) {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}
