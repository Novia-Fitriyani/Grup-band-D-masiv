<?php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/koneksi_mysqli.php";

// nilai yang ditampilkan di form
$d = [];
$d['id_kategori'] = '';
$d['judul'] = '';
$d['lokasi'] = '';
$d['tanggal_event'] = '';
$d['waktu_mulai'] = '';
$d['deskripsi'] = '';

$errors = $_SESSION['form_event_add_errors'] ?? [];
$old    = $_SESSION['form_event_add_old'] ?? [];
unset($_SESSION['form_event_add_errors'], $_SESSION['form_event_add_old']);
foreach ($old as $kunci => $nilai) {
  if (array_key_exists($kunci, $d)) { $d[$kunci] = $nilai; }
}

$daftar_id_kategori = mysqli_query($conn, "SELECT id_kategori, nama_kategori FROM kategori_event ORDER BY nama_kategori");

$judul = 'Form Tambah Event';
$menu  = 'event';
require __DIR__ . "/../includes/header.php";
?>
<div class="card card-primary card-outline">
  <div class="card-header">
    <h3 class="card-title mb-0">Form Tambah Event</h3>
  </div>
  <form action="simpanevent.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
    <div class="card-body">
      <?php if ($errors) { ?>
        <div class="alert alert-danger"><ul class="mb-0">
          <?php foreach ($errors as $pesan) { ?><li><?php echo e($pesan); ?></li><?php } ?>
        </ul></div>
      <?php } ?>

      <div class="mb-3">
        <label class="form-label" for="f_id_kategori">Kategori *</label>
        <select class="form-select" id="f_id_kategori" name="id_kategori" required>
          <option value="">- Pilih kategori -</option>
          <?php while ($k = mysqli_fetch_assoc($daftar_id_kategori)) { ?>
            <option value="<?php echo (int)$k['id_kategori']; ?>" <?php echo ((int)$d['id_kategori'] === (int)$k['id_kategori']) ? 'selected' : ''; ?>><?php echo e($k['nama_kategori']); ?></option>
          <?php } ?>
        </select>
        <?php if (mysqli_num_rows($daftar_id_kategori) == 0) { ?>
          <div class="form-text text-danger">Belum ada data kategori. <a href="<?php echo e(BASE_URL . '/kategori_event/form_kategorievent.php'); ?>">Tambah kategori event dulu</a></div>
        <?php } ?>
      </div>
      <div class="mb-3">
        <label class="form-label" for="f_judul">Judul event *</label>
        <input type="text" class="form-control" id="f_judul" name="judul" maxlength="150" value="<?php echo e($d['judul']); ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label" for="f_lokasi">Lokasi *</label>
        <input type="text" class="form-control" id="f_lokasi" name="lokasi" maxlength="100" value="<?php echo e($d['lokasi']); ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label" for="f_tanggal_event">Tanggal event *</label>
        <input type="date" class="form-control" id="f_tanggal_event" name="tanggal_event" value="<?php echo e($d['tanggal_event']); ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label" for="f_waktu_mulai">Jam mulai (opsional)</label>
        <input type="time" class="form-control" id="f_waktu_mulai" name="waktu_mulai" value="<?php echo e($d['waktu_mulai']); ?>">
      </div>
      <div class="mb-3">
        <label class="form-label" for="f_deskripsi">Deskripsi (opsional)</label>
        <textarea class="form-control" id="f_deskripsi" name="deskripsi" rows="4"><?php echo e($d['deskripsi']); ?></textarea>
      </div>
    </div>
    <div class="card-footer">
      <input type="reset" value="Cancel" class="btn btn-secondary">
      <input type="submit" value="Simpan" class="btn btn-primary">
      <a href="<?php echo e(BASE_URL . '/event/bacaevent.php'); ?>" class="btn btn-outline-secondary">Kembali</a>
    </div>
  </form>
</div>
<?php require __DIR__ . "/../includes/footer.php"; ?>
