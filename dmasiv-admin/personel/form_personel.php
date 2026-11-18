<?php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/koneksi_mysqli.php";

// nilai yang ditampilkan di form
$d = [];
$d['nama_lengkap'] = '';
$d['nama_panggilan'] = '';
$d['peran'] = '';
$d['bio'] = '';
$d['urutan'] = '';
$d['medsos_anggota'] = '';

$errors = $_SESSION['form_personel_add_errors'] ?? [];
$old    = $_SESSION['form_personel_add_old'] ?? [];
unset($_SESSION['form_personel_add_errors'], $_SESSION['form_personel_add_old']);
foreach ($old as $kunci => $nilai) {
  if (array_key_exists($kunci, $d)) { $d[$kunci] = $nilai; }
}


$judul = 'Form Tambah Personel';
$menu  = 'personel';
require __DIR__ . "/../includes/header.php";
?>
<div class="card card-primary card-outline">
  <div class="card-header">
    <h3 class="card-title mb-0">Form Tambah Personel</h3>
  </div>
  <form action="simpanpersonel.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
    <div class="card-body">
      <?php if ($errors) { ?>
        <div class="alert alert-danger"><ul class="mb-0">
          <?php foreach ($errors as $pesan) { ?><li><?php echo e($pesan); ?></li><?php } ?>
        </ul></div>
      <?php } ?>

      <div class="mb-3">
        <label class="form-label" for="f_nama_lengkap">Nama lengkap *</label>
        <input type="text" class="form-control" id="f_nama_lengkap" name="nama_lengkap" maxlength="100" value="<?php echo e($d['nama_lengkap']); ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label" for="f_nama_panggilan">Nama panggilan (tampil di kartu) *</label>
        <input type="text" class="form-control" id="f_nama_panggilan" name="nama_panggilan" maxlength="50" value="<?php echo e($d['nama_panggilan']); ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label" for="f_peran">Posisi di band *</label>
        <input type="text" class="form-control" id="f_peran" name="peran" maxlength="30" value="<?php echo e($d['peran']); ?>" required>
        <div class="form-text">Contoh: Vokal, Gitar, Bass, Drum</div>
      </div>
      <div class="mb-3">
        <label class="form-label" for="f_bio">Deskripsi singkat (opsional)</label>
        <textarea class="form-control" id="f_bio" name="bio" rows="4"><?php echo e($d['bio']); ?></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label" for="f_foto">Foto *</label>
        <input type="file" class="form-control" id="f_foto" name="foto" accept="image/jpeg,image/png,image/gif" required>
        <div class="form-text">JPG, JPEG, PNG, atau GIF, maksimal 1 MB.</div>
      </div>
      <div class="mb-3">
        <label class="form-label" for="f_urutan">Urutan tampil (opsional)</label>
        <input type="number" class="form-control" id="f_urutan" name="urutan" min="0" value="<?php echo e($d['urutan']); ?>">
        <div class="form-text">Angka kecil tampil lebih dulu</div>
      </div>
      <div class="mb-3">
        <label class="form-label" for="f_medsos_anggota">Alamat akun media sosial anggota *</label>
        <input type="url" class="form-control" id="f_medsos_anggota" name="medsos_anggota" value="<?php echo e($d['medsos_anggota']); ?>" required>
      </div>
    </div>
    <div class="card-footer">
      <input type="reset" value="Cancel" class="btn btn-secondary">
      <input type="submit" value="Simpan" class="btn btn-primary">
      <a href="<?php echo e(BASE_URL . '/personel/bacapersonel.php'); ?>" class="btn btn-outline-secondary">Kembali</a>
    </div>
  </form>
</div>
<?php require __DIR__ . "/../includes/footer.php"; ?>
