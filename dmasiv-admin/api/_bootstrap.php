<?php
// Bootstrap API publik D'MASIV (hanya-baca, kecuali api/fans.php).
// Dipakai oleh semua file di folder api/. TIDAK memuat includes/auth.php karena
// data ini ditampilkan ke pengunjung website (frontend), bukan ke admin.

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');               // frontend boleh berada di port/domain lain
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('X-Content-Type-Options: nosniff');

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') { http_response_code(204); exit; }

// Jika koneksi.php gagal ia mencetak teks biasa lalu exit; bungkus jadi JSON agar frontend tetap bisa membacanya.
ob_start(function ($buf) {
    $t = ltrim($buf);
    if ($t === '' || $t[0] === '{' || $t[0] === '[') return $buf;
    return json_encode(['ok' => false, 'error' => 'Server tidak dapat mengambil data. Periksa config/koneksi.php dan database.']);
});

require_once __DIR__ . '/../config/koneksi.php';    // $pdo, BASE_URL
if (session_status() === PHP_SESSION_ACTIVE) session_write_close(); // API tidak butuh sesi; cegah request paralel saling menunggu

// Ubah path dari database (mis. "gambar/album/x.jpg") menjadi URL lengkap yang bisa dipakai <img> di frontend
function api_url_file($path) {
    $path = trim((string)$path);
    if ($path === '') return null;
    if (preg_match('#^https?://#i', $path)) return $path;        // sudah URL penuh (mis. thumbnail YouTube)
    $skema = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host  = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $seg   = array_map('rawurlencode', explode('/', ltrim($path, '/')));
    return $skema . '://' . $host . rtrim(BASE_URL, '/') . '/' . implode('/', $seg);
}

// Hanya izinkan tautan http/https agar data aneh di database tidak jadi "javascript:..."
function api_url_link($u) {
    $u = trim((string)$u);
    return preg_match('#^https?://#i', $u) ? $u : null;
}

function api_kirim($data) {
    echo json_encode(['ok' => true] + $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function api_gagal($pesan, $kode = 400, array $extra = []) {
    http_response_code($kode);
    echo json_encode(['ok' => false, 'error' => $pesan] + $extra, JSON_UNESCAPED_UNICODE);
    exit;
}

// Bungkus semua query: error database -> JSON 500 (tanpa membocorkan detail)
function api_jalankan(callable $fn) {
    try { $fn(); }
    catch (Throwable $ex) { api_gagal('Terjadi kesalahan pada server.', 500); }
}
