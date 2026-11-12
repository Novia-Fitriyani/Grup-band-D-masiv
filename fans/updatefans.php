<?php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/koneksi_mysqli.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ' . BASE_URL . '/fans/bacafans.php');
  exit;
}
csrf_check();

// data dari form_editfans.php
$id = (int)($_POST['id'] ?? 0);
$nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
$jenis_kelamin = trim($_POST['jenis_kelamin'] ?? '');
$tanggal_lahir = trim($_POST['tanggal_lahir'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');
$email = trim($_POST['email'] ?? '');
$no_telepon = trim($_POST['no_telepon'] ?? '');
$url_sosmed = trim($_POST['url_sosmed'] ?? '');

$stmt = mysqli_prepare($conn, "SELECT * FROM fans WHERE id_fans = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
if (!$row) {
  set_flash('danger', 'Data tidak ditemukan.');
  header('Location: ' . BASE_URL . '/fans/bacafans.php');
  exit;
}

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

// jika ada isian yang salah, kembali ke form edit
if ($errors) {
  $old = $_POST;
  unset($old['csrf']);
  $_SESSION['form_fans_edit_errors'] = $errors;
  $_SESSION['form_fans_edit_old'] = $old;
  header('Location: ' . BASE_URL . '/fans/form_editfans.php?id=' . $id);
  exit;
}

// kolom opsional yang kosong disimpan sebagai NULL
$url_sosmed = ($url_sosmed === '') ? null : $url_sosmed;

// SQL to update a record
$set_kolom = ['nama_lengkap=?', 'jenis_kelamin=?', 'tanggal_lahir=?', 'alamat=?', 'email=?', 'no_telepon=?', 'url_sosmed=?'];
$bind_tipe  = "sssssss";
$bind_nilai = [$nama_lengkap, $jenis_kelamin, $tanggal_lahir, $alamat, $email, $no_telepon, $url_sosmed];
$bind_tipe .= "i"; $bind_nilai[] = $id;
$sql = "UPDATE fans SET " . implode(", ", $set_kolom) . " WHERE id_fans = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, $bind_tipe, ...$bind_nilai);

if (mysqli_stmt_execute($stmt)) {
  set_flash('success', 'Data berhasil diperbarui.');
  header('Location: ' . BASE_URL . '/fans/bacafans.php');
  exit;
} else {
  // jika perubahan gagal
  $errors[] = (mysqli_stmt_errno($stmt) == 1062) ? 'Email ini sudah terdaftar sebagai fans.' : 'Terjadi kesalahan pada database. Periksa kembali isian Anda.';
  $old = $_POST;
  unset($old['csrf']);
  $_SESSION['form_fans_edit_errors'] = $errors;
  $_SESSION['form_fans_edit_old'] = $old;
  header('Location: ' . BASE_URL . '/fans/form_editfans.php?id=' . $id);
  exit;
}
?>