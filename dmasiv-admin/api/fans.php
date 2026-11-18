<?php
// Pendaftaran fans dari halaman "Be a Fans" -> tabel fans. Aturan validasi sama dengan fans/simpanfans.php (admin).
require_once __DIR__ . '/_bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') api_gagal('Gunakan metode POST.', 405);

$in = json_decode(file_get_contents('php://input'), true);
if (!is_array($in)) api_gagal('Data tidak valid.');

// Kolom jebakan untuk bot: manusia tidak akan mengisinya. Pura-pura sukses.
if (!empty($in['website'])) api_kirim(['pesan' => 'Terima kasih!']);

$f = fn($k) => trim((string)($in[$k] ?? ''));
$nama = $f('nama_lengkap'); $jk = $f('jenis_kelamin'); $tgl = $f('tanggal_lahir'); $alamat = $f('alamat');
$email = $f('email'); $telp = $f('no_telepon'); $sosmed = $f('url_sosmed');

$err = [];
if ($nama === '' || mb_strlen($nama) > 100) $err[] = 'Nama lengkap wajib diisi (maksimal 100 karakter).';
if (!in_array($jk, ['Pria', 'Wanita'], true)) $err[] = 'Jenis kelamin wajib dipilih.';
$d = DateTime::createFromFormat('Y-m-d', $tgl);
if (!$d || $d->format('Y-m-d') !== $tgl || $d > new DateTime('today')) $err[] = 'Tanggal lahir tidak valid.';
if ($alamat === '' || mb_strlen($alamat) > 1000) $err[] = 'Alamat (provinsi dan kota) wajib diisi.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 100) $err[] = 'Email tidak valid.';
if ($telp === '' || mb_strlen($telp) > 20 || !preg_match('/^[0-9+\-\s()]+$/', $telp)) $err[] = 'No. telepon wajib diisi (maksimal 20 karakter, hanya angka).';
if ($sosmed !== '' && (!preg_match('#^https?://#i', $sosmed) || !filter_var($sosmed, FILTER_VALIDATE_URL) || mb_strlen($sosmed) > 255))
    $err[] = 'Alamat media sosial harus berawalan http:// atau https://.';
if ($err) api_gagal($err[0], 422, ['errors' => $err]);

api_jalankan(function () use ($pdo, $nama, $jk, $tgl, $alamat, $email, $telp, $sosmed) {
    try {
        $pdo->prepare("INSERT INTO fans (nama_lengkap, jenis_kelamin, tanggal_lahir, alamat, email, no_telepon, url_sosmed)
                       VALUES (?, ?, ?, ?, ?, ?, ?)")
            ->execute([$nama, $jk, $tgl, $alamat, $email, $telp, $sosmed === '' ? null : $sosmed]);
    } catch (PDOException $ex) {
        if ((int)($ex->errorInfo[1] ?? 0) === 1062) api_gagal('Email ini sudah terdaftar sebagai fans.', 409);
        throw $ex;
    }
    api_kirim(['pesan' => 'Terima kasih, ' . $nama . '! Pendaftaran kamu sudah tercatat.']);
});
