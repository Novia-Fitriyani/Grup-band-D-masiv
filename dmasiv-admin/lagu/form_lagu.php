<?php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/koneksi_mysqli.php";

$id_album = (int)($_GET['album'] ?? 0);
$stmt = mysqli_prepare($conn, "SELECT judul_album FROM album WHERE id_album = ?");
mysqli_stmt_bind_param($stmt, "i", $id_album);
mysqli_stmt_execute($stmt);
$induk = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);
if (!$induk) {
  set_flash('danger', 'Album tidak ditemukan.');
  header('Location: ' . BASE_URL . '/album/bacaalbum.php');
  exit;
}

// nilai yang ditampilkan di form
$d = [];
$d['judul_lagu'] = '';
$d['no_urut'] = '';
$d['link_spotify'] = '';

$errors = $_SESSION['form_lagu_add_errors'] ?? [];
$old    = $_SESSION['form_lagu_add_old'] ?? [];
unset($_SESSION['form_lagu_add_errors'], $_SESSION['form_lagu_add_old']);
foreach ($old as $kunci => $nilai) {
  if (array_key_exists($kunci, $d)) { $d[$kunci] = $nilai; }
}


$judul = 'Form Tambah Lagu';
$menu  = 'album';
require __DIR__ . "/../includes/header.php";
?>
<div class="card card-primary card-outline">
  <div class="card-header">
    <h3 class="card-title mb-0">Form Tambah Lagu - Album: <?php echo e($induk['judul_album']); ?></h3>
  </div>
  <form action="simpanlagu.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
    <input type="hidden" name="id_album" value="<?php echo $id_album; ?>">
    <div class="card-body">
      <?php if ($errors) { ?>
        <div class="alert alert-danger"><ul class="mb-0">
          <?php foreach ($errors as $pesan) { ?><li><?php echo e($pesan); ?></li><?php } ?>
        </ul></div>
      <?php } ?>

      <div class="mb-3">
        <label class="form-label" for="f_judul_lagu">Judul lagu *</label>
        <input type="text" class="form-control" id="f_judul_lagu" name="judul_lagu" maxlength="150" value="<?php echo e($d['judul_lagu']); ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label" for="f_no_urut">Nomor urut dalam album (opsional)</label>
        <input type="number" class="form-control" id="f_no_urut" name="no_urut" min="0" value="<?php echo e($d['no_urut']); ?>">
      </div>
      <div class="mb-3">
        <label class="form-label" for="f_link_spotify">Tautan lagu di Spotify (opsional)</label>
        <input type="url" class="form-control" id="f_link_spotify" name="link_spotify" value="<?php echo e($d['link_spotify']); ?>">
      </div>
    </div>
    <div class="card-footer">
      <input type="reset" value="Cancel" class="btn btn-secondary">
      <input type="submit" value="Simpan" class="btn btn-primary">
      <a href="<?php echo e(BASE_URL . '/lagu/bacalagu.php?album=' . $id_album); ?>" class="btn btn-outline-secondary">Kembali</a>
    </div>
  </form>
</div>
<?php require __DIR__ . "/../includes/footer.php"; ?>
