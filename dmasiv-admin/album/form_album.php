<?php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/koneksi_mysqli.php";

// nilai yang ditampilkan di form
$d = [];
$d['judul_album'] = '';
$d['tanggal_rilis'] = '';
$d['link_spotify'] = '';

$errors = $_SESSION['form_album_add_errors'] ?? [];
$old    = $_SESSION['form_album_add_old'] ?? [];
unset($_SESSION['form_album_add_errors'], $_SESSION['form_album_add_old']);
foreach ($old as $kunci => $nilai) {
  if (array_key_exists($kunci, $d)) { $d[$kunci] = $nilai; }
}


$judul = 'Form Tambah Album';
$menu  = 'album';
require __DIR__ . "/../includes/header.php";
?>
<div class="card card-primary card-outline">
  <div class="card-header">
    <h3 class="card-title mb-0">Form Tambah Album</h3>
  </div>
  <form action="simpanalbum.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
    <div class="card-body">
      <?php if ($errors) { ?>
        <div class="alert alert-danger"><ul class="mb-0">
          <?php foreach ($errors as $pesan) { ?><li><?php echo e($pesan); ?></li><?php } ?>
        </ul></div>
      <?php } ?>

      <div class="mb-3">
        <label class="form-label" for="f_judul_album">Judul album/single *</label>
        <input type="text" class="form-control" id="f_judul_album" name="judul_album" maxlength="150" value="<?php echo e($d['judul_album']); ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label" for="f_tanggal_rilis">Tanggal rilis *</label>
        <input type="date" class="form-control" id="f_tanggal_rilis" name="tanggal_rilis" value="<?php echo e($d['tanggal_rilis']); ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label" for="f_cover">Cover *</label>
        <input type="file" class="form-control" id="f_cover" name="cover" accept="image/jpeg,image/png,image/gif" required>
        <div class="form-text">JPG, JPEG, PNG, atau GIF, maksimal 1 MB.</div>
      </div>
      <div class="mb-3">
        <label class="form-label" for="f_link_spotify">Tautan album di Spotify (opsional)</label>
        <input type="url" class="form-control" id="f_link_spotify" name="link_spotify" value="<?php echo e($d['link_spotify']); ?>">
      </div>
    </div>
    <div class="card-footer">
      <input type="reset" value="Cancel" class="btn btn-secondary">
      <input type="submit" value="Simpan" class="btn btn-primary">
      <a href="<?php echo e(BASE_URL . '/album/bacaalbum.php'); ?>" class="btn btn-outline-secondary">Kembali</a>
    </div>
  </form>
</div>
<?php require __DIR__ . "/../includes/footer.php"; ?>
