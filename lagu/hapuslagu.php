<?php
//koneksi ke db
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/koneksi_mysqli.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ' . BASE_URL . '/album/bacaalbum.php');
  exit;
}
csrf_check();

$id = (int)($_POST['id'] ?? 0);
$id_album = (int)($_POST['id_album'] ?? 0);

$stmt = mysqli_prepare($conn, "SELECT * FROM lagu WHERE id_lagu = ? AND id_album = ?");
mysqli_stmt_bind_param($stmt, "ii", $id, $id_album);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
if (!$row) {
  set_flash('danger', 'Data tidak ditemukan.');
  header('Location: ' . BASE_URL . '/lagu/bacalagu.php?album=' . $id_album);
  exit;
}

// SQL to delete a record
$sql = "DELETE FROM lagu WHERE id_lagu = ? AND id_album = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $id, $id_album);

if (mysqli_stmt_execute($stmt)) {
  set_flash('success', 'Data berhasil dihapus.');
} else {
//jika penghapusan gagal
  set_flash('danger', 'Terjadi kesalahan pada database. Periksa kembali isian Anda.');
}

header('Location: ' . BASE_URL . '/lagu/bacalagu.php?album=' . $id_album);
exit;
?>