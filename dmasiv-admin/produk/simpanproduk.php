<?php
require_once __DIR__ . "/../includes/crud.php";
require_once __DIR__ . "/../config/koneksi_mysqli.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ' . BASE_URL . '/produk/bacaproduk.php');
  exit;
}
csrf_check();

// data dari form_produk.php
$nama_produk = trim($_POST['nama_produk'] ?? '');
$deskripsi = trim($_POST['deskripsi'] ?? '');
$harga = trim($_POST['harga'] ?? '');
$link_beli = trim($_POST['link_beli'] ?? '');
$status = trim($_POST['status'] ?? '');

$errors = [];
if ($nama_produk === '') { $errors[] = 'Nama produk wajib diisi.'; }
elseif (mb_strlen($nama_produk) > 100) { $errors[] = 'Nama produk maksimal 100 karakter.'; }
if ($deskripsi !== '' && (mb_strlen($deskripsi) > 65535)) { $errors[] = 'Deskripsi singkat maksimal 65535 karakter.'; }
if ($harga === '') { $errors[] = 'Harga wajib diisi.'; }
elseif (!preg_match('/^\d{1,9}$/', $harga)) { $errors[] = 'Harga harus berupa angka bulat (0 atau lebih).'; }
if ($link_beli === '') { $errors[] = 'Tautan tombol Buy Now wajib diisi.'; }
elseif (!filter_var($link_beli, FILTER_VALIDATE_URL) || !preg_match('#^https?://#i', $link_beli) || mb_strlen($link_beli) > 255) { $errors[] = 'Tautan tombol Buy Now harus berupa alamat lengkap berawalan http:// atau https://.'; }
if ($status === '') { $errors[] = 'Ketersediaan wajib dipilih.'; }
elseif (!in_array($status, ['tersedia', 'habis'], true)) { $errors[] = 'Ketersediaan tidak valid.'; }

// inisialisasi status upload
$uploadOk = 1;
$gambar = null;   // path yang nanti disalin ke database

// Definisikan direktori target/ tujuan
$target_dir = "gambar/produk/";
$adaFile = isset($_FILES["gambar"]) && $_FILES["gambar"]["error"] !== UPLOAD_ERR_NO_FILE;

if ($adaFile) {
  // menerima data dari upload
  $nameFile = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($_FILES["gambar"]["name"]));
  $ukuranFile = $_FILES["gambar"]["size"];
  $fileSementara = $_FILES["gambar"]["tmp_name"];
  $statusError = $_FILES["gambar"]["error"];

  $file_path = $target_dir . $nameFile;           // alamat yang disimpan di database
  $file_fisik = __DIR__ . "/../" . $file_path;
  // mengubah ekstensi file menjadi huruf kecil
  $imageFileType = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));

  if ($statusError !== UPLOAD_ERR_OK) {
    $errors[] = 'Foto produk: unggahan gagal (kode ' . $statusError . ').';
    $uploadOk = 0;
  } else {
    // Checkpoint 1 (Autentikasi)
    // Memeriksa apakah file yang diunggah 'real image' atau 'fake image'
    $cek = @getimagesize($fileSementara);
    if ($cek === false) {
      $errors[] = 'Foto produk: file BUKAN gambar asli.';
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
  $errors[] = 'Foto produk wajib diunggah.';
  $uploadOk = 0;
}

// jika ada isian yang salah, kembali ke form (file baru dipindahkan hanya jika semua isian lolos)
if ($errors) {
  $old = $_POST;
  unset($old['csrf']);
  $_SESSION['form_produk_add_errors'] = $errors;
  $_SESSION['form_produk_add_old'] = $old;
  header('Location: ' . BASE_URL . '/produk/form_produk.php');
  exit;
}

// Jika status uploadOk = 1 pindahkan file ke direktori tujuan/ target
if ($adaFile && $uploadOk == 1) {
  if (!is_dir(dirname($file_fisik))) { mkdir(dirname($file_fisik), 0755, true); }
  // pindahkan fisik file ke direktori tujuan/ target
  if (move_uploaded_file($fileSementara, $file_fisik)) {
    $gambar = $file_path;   // alamat file (path name) inilah yang disalin ke database
  } else {
    $errors[] = 'File gagal dipindahkan ke folder tujuan. Periksa izin folder gambar/.';
    $old = $_POST;
    unset($old['csrf']);
    $_SESSION['form_produk_add_errors'] = $errors;
    $_SESSION['form_produk_add_old'] = $old;
    header('Location: ' . BASE_URL . '/produk/form_produk.php');
    exit;
  }
}

// kolom opsional yang kosong disimpan sebagai NULL
$deskripsi = ($deskripsi === '') ? null : $deskripsi;
$harga = (int)$harga;
$id_admin = (int)$_SESSION['id_admin'];

// SQL to insert a record
$sql = "INSERT INTO produk (nama_produk, deskripsi, harga, gambar, link_beli, status, id_admin) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ssisssi", $nama_produk, $deskripsi, $harga, $gambar, $link_beli, $status, $id_admin);

if (mysqli_stmt_execute($stmt)) {
  set_flash('success', 'Data berhasil ditambahkan.');
  header('Location: ' . BASE_URL . '/produk/bacaproduk.php');
  exit;
} else {
  // jika penyimpanan gagal
  $errors[] = 'Terjadi kesalahan pada database. Periksa kembali isian Anda.';
  crud_hapus_file($gambar);
  $old = $_POST;
  unset($old['csrf']);
  $_SESSION['form_produk_add_errors'] = $errors;
  $_SESSION['form_produk_add_old'] = $old;
  header('Location: ' . BASE_URL . '/produk/form_produk.php');
  exit;
}
?>