<?php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/koneksi_mysqli.php";

// nilai yang ditampilkan di form
$d = [];
$d['username'] = '';
$d['nama_lengkap'] = '';
$d['email'] = '';
$d['password'] = '';   // kata sandi tidak pernah ditampilkan ulang
$d['status'] = 'aktif';

$errors = $_SESSION['form_admin_add_errors'] ?? [];
$old    = $_SESSION['form_admin_add_old'] ?? [];
unset($_SESSION['form_admin_add_errors'], $_SESSION['form_admin_add_old']);
foreach ($old as $kunci => $nilai) {
  if (array_key_exists($kunci, $d)) { $d[$kunci] = $nilai; }
}

$opsi_status = ['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif (tidak bisa login)'];

$judul = 'Form Tambah Admin';
$menu  = 'admin';
require __DIR__ . "/../includes/header.php";
?>
<div class="card card-primary card-outline">
  <div class="card-header">
    <h3 class="card-title mb-0">Form Tambah Admin</h3>
  </div>
  <form action="simpanadmin.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
    <div class="card-body">
      <?php if ($errors) { ?>
        <div class="alert alert-danger"><ul class="mb-0">
          <?php foreach ($errors as $pesan) { ?><li><?php echo e($pesan); ?></li><?php } ?>
        </ul></div>
      <?php } ?>

      <div class="mb-3">
        <label class="form-label" for="f_username">Username *</label>
        <input type="text" class="form-control" id="f_username" name="username" maxlength="50" value="<?php echo e($d['username']); ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label" for="f_nama_lengkap">Nama lengkap *</label>
        <input type="text" class="form-control" id="f_nama_lengkap" name="nama_lengkap" maxlength="100" value="<?php echo e($d['nama_lengkap']); ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label" for="f_email">Email *</label>
        <input type="email" class="form-control" id="f_email" name="email" value="<?php echo e($d['email']); ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label" for="f_password">Kata sandi *</label>
        <input type="password" class="form-control" id="f_password" name="password" autocomplete="new-password" value="" required>
        <div class="form-text">Minimal 8 karakter. Saat mengubah data, kosongkan jika tidak ingin mengganti.</div>
      </div>
      <div class="mb-3">
        <label class="form-label" for="f_status">Status akun *</label>
        <select class="form-select" id="f_status" name="status" required>
          <option value="">- Pilih -</option>
          <?php foreach ($opsi_status as $ov => $ol) { ?>
            <option value="<?php echo e($ov); ?>" <?php echo ((string)$d['status'] === (string)$ov) ? 'selected' : ''; ?>><?php echo e($ol); ?></option>
          <?php } ?>
        </select>
      </div>
    </div>
    <div class="card-footer">
      <input type="reset" value="Cancel" class="btn btn-secondary">
      <input type="submit" value="Simpan" class="btn btn-primary">
      <a href="<?php echo e(BASE_URL . '/kelola_admin/bacaadmin.php'); ?>" class="btn btn-outline-secondary">Kembali</a>
    </div>
  </form>
</div>
<?php require __DIR__ . "/../includes/footer.php"; ?>
