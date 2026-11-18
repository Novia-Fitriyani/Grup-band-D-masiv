<?php
require_once __DIR__ . "/../includes/crud.php";
require_once __DIR__ . "/../config/koneksi_mysqli.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ' . BASE_URL . '/album/bacaalbum.php');
  exit;
}
csrf_check();

// data dari form_editalbum.php
$id = (int)($_POST['id'] ?? 0);
$judul_album = trim($_POST['judul_album'] ?? '');
$tanggal_rilis = trim($_POST['tanggal_rilis'] ?? '');
$link_spotify = trim($_POST['link_spotify'] ?? '');

$stmt = mysqli_prepare($conn, "SELECT * FROM album WHERE id_album = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
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
if ($judul_album === '') { $errors[] = 'Judul album/single wajib diisi.'; }
elseif (mb_strlen($judul_album) > 150) { $errors[] = 'Judul album/single maksimal 150 karakter.'; }
$tgl = DateTime::createFromFormat('Y-m-d', $tanggal_rilis);
if ($tanggal_rilis === '') { $errors[] = 'Tanggal rilis wajib diisi.'; }
elseif (!$tgl || $tgl->format('Y-m-d') !== $tanggal_rilis) { $errors[] = 'Tanggal rilis tidak valid.'; }
if ($link_spotify !== '' && (!filter_var($link_spotify, FILTER_VALIDATE_URL) || !preg_match('#^https?://#i', $link_spotify) || mb_strlen($link_spotify) > 255)) { $errors[] = 'Tautan album di Spotify harus berupa alamat lengkap berawalan http:// atau https://.'; }

// inisialisasi status upload
$uploadOk = 1;
$cover = null;   // path yang nanti disalin ke database

// Definisikan direktori target/ tujuan
$target_dir = "gambar/album/";
$adaFile = isset($_FILES["cover"]) && $_FILES["cover"]["error"] !== UPLOAD_ERR_NO_FILE;

if ($adaFile) {
  // menerima data dari upload
  $nameFile = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($_FILES["cover"]["name"]));
  $ukuranFile = $_FILES["cover"]["size"];
  $fileSementara = $_FILES["cover"]["tmp_name"];
  $statusError = $_FILES["cover"]["error"];

  $file_path = $target_dir . $nameFile;           // alamat yang disimpan di database
  $file_fisik = __DIR__ . "/../" . $file_path;
  // mengubah ekstensi file menjadi huruf kecil
  $imageFileType = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));

  if ($statusError !== UPLOAD_ERR_OK) {
    $errors[] = 'Cover: unggahan gagal (kode ' . $statusError . ').';
    $uploadOk = 0;
  } else {
    // Checkpoint 1 (Autentikasi)
    // Memeriksa apakah file yang diunggah 'real image' atau 'fake image'
    $cek = @getimagesize($fileSementara);
    if ($cek === false) {
      $errors[] = 'Cover: file BUKAN gambar asli.';
      $uploadOk = 0;
    }

    // Checkpoint 2 (Duplikasi)
    // Memeriksa apakah nama file yang sama ada di direktori tujuan
    if (file_exists($file_fisik)) {
      $errors[] = 'Maaf, file ' . $nameFile . ' sudah ada. Ganti nama file Anda.';
      $uploadOk = 0;
    }

    // checkpoint 3 (pembatasan ukuran file)
    // memeriksa apakah ukuran file yang diunggah TIDAK MELEBIHI ukuran yang ditentukan (1 MB)
    if ($ukuranFile > 1000000) {
      $errors[] = 'Maaf, ukuran file melebihi 1 MB.';
      $uploadOk = 0;
    }

    // Checkpoint 4 (Format file)
    // memeriksa ekstensi file termasuk dalam 'whitelist' yang diijinkan
    if ($imageFileType != "jpg" && $imageFileType != "png" &&
        $imageFileType != "jpeg" && $imageFileType != "gif") {
      $errors[] = 'Maaf, hanya file JPG, JPEG, PNG & GIF yang diijinkan.';
      $uploadOk = 0;
    }
  }
}

// jika ada isian yang salah, kembali ke form edit
if ($errors) {
  $old = $_POST;
  unset($old['csrf']);
  $_SESSION['form_album_edit_errors'] = $errors;
  $_SESSION['form_album_edit_old'] = $old;
  header('Location: ' . BASE_URL . '/album/form_editalbum.php?id=' . $id);
  exit;
}

// Jika status uploadOk = 1 pindahkan file ke direktori tujuan/ target
if ($adaFile && $uploadOk == 1) {
  if (!is_dir(dirname($file_fisik))) { mkdir(dirname($file_fisik), 0755, true); }
  // pindahkan fisik file ke direktori tujuan/ target
  if (move_uploaded_file($fileSementara, $file_fisik)) {
    $cover = $file_path;   // alamat file (path name) inilah yang disalin ke database
  } else {
    $errors[] = 'File gagal dipindahkan ke folder tujuan. Periksa izin folder gambar/.';
    $old = $_POST;
    unset($old['csrf']);
    $_SESSION['form_album_edit_errors'] = $errors;
    $_SESSION['form_album_edit_old'] = $old;
    header('Location: ' . BASE_URL . '/album/form_editalbum.php?id=' . $id);
    exit;
  }
}

// kolom opsional yang kosong disimpan sebagai NULL
$link_spotify = ($link_spotify === '') ? null : $link_spotify;

// SQL to update a record
$set_kolom = ['judul_album=?', 'tanggal_rilis=?', 'link_spotify=?'];
$bind_tipe  = "sss";
$bind_nilai = [$judul_album, $tanggal_rilis, $link_spotify];
if ($cover) {
  $set_kolom[] = 'cover=?'; $bind_tipe .= 's'; $bind_nilai[] = $cover;
}
$bind_tipe .= "i"; $bind_nilai[] = $id;
$sql = "UPDATE album SET " . implode(", ", $set_kolom) . " WHERE id_album = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, $bind_tipe, ...$bind_nilai);

if (mysqli_stmt_execute($stmt)) {
  if ($cover) { crud_hapus_file($row['cover']); }
  set_flash('success', 'Data berhasil diperbarui.');
  header('Location: ' . BASE_URL . '/album/bacaalbum.php');
  exit;
} else {
  // jika perubahan gagal
  $errors[] = (mysqli_stmt_errno($stmt) == 1062) ? 'Ada nilai yang sudah dipakai data lain (harus unik).' : 'Terjadi kesalahan pada database. Periksa kembali isian Anda.';
  crud_hapus_file($cover);
  $old = $_POST;
  unset($old['csrf']);
  $_SESSION['form_album_edit_errors'] = $errors;
  $_SESSION['form_album_edit_old'] = $old;
  header('Location: ' . BASE_URL . '/album/form_editalbum.php?id=' . $id);
  exit;
}
?>