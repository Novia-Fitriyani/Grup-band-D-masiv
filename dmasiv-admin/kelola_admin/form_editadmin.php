<?php
//baca data 1 record yang dipilih untuk diedit
// yang diambil dari tampilan bacaadmin.php pada posisi tombol edit diklik
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/koneksi_mysqli.php";

$id = (int)($_GET['id'] ?? 0);
$stmt = mysqli_prepare($conn, "SELECT * FROM admin WHERE id_admin = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
if (!$row) {
  set_flash('danger', 'Data tidak ditemukan.');
  header('Location: ' . BASE_URL . '/kelola_admin/bacaadmin.php');
  exit;
}

// nilai yang ditampilkan di form
$d = [];
$d['username'] = $row['username'] ?? '';
$d['nama_lengkap'] = $row['nama_lengkap'] ?? '';
$d['email'] = $row['email'] ?? '';
$d['password'] = '';   // kata sandi tidak pernah ditampilkan ulang
$d['status'] = $row['status'] ?? '';

$errors = $_SESSION['form_admin_edit_errors'] ?? [];
$old    = $_SESSION['form_admin_edit_old'] ?? [];
unset($_SESSION['form_admin_edit_errors'], $_SESSION['form_admin_edit_old']);
foreach ($old as $kunci => $nilai) {
  if (array_key_exists($kunci, $d)) { $d[$kunci] = $nilai; }
}

$opsi_status = ['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif (tidak bisa login)'];

$judul = 'Form Edit Admin';
$menu  = 'admin';
require __DIR__ . "/../includes/header.php";
?>
<div class="card card-primary card-outline">
  <div class="card-header">
    <h3 class="card-title mb-0">Form Edit Admin</h3>
  </div>
  <form action="updateadmin.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
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
        <input type="password" class="form-control" id="f_password" name="password" autocomplete="new-password" value="">
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
