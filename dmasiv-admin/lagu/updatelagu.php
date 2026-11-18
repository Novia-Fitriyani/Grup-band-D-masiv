<?php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/koneksi_mysqli.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ' . BASE_URL . '/album/bacaalbum.php');
  exit;
}
csrf_check();

// data dari form_editlagu.php
$id = (int)($_POST['id'] ?? 0);
$id_album = (int)($_POST['id_album'] ?? 0);
$judul_lagu = trim($_POST['judul_lagu'] ?? '');
$no_urut = trim($_POST['no_urut'] ?? '');
$link_spotify = trim($_POST['link_spotify'] ?? '');

$stmt = mysqli_prepare($conn, "SELECT * FROM lagu WHERE id_lagu = ? AND id_album = ?");
mysqli_stmt_bind_param($stmt, "ii", $id, $id_album);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
if (!$row) {
  set_flash('danger', 'Data tidak ditemukan.');
  header('Location: ' . BASE_URL . '/album/bacaalbum.php');
  exit;
}

$errors = [];
if ($judul_lagu === '') { $errors[] = 'Judul lagu wajib diisi.'; }
elseif (mb_strlen($judul_lagu) > 150) { $errors[] = 'Judul lagu maksimal 150 karakter.'; }
if ($no_urut !== '' && (!preg_match('/^\d{1,9}$/', $no_urut))) { $errors[] = 'Nomor urut dalam album harus berupa angka bulat (0 atau lebih).'; }
if ($link_spotify !== '' && (!filter_var($link_spotify, FILTER_VALIDATE_URL) || !preg_match('#^https?://#i', $link_spotify) || mb_strlen($link_spotify) > 255)) { $errors[] = 'Tautan lagu di Spotify harus berupa alamat lengkap berawalan http:// atau https://.'; }

// jika ada isian yang salah, kembali ke form edit
if ($errors) {
  $old = $_POST;
  unset($old['csrf']);
  $_SESSION['form_lagu_edit_errors'] = $errors;
  $_SESSION['form_lagu_edit_old'] = $old;
  header('Location: ' . BASE_URL . '/lagu/form_editlagu.php?id=' . $id . '&album=' . $id_album);
  exit;
}

// kolom opsional yang kosong disimpan sebagai NULL
$no_urut = ($no_urut === '') ? null : (int)$no_urut;
$link_spotify = ($link_spotify === '') ? null : $link_spotify;

// SQL to update a record
$set_kolom = ['judul_lagu=?', 'no_urut=?', 'link_spotify=?'];
$bind_tipe  = "sis";
$bind_nilai = [$judul_lagu, $no_urut, $link_spotify];
$bind_tipe .= "i"; $bind_nilai[] = $id;
$bind_tipe .= "i"; $bind_nilai[] = $id_album;
$sql = "UPDATE lagu SET " . implode(", ", $set_kolom) . " WHERE id_lagu = ? AND id_album = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, $bind_tipe, ...$bind_nilai);

if (mysqli_stmt_execute($stmt)) {
  set_flash('success', 'Data berhasil diperbarui.');
  header('Location: ' . BASE_URL . '/lagu/bacalagu.php?album=' . $id_album);
  exit;
} else {
  // jika perubahan gagal
  $errors[] = (mysqli_stmt_errno($stmt) == 1062) ? 'Nomor urut ini sudah dipakai lagu lain di album yang sama.' : 'Terjadi kesalahan pada database. Periksa kembali isian Anda.';
  $old = $_POST;
  unset($old['csrf']);
  $_SESSION['form_lagu_edit_errors'] = $errors;
  $_SESSION['form_lagu_edit_old'] = $old;
  header('Location: ' . BASE_URL . '/lagu/form_editlagu.php?id=' . $id . '&album=' . $id_album);
  exit;
}
?>