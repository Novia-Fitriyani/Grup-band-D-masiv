<?php
//koneksi ke db
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/koneksi_mysqli.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ' . BASE_URL . '/kelola_admin/bacaadmin.php');
  exit;
}
csrf_check();

$id = (int)($_POST['id'] ?? 0);

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
if ((int)$row['id_admin'] === (int)$_SESSION['id_admin']) {
  set_flash('danger', 'Anda tidak bisa menghapus akun yang sedang dipakai.');
  header('Location: ' . BASE_URL . '/kelola_admin/bacaadmin.php');
  exit;
}

// SQL to delete a record
$sql = "DELETE FROM admin WHERE id_admin = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
  set_flash('success', 'Data berhasil dihapus.');
} else {
//jika penghapusan gagal
  set_flash('danger', (mysqli_stmt_errno($stmt) == 1451) ? 'Admin ini sudah pernah menambahkan konten sehingga tidak bisa dihapus. Ubah statusnya menjadi Nonaktif.' : 'Terjadi kesalahan pada database. Periksa kembali isian Anda.');
}

header('Location: ' . BASE_URL . '/kelola_admin/bacaadmin.php');
exit;
?>