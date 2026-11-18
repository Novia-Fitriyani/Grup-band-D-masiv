<?php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/koneksi_mysqli.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ' . BASE_URL . '/mitra/bacamitra.php');
  exit;
}
csrf_check();

// data dari form_mitra.php
$nama_mitra = trim($_POST['nama_mitra'] ?? '');
$url = trim($_POST['url'] ?? '');

$errors = [];
if ($nama_mitra === '') { $errors[] = 'Nama partner wajib diisi.'; }
elseif (mb_strlen($nama_mitra) > 50) { $errors[] = 'Nama partner maksimal 50 karakter.'; }
if ($url !== '' && (!filter_var($url, FILTER_VALIDATE_URL) || !preg_match('#^https?://#i', $url) || mb_strlen($url) > 255)) { $errors[] = 'Tautan partner harus berupa alamat lengkap berawalan http:// atau https://.'; }

// jika ada isian yang salah, kembali ke form (file baru dipindahkan hanya jika semua isian lolos)
if ($errors) {
  $old = $_POST;
  unset($old['csrf']);
  $_SESSION['form_mitra_add_errors'] = $errors;
  $_SESSION['form_mitra_add_old'] = $old;
  header('Location: ' . BASE_URL . '/mitra/form_mitra.php');
  exit;
}

// kolom opsional yang kosong disimpan sebagai NULL
$url = ($url === '') ? null : $url;
$id_admin = (int)$_SESSION['id_admin'];

// SQL to insert a record
$sql = "INSERT INTO mitra (nama_mitra, url, id_admin) VALUES (?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ssi", $nama_mitra, $url, $id_admin);

if (mysqli_stmt_execute($stmt)) {
  set_flash('success', 'Data berhasil ditambahkan.');
  header('Location: ' . BASE_URL . '/mitra/bacamitra.php');
  exit;
} else {
  // jika penyimpanan gagal
  $errors[] = 'Terjadi kesalahan pada database. Periksa kembali isian Anda.';
  $old = $_POST;
  unset($old['csrf']);
  $_SESSION['form_mitra_add_errors'] = $errors;
  $_SESSION['form_mitra_add_old'] = $old;
  header('Location: ' . BASE_URL . '/mitra/form_mitra.php');
  exit;
}
?>