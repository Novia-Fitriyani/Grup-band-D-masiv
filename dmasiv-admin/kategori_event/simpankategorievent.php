<?php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/koneksi_mysqli.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ' . BASE_URL . '/kategori_event/bacakategorievent.php');
  exit;
}
csrf_check();

// data dari form_kategorievent.php
$nama_kategori = trim($_POST['nama_kategori'] ?? '');

$errors = [];
if ($nama_kategori === '') { $errors[] = 'Nama kategori wajib diisi.'; }
elseif (mb_strlen($nama_kategori) > 50) { $errors[] = 'Nama kategori maksimal 50 karakter.'; }

// jika ada isian yang salah, kembali ke form (file baru dipindahkan hanya jika semua isian lolos)
if ($errors) {
  $old = $_POST;
  unset($old['csrf']);
  $_SESSION['form_kategorievent_add_errors'] = $errors;
  $_SESSION['form_kategorievent_add_old'] = $old;
  header('Location: ' . BASE_URL . '/kategori_event/form_kategorievent.php');
  exit;
}


// SQL to insert a record
$sql = "INSERT INTO kategori_event (nama_kategori) VALUES (?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $nama_kategori);

if (mysqli_stmt_execute($stmt)) {
  set_flash('success', 'Data berhasil ditambahkan.');
  header('Location: ' . BASE_URL . '/kategori_event/bacakategorievent.php');
  exit;
} else {
  // jika penyimpanan gagal
  $errors[] = (mysqli_stmt_errno($stmt) == 1062) ? 'Nama kategori ini sudah ada.' : 'Terjadi kesalahan pada database. Periksa kembali isian Anda.';
  $old = $_POST;
  unset($old['csrf']);
  $_SESSION['form_kategorievent_add_errors'] = $errors;
  $_SESSION['form_kategorievent_add_old'] = $old;
  header('Location: ' . BASE_URL . '/kategori_event/form_kategorievent.php');
  exit;
}
?>