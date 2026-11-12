<?php
//baca data 1 record yang dipilih untuk diedit
// yang diambil dari tampilan bacakategorievent.php pada posisi tombol edit diklik
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/koneksi_mysqli.php";

$id = (int)($_GET['id'] ?? 0);
$stmt = mysqli_prepare($conn, "SELECT * FROM kategori_event WHERE id_kategori = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
if (!$row) {
  set_flash('danger', 'Data tidak ditemukan.');
  header('Location: ' . BASE_URL . '/kategori_event/bacakategorievent.php');
  exit;
}

// nilai yang ditampilkan di form
$d = [];
$d['nama_kategori'] = $row['nama_kategori'] ?? '';

$errors = $_SESSION['form_kategorievent_edit_errors'] ?? [];
$old    = $_SESSION['form_kategorievent_edit_old'] ?? [];
unset($_SESSION['form_kategorievent_edit_errors'], $_SESSION['form_kategorievent_edit_old']);
foreach ($old as $kunci => $nilai) {
  if (array_key_exists($kunci, $d)) { $d[$kunci] = $nilai; }
}


$judul = 'Form Edit Kategori Event';
$menu  = 'kategori';
require __DIR__ . "/../includes/header.php";
?>
<div class="card card-primary card-outline">
  <div class="card-header">
    <h3 class="card-title mb-0">Form Edit Kategori Event</h3>
  </div>
  <form action="updatekategorievent.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <div class="card-body">
      <?php if ($errors) { ?>
        <div class="alert alert-danger"><ul class="mb-0">
          <?php foreach ($errors as $pesan) { ?><li><?php echo e($pesan); ?></li><?php } ?>
        </ul></div>
      <?php } ?>

      <div class="mb-3">
        <label class="form-label" for="f_nama_kategori">Nama kategori *</label>
        <input type="text" class="form-control" id="f_nama_kategori" name="nama_kategori" maxlength="50" value="<?php echo e($d['nama_kategori']); ?>" required>
        <div class="form-text">Contoh: Release Promo, Concert, On Air Radio, Others</div>
      </div>
    </div>
    <div class="card-footer">
      <input type="reset" value="Cancel" class="btn btn-secondary">
      <input type="submit" value="Simpan" class="btn btn-primary">
      <a href="<?php echo e(BASE_URL . '/kategori_event/bacakategorievent.php'); ?>" class="btn btn-outline-secondary">Kembali</a>
    </div>
  </form>
</div>
<?php require __DIR__ . "/../includes/footer.php"; ?>
