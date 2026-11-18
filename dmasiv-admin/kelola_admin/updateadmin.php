<?php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/koneksi_mysqli.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ' . BASE_URL . '/kelola_admin/bacaadmin.php');
  exit;
}
csrf_check();

// data dari form_editadmin.php
$id = (int)($_POST['id'] ?? 0);
$username = trim($_POST['username'] ?? '');
$nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';   // tidak di-trim, sama seperti proses login
$status = trim($_POST['status'] ?? '');

$stmt = mysqli_prepare($conn, "SELECT * FROM admin WHERE id_admin = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
if (!$row) {
  set_flash('danger', 'Data tidak ditemukan.');
  header('Location: ' . BASE_URL . '/kelola_admin/bacaadmin.php');
  exit;
}

$errors = [];
if ($username === '') { $errors[] = 'Username wajib diisi.'; }
elseif (mb_strlen($username) > 50) { $errors[] = 'Username maksimal 50 karakter.'; }
if ($nama_lengkap === '') { $errors[] = 'Nama lengkap wajib diisi.'; }
elseif (mb_strlen($nama_lengkap) > 100) { $errors[] = 'Nama lengkap maksimal 100 karakter.'; }
if ($email === '') { $errors[] = 'Email wajib diisi.'; }
elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 100) { $errors[] = 'Email tidak valid.'; }
if ($password !== '' && strlen($password) < 8) { $errors[] = 'Kata sandi minimal 8 karakter.'; }
if ($status === '') { $errors[] = 'Status akun wajib dipilih.'; }
elseif (!in_array($status, ['aktif', 'nonaktif'], true)) { $errors[] = 'Status akun tidak valid.'; }
if ($id === (int)$_SESSION['id_admin'] && $status === 'nonaktif') { $errors[] = 'Anda tidak bisa menonaktifkan akun yang sedang dipakai.'; }

// jika ada isian yang salah, kembali ke form edit
if ($errors) {
  $old = $_POST;
  unset($old['csrf'], $old['password']);   // kata sandi tidak dikembalikan ke form
  $_SESSION['form_admin_edit_errors'] = $errors;
  $_SESSION['form_admin_edit_old'] = $old;
  header('Location: ' . BASE_URL . '/kelola_admin/form_editadmin.php?id=' . $id);
  exit;
}

// SQL to update a record
$set_kolom = ['username=?', 'nama_lengkap=?', 'email=?', 'status=?'];
$bind_tipe  = "ssss";
$bind_nilai = [$username, $nama_lengkap, $email, $status];
if ($password !== '') {
  $set_kolom[] = 'password=?'; $bind_tipe .= 's'; $bind_nilai[] = password_hash($password, PASSWORD_DEFAULT);
}
$bind_tipe .= "i"; $bind_nilai[] = $id;
$sql = "UPDATE admin SET " . implode(", ", $set_kolom) . " WHERE id_admin = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, $bind_tipe, ...$bind_nilai);

if (mysqli_stmt_execute($stmt)) {
  set_flash('success', 'Data berhasil diperbarui.');
  header('Location: ' . BASE_URL . '/kelola_admin/bacaadmin.php');
  exit;
} else {
  // jika perubahan gagal
  $errors[] = (mysqli_stmt_errno($stmt) == 1062) ? 'Username atau email ini sudah dipakai akun lain.' : 'Terjadi kesalahan pada database. Periksa kembali isian Anda.';
  $old = $_POST;
  unset($old['csrf'], $old['password']);   // kata sandi tidak dikembalikan ke form
  $_SESSION['form_admin_edit_errors'] = $errors;
  $_SESSION['form_admin_edit_old'] = $old;
  header('Location: ' . BASE_URL . '/kelola_admin/form_editadmin.php?id=' . $id);
  exit;
}
?>