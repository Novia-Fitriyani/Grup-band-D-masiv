<?php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/koneksi_mysqli.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ' . BASE_URL . '/kategori_event/bacakategorievent.php');
  exit;
}
csrf_check();

// data dari form_editkategorievent.php
$id = (int)($_POST['id'] ?? 0);
$nama_kategori = trim($_POST['nama_kategori'] ?? '');

$stmt = mysqli_prepare($conn, "SELECT * FROM kategori_event WHERE id_kategori = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
if (!$row) {
  set_flash('danger', 'Data tidak ditemukan.');
  header('Location: ' . BASE_URL . '/kategori_event/bacakategorievent.php');
  exit;
}

$errors = [];
if ($nama_kategori === '') { $errors[] = 'Nama kategori wajib diisi.'; }
elseif (mb_strlen($nama_kategori) > 50) { $errors[] = 'Nama kategori maksimal 50 karakter.'; }

// jika ada isian yang salah, kembali ke form edit
if ($errors) {
  $old = $_POST;
  unset($old['csrf']);
  $_SESSION['form_kategorievent_edit_errors'] = $errors;
  $_SESSION['form_kategorievent_edit_old'] = $old;
  header('Location: ' . BASE_URL . '/kategori_event/form_editkategorievent.php?id=' . $id);
  exit;
}

// SQL to update a record
$set_kolom = ['nama_kategori=?'];
$bind_tipe  = "s";
$bind_nilai = [$nama_kategori];
$bind_tipe .= "i"; $bind_nilai[] = $id;
$sql = "UPDATE kategori_event SET " . implode(", ", $set_kolom) . " WHERE id_kategori = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, $bind_tipe, ...$bind_nilai);

if (mysqli_stmt_execute($stmt)) {
  set_flash('success', 'Data berhasil diperbarui.');
  header('Location: ' . BASE_URL . '/kategori_event/bacakategorievent.php');
  exit;
} else {
  // jika perubahan gagal
  $errors[] = (mysqli_stmt_errno($stmt) == 1062) ? 'Nama kategori ini sudah ada.' : 'Terjadi kesalahan pada database. Periksa kembali isian Anda.';
  $old = $_POST;
  unset($old['csrf']);
  $_SESSION['form_kategorievent_edit_errors'] = $errors;
  $_SESSION['form_kategorievent_edit_old'] = $old;
  header('Location: ' . BASE_URL . '/kategori_event/form_editkategorievent.php?id=' . $id);
  exit;
}
?>