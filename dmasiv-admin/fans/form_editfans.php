<?php
//baca data 1 record yang dipilih untuk diedit
// yang diambil dari tampilan bacafans.php pada posisi tombol edit diklik
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/koneksi_mysqli.php";

$id = (int)($_GET['id'] ?? 0);
$stmt = mysqli_prepare($conn, "SELECT * FROM fans WHERE id_fans = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
if (!$row) {
  set_flash('danger', 'Data tidak ditemukan.');
  header('Location: ' . BASE_URL . '/fans/bacafans.php');
  exit;
}

// nilai yang ditampilkan di form
$d = [];
$d['nama_lengkap'] = $row['nama_lengkap'] ?? '';
$d['jenis_kelamin'] = $row['jenis_kelamin'] ?? '';
$d['tanggal_lahir'] = $row['tanggal_lahir'] ?? '';
$d['alamat'] = $row['alamat'] ?? '';
$d['email'] = $row['email'] ?? '';
$d['no_telepon'] = $row['no_telepon'] ?? '';
$d['url_sosmed'] = $row['url_sosmed'] ?? '';

$errors = $_SESSION['form_fans_edit_errors'] ?? [];
$old    = $_SESSION['form_fans_edit_old'] ?? [];
unset($_SESSION['form_fans_edit_errors'], $_SESSION['form_fans_edit_old']);
foreach ($old as $kunci => $nilai) {
  if (array_key_exists($kunci, $d)) { $d[$kunci] = $nilai; }
}

$opsi_jenis_kelamin = ['Pria' => 'Pria', 'Wanita' => 'Wanita'];

$judul = 'Form Edit Fans';
$menu  = 'fans';
require __DIR__ . "/../includes/header.php";
?>
<div class="card card-primary card-outline">
  <div class="card-header">
    <h3 class="card-title mb-0">Form Edit Fans</h3>
  </div>
  <form action="updatefans.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
    <input type="hidden" name="id" value="<?php echo $id; ?>">
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
        <label class="form-label" for="f_jenis_kelamin">Jenis kelamin *</label>
        <select class="form-select" id="f_jenis_kelamin" name="jenis_kelamin" required>
          <option value="">- Pilih -</option>
          <?php foreach ($opsi_jenis_kelamin as $ov => $ol) { ?>
            <option value="<?php echo e($ov); ?>" <?php echo ((string)$d['jenis_kelamin'] === (string)$ov) ? 'selected' : ''; ?>><?php echo e($ol); ?></option>
          <?php } ?>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label" for="f_tanggal_lahir">Tanggal lahir *</label>
        <input type="date" class="form-control" id="f_tanggal_lahir" name="tanggal_lahir" value="<?php echo e($d['tanggal_lahir']); ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label" for="f_alamat">Alamat lengkap *</label>
        <textarea class="form-control" id="f_alamat" name="alamat" rows="4" required><?php echo e($d['alamat']); ?></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label" for="f_email">Email *</label>
        <input type="email" class="form-control" id="f_email" name="email" value="<?php echo e($d['email']); ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label" for="f_no_telepon">No. telepon *</label>
        <input type="text" class="form-control" id="f_no_telepon" name="no_telepon" maxlength="20" value="<?php echo e($d['no_telepon']); ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label" for="f_url_sosmed">Alamat akun media sosial (opsional)</label>
        <input type="url" class="form-control" id="f_url_sosmed" name="url_sosmed" value="<?php echo e($d['url_sosmed']); ?>">
      </div>
    </div>
    <div class="card-footer">
      <input type="reset" value="Cancel" class="btn btn-secondary">
      <input type="submit" value="Simpan" class="btn btn-primary">
      <a href="<?php echo e(BASE_URL . '/fans/bacafans.php'); ?>" class="btn btn-outline-secondary">Kembali</a>
    </div>
  </form>
</div>
<?php require __DIR__ . "/../includes/footer.php"; ?>
