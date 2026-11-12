<?php
//baca data 1 record yang dipilih untuk diedit
// yang diambil dari tampilan bacagaleri.php pada posisi tombol edit diklik
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/koneksi_mysqli.php";

$id = (int)($_GET['id'] ?? 0);
$stmt = mysqli_prepare($conn, "SELECT * FROM galeri WHERE id_galeri = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
if (!$row) {
  set_flash('danger', 'Data tidak ditemukan.');
  header('Location: ' . BASE_URL . '/galeri/bacagaleri.php');
  exit;
}

// nilai yang ditampilkan di form
$d = [];
$d['tipe'] = $row['tipe'] ?? '';
$d['judul'] = $row['judul'] ?? '';
$d['tanggal'] = $row['tanggal'] ?? '';
$d['url_video'] = $row['url_video'] ?? '';
$d['status_aktif'] = $row['status_aktif'] ?? '';

$errors = $_SESSION['form_galeri_edit_errors'] ?? [];
$old    = $_SESSION['form_galeri_edit_old'] ?? [];
unset($_SESSION['form_galeri_edit_errors'], $_SESSION['form_galeri_edit_old']);
foreach ($old as $kunci => $nilai) {
  if (array_key_exists($kunci, $d)) { $d[$kunci] = $nilai; }
}

$opsi_tipe = ['foto' => 'Foto', 'video' => 'Video'];
$opsi_status_aktif = ['arsip' => 'Arsip (tidak ditampilkan)', 'tampil' => 'Tampil di Latest Video footer'];

$judul = 'Form Edit Galeri';
$menu  = 'galeri';
require __DIR__ . "/../includes/header.php";
?>
<div class="card card-primary card-outline">
  <div class="card-header">
    <h3 class="card-title mb-0">Form Edit Galeri</h3>
  </div>
  <form action="updategaleri.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
    <div class="card-body">
      <?php if ($errors) { ?>
        <div class="alert alert-danger"><ul class="mb-0">
          <?php foreach ($errors as $pesan) { ?><li><?php echo e($pesan); ?></li><?php } ?>
        </ul></div>
      <?php } ?>

      <div class="mb-3">
        <label class="form-label" for="f_tipe">Jenis *</label>
        <select class="form-select" id="f_tipe" name="tipe" required>
          <option value="">- Pilih -</option>
          <?php foreach ($opsi_tipe as $ov => $ol) { ?>
            <option value="<?php echo e($ov); ?>" <?php echo ((string)$d['tipe'] === (string)$ov) ? 'selected' : ''; ?>><?php echo e($ol); ?></option>
          <?php } ?>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label" for="f_judul">Judul dokumentasi *</label>
        <input type="text" class="form-control" id="f_judul" name="judul" maxlength="150" value="<?php echo e($d['judul']); ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label" for="f_tanggal">Tanggal kegiatan *</label>
        <input type="date" class="form-control" id="f_tanggal" name="tanggal" value="<?php echo e($d['tanggal']); ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label" for="f_file_thumbnail">Gambar / thumbnail *</label>
        <?php if (!empty($row['file_thumbnail'])) { ?>
          <div class="mb-2"><img src="<?php echo e(BASE_URL . '/' . $row['file_thumbnail']); ?>" alt="" class="rounded" style="max-height:120px"></div>
        <?php } ?>
        <input type="file" class="form-control" id="f_file_thumbnail" name="file_thumbnail" accept="image/jpeg,image/png,image/gif">
        <div class="form-text">JPG, JPEG, PNG, atau GIF, maksimal 1 MB. Kosongkan jika tidak ingin mengganti gambar.</div>
      </div>
      <div class="mb-3">
        <label class="form-label" for="f_url_video">Tautan YouTube (khusus video)</label>
        <input type="url" class="form-control" id="f_url_video" name="url_video" value="<?php echo e($d['url_video']); ?>">
        <div class="form-text">Wajib untuk jenis Video, kosongkan untuk Foto</div>
      </div>
      <div class="mb-3">
        <label class="form-label" for="f_status_aktif">Status tampil *</label>
        <select class="form-select" id="f_status_aktif" name="status_aktif" required>
          <option value="">- Pilih -</option>
          <?php foreach ($opsi_status_aktif as $ov => $ol) { ?>
            <option value="<?php echo e($ov); ?>" <?php echo ((string)$d['status_aktif'] === (string)$ov) ? 'selected' : ''; ?>><?php echo e($ol); ?></option>
          <?php } ?>
        </select>
      </div>
    </div>
    <div class="card-footer">
      <input type="reset" value="Cancel" class="btn btn-secondary">
      <input type="submit" value="Simpan" class="btn btn-primary">
      <a href="<?php echo e(BASE_URL . '/galeri/bacagaleri.php'); ?>" class="btn btn-outline-secondary">Kembali</a>
    </div>
  </form>
</div>
<?php require __DIR__ . "/../includes/footer.php"; ?>
