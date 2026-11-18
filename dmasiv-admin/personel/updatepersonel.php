<?php
require_once __DIR__ . "/../includes/crud.php";
require_once __DIR__ . "/../config/koneksi_mysqli.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ' . BASE_URL . '/personel/bacapersonel.php');
  exit;
}
csrf_check();

// data dari form_editpersonel.php
$id = (int)($_POST['id'] ?? 0);
$nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
$nama_panggilan = trim($_POST['nama_panggilan'] ?? '');
$peran = trim($_POST['peran'] ?? '');
$bio = trim($_POST['bio'] ?? '');
$urutan = trim($_POST['urutan'] ?? '');
$medsos_anggota = trim($_POST['medsos_anggota'] ?? '');

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

$errors = [];
if ($nama_lengkap === '') { $errors[] = 'Nama lengkap wajib diisi.'; }
elseif (mb_strlen($nama_lengkap) > 100) { $errors[] = 'Nama lengkap maksimal 100 karakter.'; }
if ($nama_panggilan === '') { $errors[] = 'Nama panggilan wajib diisi.'; }
elseif (mb_strlen($nama_panggilan) > 50) { $errors[] = 'Nama panggilan maksimal 50 karakter.'; }
if ($peran === '') { $errors[] = 'Posisi di band wajib diisi.'; }
elseif (mb_strlen($peran) > 30) { $errors[] = 'Posisi di band maksimal 30 karakter.'; }
if ($bio !== '' && (mb_strlen($bio) > 65535)) { $errors[] = 'Deskripsi singkat maksimal 65535 karakter.'; }
if ($urutan !== '' && (!preg_match('/^\d{1,9}$/', $urutan))) { $errors[] = 'Urutan tampil harus berupa angka bulat (0 atau lebih).'; }
if ($medsos_anggota === '') { $errors[] = 'Alamat akun media sosial anggota wajib diisi.'; }
elseif (!filter_var($medsos_anggota, FILTER_VALIDATE_URL) || !preg_match('#^https?://#i', $medsos_anggota) || mb_strlen($medsos_anggota) > 255) { $errors[] = 'Alamat akun media sosial anggota harus berupa alamat lengkap berawalan http:// atau https://.'; }

// inisialisasi status upload
$uploadOk = 1;
$foto = null;   // path yang nanti disalin ke database

// Definisikan direktori target/ tujuan
$target_dir = "gambar/personel/";
$adaFile = isset($_FILES["foto"]) && $_FILES["foto"]["error"] !== UPLOAD_ERR_NO_FILE;

if ($adaFile) {
  // menerima data dari upload
  $nameFile = preg_replace('/[^A-Za-z0-9._-]/', '_', basename($_FILES["foto"]["name"]));
  $ukuranFile = $_FILES["foto"]["size"];
  $fileSementara = $_FILES["foto"]["tmp_name"];
  $statusError = $_FILES["foto"]["error"];

  $file_path = $target_dir . $nameFile;           // alamat yang disimpan di database
  $file_fisik = __DIR__ . "/../" . $file_path;
  // mengubah ekstensi file menjadi huruf kecil
  $imageFileType = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));

  if ($statusError !== UPLOAD_ERR_OK) {
    $errors[] = 'Foto: unggahan gagal (kode ' . $statusError . ').';
    $uploadOk = 0;
  } else {
    // Checkpoint 1 (Autentikasi)
    // Memeriksa apakah file yang diunggah 'real image' atau 'fake image'
    $cek = @getimagesize($fileSementara);
    if ($cek === false) {
      $errors[] = 'Foto: file BUKAN gambar asli.';
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
  $_SESSION['form_personel_edit_errors'] = $errors;
  $_SESSION['form_personel_edit_old'] = $old;
  header('Location: ' . BASE_URL . '/personel/form_editpersonel.php?id=' . $id);
  exit;
}

// Jika status uploadOk = 1 pindahkan file ke direktori tujuan/ target
if ($adaFile && $uploadOk == 1) {
  if (!is_dir(dirname($file_fisik))) { mkdir(dirname($file_fisik), 0755, true); }
  // pindahkan fisik file ke direktori tujuan/ target
  if (move_uploaded_file($fileSementara, $file_fisik)) {
    $foto = $file_path;   // alamat file (path name) inilah yang disalin ke database
  } else {
    $errors[] = 'File gagal dipindahkan ke folder tujuan. Periksa izin folder gambar/.';
    $old = $_POST;
    unset($old['csrf']);
    $_SESSION['form_personel_edit_errors'] = $errors;
    $_SESSION['form_personel_edit_old'] = $old;
    header('Location: ' . BASE_URL . '/personel/form_editpersonel.php?id=' . $id);
    exit;
  }
}

// kolom opsional yang kosong disimpan sebagai NULL
$bio = ($bio === '') ? null : $bio;
$urutan = ($urutan === '') ? null : (int)$urutan;

// SQL to update a record
$set_kolom = ['nama_lengkap=?', 'nama_panggilan=?', 'peran=?', 'bio=?', 'urutan=?', 'medsos_anggota=?'];
$bind_tipe  = "ssssis";
$bind_nilai = [$nama_lengkap, $nama_panggilan, $peran, $bio, $urutan, $medsos_anggota];
if ($foto) {
  $set_kolom[] = 'foto=?'; $bind_tipe .= 's'; $bind_nilai[] = $foto;
}
$bind_tipe .= "i"; $bind_nilai[] = $id;
$sql = "UPDATE personel SET " . implode(", ", $set_kolom) . " WHERE id_personel = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, $bind_tipe, ...$bind_nilai);

if (mysqli_stmt_execute($stmt)) {
  if ($foto) { crud_hapus_file($row['foto']); }
  set_flash('success', 'Data berhasil diperbarui.');
  header('Location: ' . BASE_URL . '/personel/bacapersonel.php');
  exit;
} else {
  // jika perubahan gagal
  $errors[] = 'Terjadi kesalahan pada database. Periksa kembali isian Anda.';
  crud_hapus_file($foto);
  $old = $_POST;
  unset($old['csrf']);
  $_SESSION['form_personel_edit_errors'] = $errors;
  $_SESSION['form_personel_edit_old'] = $old;
  header('Location: ' . BASE_URL . '/personel/form_editpersonel.php?id=' . $id);
  exit;
}
?>