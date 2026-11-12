<?php
// Dipanggil di setiap halaman admin: wajib login dan akun harus aktif
require_once __DIR__ . '/../config/koneksi.php';

if (empty($_SESSION['id_admin'])) {
    redirect('/login.php');
}

$cek = $pdo->prepare('SELECT status FROM admin WHERE id_admin = ?');
$cek->execute([$_SESSION['id_admin']]);
$akun = $cek->fetch();
if (!$akun || $akun['status'] !== 'aktif') {
    session_destroy();
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}

function csrf_token() {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}
function csrf_check() {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(419);
        exit('Token tidak valid. Muat ulang halaman.');
    }
}
