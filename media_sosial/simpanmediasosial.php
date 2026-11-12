<?php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/koneksi_mysqli.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ' . BASE_URL . '/media_sosial/bacamediasosial.php');
  exit;
}
csrf_check();

// data dari form_mediasosial.php
$platform = trim($_POST['platform'] ?? '');
$url = trim($_POST['url'] ?? '');
$ikon = trim($_POST['ikon'] ?? '');
$nama_kontak = trim($_POST['nama_kontak'] ?? '');
$no_telepon = trim($_POST['no_telepon'] ?? '');

$errors = [];
if ($platform === '') { $errors[] = 'Platform wajib diisi.'; }
elseif (mb_strlen($platform) > 30) { $errors[] = 'Platform maksimal 30 karakter.'; }
if ($url === '') { $errors[] = 'Alamat akun wajib diisi.'; }
elseif (!filter_var($url, FILTER_VALIDATE_URL) || !preg_match('#^https?://#i', $url) || mb_strlen($url) > 255) { $errors[] = 'Alamat akun harus berupa alamat lengkap berawalan http:// atau https://.'; }
if ($ikon !== '' && (mb_strlen($ikon) > 50)) { $errors[] = 'Nama class ikon Font Awesome maksimal 50 karakter.'; }
if ($nama_kontak === '') { $errors[] = 'Nama narahubung wajib diisi.'; }
elseif (mb_strlen($nama_kontak) > 50) { $errors[] = 'Nama narahubung maksimal 50 karakter.'; }
if ($no_telepon === '') { $errors[] = 'No. telepon narahubung wajib diisi.'; }
elseif (mb_strlen($no_telepon) > 20) { $errors[] = 'No. telepon narahubung maksimal 20 karakter.'; }

// jika ada isian yang salah, kembali ke form (file baru dipindahkan hanya jika semua isian lolos)
if ($errors) {
  $old = $_POST;
  unset($old['csrf']);
  $_SESSION['form_mediasosial_add_errors'] = $errors;
  $_SESSION['form_mediasosial_add_old'] = $old;
  header('Location: ' . BASE_URL . '/media_sosial/form_mediasosial.php');
  exit;
}

// kolom opsional yang kosong disimpan sebagai NULL
$ikon = ($ikon === '') ? null : $ikon;
$id_admin = (int)$_SESSION['id_admin'];

// SQL to insert a record
$sql = "INSERT INTO media_sosial (platform, url, ikon, nama_kontak, no_telepon, id_admin) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "sssssi", $platform, $url, $ikon, $nama_kontak, $no_telepon, $id_admin);

if (mysqli_stmt_execute($stmt)) {
  set_flash('success', 'Data berhasil ditambahkan.');
  header('Location: ' . BASE_URL . '/media_sosial/bacamediasosial.php');
  exit;
} else {
  // jika penyimpanan gagal
  $errors[] = 'Terjadi kesalahan pada database. Periksa kembali isian Anda.';
  $old = $_POST;
  unset($old['csrf']);
  $_SESSION['form_mediasosial_add_errors'] = $errors;
  $_SESSION['form_mediasosial_add_old'] = $old;
  header('Location: ' . BASE_URL . '/media_sosial/form_mediasosial.php');
  exit;
}
?>