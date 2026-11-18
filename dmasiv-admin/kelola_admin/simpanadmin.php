<?php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/koneksi_mysqli.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ' . BASE_URL . '/kelola_admin/bacaadmin.php');
  exit;
}
csrf_check();

// data dari form_admin.php
$username = trim($_POST['username'] ?? '');
$nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';   // tidak di-trim, sama seperti proses login
$status = trim($_POST['status'] ?? '');

$errors = [];
if ($username === '') { $errors[] = 'Username wajib diisi.'; }
elseif (mb_strlen($username) > 50) { $errors[] = 'Username maksimal 50 karakter.'; }
if ($nama_lengkap === '') { $errors[] = 'Nama lengkap wajib diisi.'; }
elseif (mb_strlen($nama_lengkap) > 100) { $errors[] = 'Nama lengkap maksimal 100 karakter.'; }
if ($email === '') { $errors[] = 'Email wajib diisi.'; }
elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 100) { $errors[] = 'Email tidak valid.'; }
if ($password === '') { $errors[] = 'Kata sandi wajib diisi.'; }
elseif (strlen($password) < 8) { $errors[] = 'Kata sandi minimal 8 karakter.'; }
if ($status === '') { $errors[] = 'Status akun wajib dipilih.'; }
elseif (!in_array($status, ['aktif', 'nonaktif'], true)) { $errors[] = 'Status akun tidak valid.'; }

// jika ada isian yang salah, kembali ke form (file baru dipindahkan hanya jika semua isian lolos)
if ($errors) {
  $old = $_POST;
  unset($old['csrf'], $old['password']);   // kata sandi tidak dikembalikan ke form
  $_SESSION['form_admin_add_errors'] = $errors;
  $_SESSION['form_admin_add_old'] = $old;
  header('Location: ' . BASE_URL . '/kelola_admin/form_admin.php');
  exit;
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);

// SQL to insert a record
$sql = "INSERT INTO admin (username, nama_lengkap, email, password, status) VALUES (?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "sssss", $username, $nama_lengkap, $email, $password_hash, $status);

if (mysqli_stmt_execute($stmt)) {
  set_flash('success', 'Data berhasil ditambahkan.');
  header('Location: ' . BASE_URL . '/kelola_admin/bacaadmin.php');
  exit;
} else {
  // jika penyimpanan gagal
  $errors[] = (mysqli_stmt_errno($stmt) == 1062) ? 'Username atau email ini sudah dipakai akun lain.' : 'Terjadi kesalahan pada database. Periksa kembali isian Anda.';
  $old = $_POST;
  unset($old['csrf'], $old['password']);   // kata sandi tidak dikembalikan ke form
  $_SESSION['form_admin_add_errors'] = $errors;
  $_SESSION['form_admin_add_old'] = $old;
  header('Location: ' . BASE_URL . '/kelola_admin/form_admin.php');
  exit;
}
?>