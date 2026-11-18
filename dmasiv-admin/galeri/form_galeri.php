<?php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/koneksi_mysqli.php";

// nilai yang ditampilkan di form
$d = [];
$d['tipe'] = '';
$d['judul'] = '';
$d['tanggal'] = '';
$d['url_video'] = '';
$d['status_aktif'] = 'arsip';

$errors = $_SESSION['form_galeri_add_errors'] ?? [];
$old    = $_SESSION['form_galeri_add_old'] ?? [];
unset($_SESSION['form_galeri_add_errors'], $_SESSION['form_galeri_add_old']);
foreach ($old as $kunci => $nilai) {
  if (array_key_exists($kunci, $d)) { $d[$kunci] = $nilai; }
}

$opsi_tipe = ['foto' => 'Foto', 'video' => 'Video'];
$opsi_status_aktif = ['arsip' => 'Arsip (tidak ditampilkan)', 'tampil' => 'Tampil di Latest Video footer'];

$judul = 'Form Tambah Galeri';
$menu  = 'galeri';
require __DIR__ . "/../includes/header.php";
?>
<div class="card card-primary card-outline">
  <div class="card-header">
    <h3 class="card-title mb-0">Form Tambah Galeri</h3>
  </div>
  <form action="simpangaleri.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
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
        <input type="file" class="form-control" id="f_file_thumbnail" name="file_thumbnail" accept="image/jpeg,image/png,image/gif" required>
        <div class="form-text">JPG, JPEG, PNG, atau GIF, maksimal 1 MB.</div>
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
