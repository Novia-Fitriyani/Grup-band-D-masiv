<?php
//baca data 1 record yang dipilih untuk diedit
// yang diambil dari tampilan bacaalbum.php pada posisi tombol edit diklik
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/koneksi_mysqli.php";

$id = (int)($_GET['id'] ?? 0);
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

// nilai yang ditampilkan di form
$d = [];
$d['judul_album'] = $row['judul_album'] ?? '';
$d['tanggal_rilis'] = $row['tanggal_rilis'] ?? '';
$d['link_spotify'] = $row['link_spotify'] ?? '';

$errors = $_SESSION['form_album_edit_errors'] ?? [];
$old    = $_SESSION['form_album_edit_old'] ?? [];
unset($_SESSION['form_album_edit_errors'], $_SESSION['form_album_edit_old']);
foreach ($old as $kunci => $nilai) {
  if (array_key_exists($kunci, $d)) { $d[$kunci] = $nilai; }
}


$judul = 'Form Edit Album';
$menu  = 'album';
require __DIR__ . "/../includes/header.php";
?>
<div class="card card-primary card-outline">
  <div class="card-header">
    <h3 class="card-title mb-0">Form Edit Album</h3>
  </div>
  <form action="updatealbum.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
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
        <?php if (!empty($row['cover'])) { ?>
          <div class="mb-2"><img src="<?php echo e(BASE_URL . '/' . $row['cover']); ?>" alt="" class="rounded" style="max-height:120px"></div>
        <?php } ?>
        <input type="file" class="form-control" id="f_cover" name="cover" accept="image/jpeg,image/png,image/gif">
        <div class="form-text">JPG, JPEG, PNG, atau GIF, maksimal 1 MB. Kosongkan jika tidak ingin mengganti gambar.</div>
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
