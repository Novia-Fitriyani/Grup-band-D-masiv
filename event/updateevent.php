<?php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/koneksi_mysqli.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ' . BASE_URL . '/event/bacaevent.php');
  exit;
}
csrf_check();

// data dari form_editevent.php
$id = (int)($_POST['id'] ?? 0);
$id_kategori = (int)($_POST['id_kategori'] ?? 0);
$judul = trim($_POST['judul'] ?? '');
$lokasi = trim($_POST['lokasi'] ?? '');
$tanggal_event = trim($_POST['tanggal_event'] ?? '');
$waktu_mulai = trim($_POST['waktu_mulai'] ?? '');
$deskripsi = trim($_POST['deskripsi'] ?? '');

$stmt = mysqli_prepare($conn, "SELECT * FROM event WHERE id_event = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
if (!$row) {
  set_flash('danger', 'Data tidak ditemukan.');
  header('Location: ' . BASE_URL . '/event/bacaevent.php');
  exit;
}

$errors = [];
$cek = mysqli_prepare($conn, "SELECT COUNT(*) FROM kategori_event WHERE id_kategori = ?");
mysqli_stmt_bind_param($cek, "i", $id_kategori);
mysqli_stmt_execute($cek);
mysqli_stmt_bind_result($cek, $ada);
mysqli_stmt_fetch($cek);
mysqli_stmt_close($cek);
if (!$ada) { $errors[] = 'Pilih kategori yang valid.'; }
if ($judul === '') { $errors[] = 'Judul event wajib diisi.'; }
elseif (mb_strlen($judul) > 150) { $errors[] = 'Judul event maksimal 150 karakter.'; }
if ($lokasi === '') { $errors[] = 'Lokasi wajib diisi.'; }
elseif (mb_strlen($lokasi) > 100) { $errors[] = 'Lokasi maksimal 100 karakter.'; }
$tgl = DateTime::createFromFormat('Y-m-d', $tanggal_event);
if ($tanggal_event === '') { $errors[] = 'Tanggal event wajib diisi.'; }
elseif (!$tgl || $tgl->format('Y-m-d') !== $tanggal_event) { $errors[] = 'Tanggal event tidak valid.'; }
if ($waktu_mulai !== '' && (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $waktu_mulai))) { $errors[] = 'Jam mulai tidak valid.'; }
if ($deskripsi !== '' && (mb_strlen($deskripsi) > 65535)) { $errors[] = 'Deskripsi maksimal 65535 karakter.'; }

// jika ada isian yang salah, kembali ke form edit
if ($errors) {
  $old = $_POST;
  unset($old['csrf']);
  $_SESSION['form_event_edit_errors'] = $errors;
  $_SESSION['form_event_edit_old'] = $old;
  header('Location: ' . BASE_URL . '/event/form_editevent.php?id=' . $id);
  exit;
}

// kolom opsional yang kosong disimpan sebagai NULL
$waktu_mulai = ($waktu_mulai === '') ? null : $waktu_mulai;
$deskripsi = ($deskripsi === '') ? null : $deskripsi;

// SQL to update a record
$set_kolom = ['id_kategori=?', 'judul=?', 'lokasi=?', 'tanggal_event=?', 'waktu_mulai=?', 'deskripsi=?'];
$bind_tipe  = "isssss";
$bind_nilai = [$id_kategori, $judul, $lokasi, $tanggal_event, $waktu_mulai, $deskripsi];
$bind_tipe .= "i"; $bind_nilai[] = $id;
$sql = "UPDATE event SET " . implode(", ", $set_kolom) . " WHERE id_event = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, $bind_tipe, ...$bind_nilai);

if (mysqli_stmt_execute($stmt)) {
  set_flash('success', 'Data berhasil diperbarui.');
  header('Location: ' . BASE_URL . '/event/bacaevent.php');
  exit;
} else {
  // jika perubahan gagal
  $errors[] = 'Terjadi kesalahan pada database. Periksa kembali isian Anda.';
  $old = $_POST;
  unset($old['csrf']);
  $_SESSION['form_event_edit_errors'] = $errors;
  $_SESSION['form_event_edit_old'] = $old;
  header('Location: ' . BASE_URL . '/event/form_editevent.php?id=' . $id);
  exit;
}
?>