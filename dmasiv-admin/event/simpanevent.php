<?php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/koneksi_mysqli.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ' . BASE_URL . '/event/bacaevent.php');
  exit;
}
csrf_check();

// data dari form_event.php
$id_kategori = (int)($_POST['id_kategori'] ?? 0);
$judul = trim($_POST['judul'] ?? '');
$lokasi = trim($_POST['lokasi'] ?? '');
$tanggal_event = trim($_POST['tanggal_event'] ?? '');
$waktu_mulai = trim($_POST['waktu_mulai'] ?? '');
$deskripsi = trim($_POST['deskripsi'] ?? '');

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

// jika ada isian yang salah, kembali ke form (file baru dipindahkan hanya jika semua isian lolos)
if ($errors) {
  $old = $_POST;
  unset($old['csrf']);
  $_SESSION['form_event_add_errors'] = $errors;
  $_SESSION['form_event_add_old'] = $old;
  header('Location: ' . BASE_URL . '/event/form_event.php');
  exit;
}

// kolom opsional yang kosong disimpan sebagai NULL
$waktu_mulai = ($waktu_mulai === '') ? null : $waktu_mulai;
$deskripsi = ($deskripsi === '') ? null : $deskripsi;
$id_admin = (int)$_SESSION['id_admin'];

// SQL to insert a record
$sql = "INSERT INTO event (id_kategori, judul, lokasi, tanggal_event, waktu_mulai, deskripsi, id_admin) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "isssssi", $id_kategori, $judul, $lokasi, $tanggal_event, $waktu_mulai, $deskripsi, $id_admin);

if (mysqli_stmt_execute($stmt)) {
  set_flash('success', 'Data berhasil ditambahkan.');
  header('Location: ' . BASE_URL . '/event/bacaevent.php');
  exit;
} else {
  // jika penyimpanan gagal
  $errors[] = 'Terjadi kesalahan pada database. Periksa kembali isian Anda.';
  $old = $_POST;
  unset($old['csrf']);
  $_SESSION['form_event_add_errors'] = $errors;
  $_SESSION['form_event_add_old'] = $old;
  header('Location: ' . BASE_URL . '/event/form_event.php');
  exit;
}
?>