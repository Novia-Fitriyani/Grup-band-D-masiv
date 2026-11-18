<?php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/koneksi_mysqli.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ' . BASE_URL . '/album/bacaalbum.php');
  exit;
}
csrf_check();

// data dari form_lagu.php
$id_album = (int)($_POST['id_album'] ?? 0);
$stmt = mysqli_prepare($conn, "SELECT COUNT(*) FROM album WHERE id_album = ?");
mysqli_stmt_bind_param($stmt, "i", $id_album);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $album_ada);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);
if (!$album_ada) {
  set_flash('danger', 'Album tidak ditemukan.');
  header('Location: ' . BASE_URL . '/album/bacaalbum.php');
  exit;
}
$judul_lagu = trim($_POST['judul_lagu'] ?? '');
$no_urut = trim($_POST['no_urut'] ?? '');
$link_spotify = trim($_POST['link_spotify'] ?? '');

$errors = [];
if ($judul_lagu === '') { $errors[] = 'Judul lagu wajib diisi.'; }
elseif (mb_strlen($judul_lagu) > 150) { $errors[] = 'Judul lagu maksimal 150 karakter.'; }
if ($no_urut !== '' && (!preg_match('/^\d{1,9}$/', $no_urut))) { $errors[] = 'Nomor urut dalam album harus berupa angka bulat (0 atau lebih).'; }
if ($link_spotify !== '' && (!filter_var($link_spotify, FILTER_VALIDATE_URL) || !preg_match('#^https?://#i', $link_spotify) || mb_strlen($link_spotify) > 255)) { $errors[] = 'Tautan lagu di Spotify harus berupa alamat lengkap berawalan http:// atau https://.'; }

// jika ada isian yang salah, kembali ke form (file baru dipindahkan hanya jika semua isian lolos)
if ($errors) {
  $old = $_POST;
  unset($old['csrf']);
  $_SESSION['form_lagu_add_errors'] = $errors;
  $_SESSION['form_lagu_add_old'] = $old;
  header('Location: ' . BASE_URL . '/lagu/form_lagu.php?album=' . $id_album);
  exit;
}

// kolom opsional yang kosong disimpan sebagai NULL
$no_urut = ($no_urut === '') ? null : (int)$no_urut;
$link_spotify = ($link_spotify === '') ? null : $link_spotify;
$id_admin = (int)$_SESSION['id_admin'];

// SQL to insert a record
$sql = "INSERT INTO lagu (judul_lagu, no_urut, link_spotify, id_album, id_admin) VALUES (?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "sisii", $judul_lagu, $no_urut, $link_spotify, $id_album, $id_admin);

if (mysqli_stmt_execute($stmt)) {
  set_flash('success', 'Data berhasil ditambahkan.');
  header('Location: ' . BASE_URL . '/lagu/bacalagu.php?album=' . $id_album);
  exit;
} else {
  // jika penyimpanan gagal
  $errors[] = (mysqli_stmt_errno($stmt) == 1062) ? 'Nomor urut ini sudah dipakai lagu lain di album yang sama.' : 'Terjadi kesalahan pada database. Periksa kembali isian Anda.';
  $old = $_POST;
  unset($old['csrf']);
  $_SESSION['form_lagu_add_errors'] = $errors;
  $_SESSION['form_lagu_add_old'] = $old;
  header('Location: ' . BASE_URL . '/lagu/form_lagu.php?album=' . $id_album);
  exit;
}
?>