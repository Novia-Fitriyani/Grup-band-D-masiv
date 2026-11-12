<?php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/koneksi_mysqli.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ' . BASE_URL . '/fans/bacafans.php');
  exit;
}
csrf_check();

// data dari form_fans.php
$nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
$jenis_kelamin = trim($_POST['jenis_kelamin'] ?? '');
$tanggal_lahir = trim($_POST['tanggal_lahir'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');
$email = trim($_POST['email'] ?? '');
$no_telepon = trim($_POST['no_telepon'] ?? '');
$url_sosmed = trim($_POST['url_sosmed'] ?? '');

$errors = [];
if ($nama_lengkap === '') { $errors[] = 'Nama lengkap wajib diisi.'; }
elseif (mb_strlen($nama_lengkap) > 100) { $errors[] = 'Nama lengkap maksimal 100 karakter.'; }
if ($jenis_kelamin === '') { $errors[] = 'Jenis kelamin wajib dipilih.'; }
elseif (!in_array($jenis_kelamin, ['Pria', 'Wanita'], true)) { $errors[] = 'Jenis kelamin tidak valid.'; }
$tgl = DateTime::createFromFormat('Y-m-d', $tanggal_lahir);
if ($tanggal_lahir === '') { $errors[] = 'Tanggal lahir wajib diisi.'; }
elseif (!$tgl || $tgl->format('Y-m-d') !== $tanggal_lahir) { $errors[] = 'Tanggal lahir tidak valid.'; }
if ($alamat === '') { $errors[] = 'Alamat lengkap wajib diisi.'; }
elseif (mb_strlen($alamat) > 65535) { $errors[] = 'Alamat lengkap maksimal 65535 karakter.'; }
if ($email === '') { $errors[] = 'Email wajib diisi.'; }
elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 100) { $errors[] = 'Email tidak valid.'; }
if ($no_telepon === '') { $errors[] = 'No. telepon wajib diisi.'; }
elseif (mb_strlen($no_telepon) > 20) { $errors[] = 'No. telepon maksimal 20 karakter.'; }
if ($url_sosmed !== '' && (!filter_var($url_sosmed, FILTER_VALIDATE_URL) || !preg_match('#^https?://#i', $url_sosmed) || mb_strlen($url_sosmed) > 255)) { $errors[] = 'Alamat akun media sosial harus berupa alamat lengkap berawalan http:// atau https://.'; }

// jika ada isian yang salah, kembali ke form (file baru dipindahkan hanya jika semua isian lolos)
if ($errors) {
  $old = $_POST;
  unset($old['csrf']);
  $_SESSION['form_fans_add_errors'] = $errors;
  $_SESSION['form_fans_add_old'] = $old;
  header('Location: ' . BASE_URL . '/fans/form_fans.php');
  exit;
}

// kolom opsional yang kosong disimpan sebagai NULL
$url_sosmed = ($url_sosmed === '') ? null : $url_sosmed;

// SQL to insert a record
$sql = "INSERT INTO fans (nama_lengkap, jenis_kelamin, tanggal_lahir, alamat, email, no_telepon, url_sosmed) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "sssssss", $nama_lengkap, $jenis_kelamin, $tanggal_lahir, $alamat, $email, $no_telepon, $url_sosmed);

if (mysqli_stmt_execute($stmt)) {
  set_flash('success', 'Data berhasil ditambahkan.');
  header('Location: ' . BASE_URL . '/fans/bacafans.php');
  exit;
} else {
  // jika penyimpanan gagal
  $errors[] = (mysqli_stmt_errno($stmt) == 1062) ? 'Email ini sudah terdaftar sebagai fans.' : 'Terjadi kesalahan pada database. Periksa kembali isian Anda.';
  $old = $_POST;
  unset($old['csrf']);
  $_SESSION['form_fans_add_errors'] = $errors;
  $_SESSION['form_fans_add_old'] = $old;
  header('Location: ' . BASE_URL . '/fans/form_fans.php');
  exit;
}
?>