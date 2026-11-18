<?php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/koneksi_mysqli.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ' . BASE_URL . '/mitra/bacamitra.php');
  exit;
}
csrf_check();

// data dari form_editmitra.php
$id = (int)($_POST['id'] ?? 0);
$nama_mitra = trim($_POST['nama_mitra'] ?? '');
$url = trim($_POST['url'] ?? '');

$stmt = mysqli_prepare($conn, "SELECT * FROM mitra WHERE id_mitra = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
if (!$row) {
  set_flash('danger', 'Data tidak ditemukan.');
  header('Location: ' . BASE_URL . '/mitra/bacamitra.php');
  exit;
}

$errors = [];
if ($nama_mitra === '') { $errors[] = 'Nama partner wajib diisi.'; }
elseif (mb_strlen($nama_mitra) > 50) { $errors[] = 'Nama partner maksimal 50 karakter.'; }
if ($url !== '' && (!filter_var($url, FILTER_VALIDATE_URL) || !preg_match('#^https?://#i', $url) || mb_strlen($url) > 255)) { $errors[] = 'Tautan partner harus berupa alamat lengkap berawalan http:// atau https://.'; }

// jika ada isian yang salah, kembali ke form edit
if ($errors) {
  $old = $_POST;
  unset($old['csrf']);
  $_SESSION['form_mitra_edit_errors'] = $errors;
  $_SESSION['form_mitra_edit_old'] = $old;
  header('Location: ' . BASE_URL . '/mitra/form_editmitra.php?id=' . $id);
  exit;
}

// kolom opsional yang kosong disimpan sebagai NULL
$url = ($url === '') ? null : $url;

// SQL to update a record
$set_kolom = ['nama_mitra=?', 'url=?'];
$bind_tipe  = "ss";
$bind_nilai = [$nama_mitra, $url];
$bind_tipe .= "i"; $bind_nilai[] = $id;
$sql = "UPDATE mitra SET " . implode(", ", $set_kolom) . " WHERE id_mitra = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, $bind_tipe, ...$bind_nilai);

if (mysqli_stmt_execute($stmt)) {
  set_flash('success', 'Data berhasil diperbarui.');
  header('Location: ' . BASE_URL . '/mitra/bacamitra.php');
  exit;
} else {
  // jika perubahan gagal
  $errors[] = 'Terjadi kesalahan pada database. Periksa kembali isian Anda.';
  $old = $_POST;
  unset($old['csrf']);
  $_SESSION['form_mitra_edit_errors'] = $errors;
  $_SESSION['form_mitra_edit_old'] = $old;
  header('Location: ' . BASE_URL . '/mitra/form_editmitra.php?id=' . $id);
  exit;
}
?>