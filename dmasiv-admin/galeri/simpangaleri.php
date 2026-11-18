<?php
require_once __DIR__ . "/../includes/crud.php";
require_once __DIR__ . "/../config/koneksi_mysqli.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ' . BASE_URL . '/galeri/bacagaleri.php');
  exit;
}
csrf_check();

// data dari form_galeri.php
$tipe = trim($_POST['tipe'] ?? '');
$judul = trim($_POST['judul'] ?? '');
$tanggal = trim($_POST['tanggal'] ?? '');
$url_video = trim($_POST['url_video'] ?? '');
$status_aktif = trim($_POST['status_aktif'] ?? '');

$errors = [];
if ($tipe === '') { $errors[] = 'Jenis wajib dipilih.'; }
elseif (!in_array($tipe, ['foto', 'video'], true)) { $errors[] = 'Jenis tidak valid.'; }
if ($judul === '') { $errors[] = 'Judul dokumentasi wajib diisi.'; }
elseif (mb_strlen($judul) > 150) { $errors[] = 'Judul dokumentasi maksimal 150 karakter.'; }
$tgl = DateTime::createFromFormat('Y-m-d', $tanggal);
if ($tanggal === '') { $errors[] = 'Tanggal kegiatan wajib diisi.'; }
elseif (!$tgl || $tgl->format('Y-m-d') !== $tanggal) { $errors[] = 'Tanggal kegiatan tidak valid.'; }
if ($url_video !== '' && (!filter_var($url_video, FILTER_VALIDATE_URL) || !preg_match('#^https?://#i', $url_video) || mb_strlen($url_video) > 255)) { $errors[] = 'Tautan YouTube harus berupa alamat lengkap berawalan http:// atau https://.'; }
if ($status_aktif === '') { $errors[] = 'Status tampil wajib dipilih.'; }
elseif (!in_array($status_aktif, ['arsip', 'tampil'], true)) { $errors[] = 'Status tampil tidak valid.'; }
if ($tipe === 'foto' && $url_video !== '') { $errors[] = 'Tautan YouTube hanya boleh diisi untuk jenis Video.'; }
if ($tipe === 'video' && $url_video === '') { $errors[] = 'Tautan YouTube wajib diisi untuk jenis Video.'; }
if ($tipe === 'foto' && $status_aktif === 'tampil') { $errors[] = 'Status "Tampil di Latest Video" hanya untuk jenis Video.'; }

// inisialisasi status upload
$uploadOk = 1;
$file_thumbnail = null;   // path yang nanti disalin ke database

// Definisikan direktori target/ tujuan
$target_dir = "gambar/galeri/";
$adaFile = isset($_FILES["file_thumbnail"]) && $_FILES["file_thumbnail"]["error"] !== UPLOAD_ERR_NO_FILE;

if ($adaFile) {
  // menerima data dari upload
  $nameFile = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($_FILES["file_thumbnail"]["name"]));
  $ukuranFile = $_FILES["file_thumbnail"]["size"];
  $fileSementara = $_FILES["file_thumbnail"]["tmp_name"];
  $statusError = $_FILES["file_thumbnail"]["error"];

  $file_path = $target_dir . $nameFile;           // alamat yang disimpan di database
  $file_fisik = __DIR__ . "/../" . $file_path;
  // mengubah ekstensi file menjadi huruf kecil
  $imageFileType = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));

  if ($statusError !== UPLOAD_ERR_OK) {
    $errors[] = 'Gambar / thumbnail: unggahan gagal (kode ' . $statusError . ').';
    $uploadOk = 0;
  } else {
    // Checkpoint 1 (Autentikasi)
    // Memeriksa apakah file yang diunggah 'real image' atau 'fake image'
    $cek = @getimagesize($fileSementara);
    if ($cek === false) {
      $errors[] = 'Gambar / thumbnail: file BUKAN gambar asli.';
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
} else {
  $errors[] = 'Gambar / thumbnail wajib diunggah.';
  $uploadOk = 0;
}

// jika ada isian yang salah, kembali ke form (file baru dipindahkan hanya jika semua isian lolos)
if ($errors) {
  $old = $_POST;
  unset($old['csrf']);
  $_SESSION['form_galeri_add_errors'] = $errors;
  $_SESSION['form_galeri_add_old'] = $old;
  header('Location: ' . BASE_URL . '/galeri/form_galeri.php');
  exit;
}

// Jika status uploadOk = 1 pindahkan file ke direktori tujuan/ target
if ($adaFile && $uploadOk == 1) {
  if (!is_dir(dirname($file_fisik))) { mkdir(dirname($file_fisik), 0755, true); }
  // pindahkan fisik file ke direktori tujuan/ target
  if (move_uploaded_file($fileSementara, $file_fisik)) {
    $file_thumbnail = $file_path;   // alamat file (path name) inilah yang disalin ke database
  } else {
    $errors[] = 'File gagal dipindahkan ke folder tujuan. Periksa izin folder gambar/.';
    $old = $_POST;
    unset($old['csrf']);
    $_SESSION['form_galeri_add_errors'] = $errors;
    $_SESSION['form_galeri_add_old'] = $old;
    header('Location: ' . BASE_URL . '/galeri/form_galeri.php');
    exit;
  }
}

// kolom opsional yang kosong disimpan sebagai NULL
$url_video = ($url_video === '') ? null : $url_video;
$id_admin = (int)$_SESSION['id_admin'];

// SQL to insert a record
$sql = "INSERT INTO galeri (tipe, judul, tanggal, file_thumbnail, url_video, status_aktif, id_admin) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ssssssi", $tipe, $judul, $tanggal, $file_thumbnail, $url_video, $status_aktif, $id_admin);

if (mysqli_stmt_execute($stmt)) {
  set_flash('success', 'Data berhasil ditambahkan.');
  header('Location: ' . BASE_URL . '/galeri/bacagaleri.php');
  exit;
} else {
  // jika penyimpanan gagal
  $errors[] = 'Terjadi kesalahan pada database. Periksa kembali isian Anda.';
  crud_hapus_file($file_thumbnail);
  $old = $_POST;
  unset($old['csrf']);
  $_SESSION['form_galeri_add_errors'] = $errors;
  $_SESSION['form_galeri_add_old'] = $old;
  header('Location: ' . BASE_URL . '/galeri/form_galeri.php');
  exit;
}
?>