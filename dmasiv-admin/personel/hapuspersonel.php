<?php
//koneksi ke db
require_once __DIR__ . "/../includes/crud.php";
require_once __DIR__ . "/../config/koneksi_mysqli.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ' . BASE_URL . '/personel/bacapersonel.php');
  exit;
}
csrf_check();

$id = (int)($_POST['id'] ?? 0);

$stmt = mysqli_prepare($conn, "SELECT * FROM personel WHERE id_personel = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
if (!$row) {
  set_flash('danger', 'Data tidak ditemukan.');
  header('Location: ' . BASE_URL . '/personel/bacapersonel.php');
  exit;
}

// SQL to delete a record
$sql = "DELETE FROM personel WHERE id_personel = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
  crud_hapus_file($row['foto']);
  set_flash('success', 'Data berhasil dihapus.');
} else {
//jika penghapusan gagal
  set_flash('danger', 'Terjadi kesalahan pada database. Periksa kembali isian Anda.');
}

header('Location: ' . BASE_URL . '/personel/bacapersonel.php');
exit;
?>