<?php
//baca data 1 record yang dipilih untuk diedit
// yang diambil dari tampilan bacamediasosial.php pada posisi tombol edit diklik
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/koneksi_mysqli.php";

$id = (int)($_GET['id'] ?? 0);
$stmt = mysqli_prepare($conn, "SELECT * FROM media_sosial WHERE id_sosmed = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
if (!$row) {
  set_flash('danger', 'Data tidak ditemukan.');
  header('Location: ' . BASE_URL . '/media_sosial/bacamediasosial.php');
  exit;
}

// nilai yang ditampilkan di form
$d = [];
$d['platform'] = $row['platform'] ?? '';
$d['url'] = $row['url'] ?? '';
$d['ikon'] = $row['ikon'] ?? '';
$d['nama_kontak'] = $row['nama_kontak'] ?? '';
$d['no_telepon'] = $row['no_telepon'] ?? '';

$errors = $_SESSION['form_mediasosial_edit_errors'] ?? [];
$old    = $_SESSION['form_mediasosial_edit_old'] ?? [];
unset($_SESSION['form_mediasosial_edit_errors'], $_SESSION['form_mediasosial_edit_old']);
foreach ($old as $kunci => $nilai) {
  if (array_key_exists($kunci, $d)) { $d[$kunci] = $nilai; }
}


$judul = 'Form Edit Media Sosial';
$menu  = 'sosmed';
require __DIR__ . "/../includes/header.php";
?>
<div class="card card-primary card-outline">
  <div class="card-header">
    <h3 class="card-title mb-0">Form Edit Media Sosial</h3>
  </div>
  <form action="updatemediasosial.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <div class="card-body">
      <?php if ($errors) { ?>
        <div class="alert alert-danger"><ul class="mb-0">
          <?php foreach ($errors as $pesan) { ?><li><?php echo e($pesan); ?></li><?php } ?>
        </ul></div>
      <?php } ?>

      <div class="mb-3">
        <label class="form-label" for="f_platform">Platform *</label>
        <input type="text" class="form-control" id="f_platform" name="platform" maxlength="30" value="<?php echo e($d['platform']); ?>" required>
        <div class="form-text">Contoh: Facebook, Twitter, Instagram, YouTube</div>
      </div>
      <div class="mb-3">
        <label class="form-label" for="f_url">Alamat akun *</label>
        <input type="url" class="form-control" id="f_url" name="url" value="<?php echo e($d['url']); ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label" for="f_ikon">Nama class ikon Font Awesome (opsional)</label>
        <input type="text" class="form-control" id="f_ikon" name="ikon" maxlength="50" value="<?php echo e($d['ikon']); ?>">
        <div class="form-text">Contoh: fa-brands fa-instagram</div>
      </div>
      <div class="mb-3">
        <label class="form-label" for="f_nama_kontak">Nama narahubung *</label>
        <input type="text" class="form-control" id="f_nama_kontak" name="nama_kontak" maxlength="50" value="<?php echo e($d['nama_kontak']); ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label" for="f_no_telepon">No. telepon narahubung *</label>
        <input type="text" class="form-control" id="f_no_telepon" name="no_telepon" maxlength="20" value="<?php echo e($d['no_telepon']); ?>" required>
      </div>
    </div>
    <div class="card-footer">
      <input type="reset" value="Cancel" class="btn btn-secondary">
      <input type="submit" value="Simpan" class="btn btn-primary">
      <a href="<?php echo e(BASE_URL . '/media_sosial/bacamediasosial.php'); ?>" class="btn btn-outline-secondary">Kembali</a>
    </div>
  </form>
</div>
<?php require __DIR__ . "/../includes/footer.php"; ?>
