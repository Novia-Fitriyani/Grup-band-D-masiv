<?php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/koneksi_mysqli.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ' . BASE_URL . '/media_sosial/bacamediasosial.php');
  exit;
}
csrf_check();

// data dari form_editmediasosial.php
$id = (int)($_POST['id'] ?? 0);
$platform = trim($_POST['platform'] ?? '');
$url = trim($_POST['url'] ?? '');
$ikon = trim($_POST['ikon'] ?? '');
$nama_kontak = trim($_POST['nama_kontak'] ?? '');
$no_telepon = trim($_POST['no_telepon'] ?? '');

$stmt = mysqli_prepare($conn, "SELECT * FROM media_sosial WHERE id_sosmed = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
if (!$row) {
  set_flash('danger', 'Data tidak ditemukan.');
  header('Location: ' . BASE_URL . '/media_sosial/bacamediasosial.php');
  exit;
}

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

// jika ada isian yang salah, kembali ke form edit
if ($errors) {
  $old = $_POST;
  unset($old['csrf']);
  $_SESSION['form_mediasosial_edit_errors'] = $errors;
  $_SESSION['form_mediasosial_edit_old'] = $old;
  header('Location: ' . BASE_URL . '/media_sosial/form_editmediasosial.php?id=' . $id);
  exit;
}

// kolom opsional yang kosong disimpan sebagai NULL
$ikon = ($ikon === '') ? null : $ikon;

// SQL to update a record
$set_kolom = ['platform=?', 'url=?', 'ikon=?', 'nama_kontak=?', 'no_telepon=?'];
$bind_tipe  = "sssss";
$bind_nilai = [$platform, $url, $ikon, $nama_kontak, $no_telepon];
$bind_tipe .= "i"; $bind_nilai[] = $id;
$sql = "UPDATE media_sosial SET " . implode(", ", $set_kolom) . " WHERE id_sosmed = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, $bind_tipe, ...$bind_nilai);

if (mysqli_stmt_execute($stmt)) {
  set_flash('success', 'Data berhasil diperbarui.');
  header('Location: ' . BASE_URL . '/media_sosial/bacamediasosial.php');
  exit;
} else {
  // jika perubahan gagal
  $errors[] = 'Terjadi kesalahan pada database. Periksa kembali isian Anda.';
  $old = $_POST;
  unset($old['csrf']);
  $_SESSION['form_mediasosial_edit_errors'] = $errors;
  $_SESSION['form_mediasosial_edit_old'] = $old;
  header('Location: ' . BASE_URL . '/media_sosial/form_editmediasosial.php?id=' . $id);
  exit;
}
?>