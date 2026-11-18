<?php
require_once __DIR__ . "/../includes/auth.php";
require_once __DIR__ . "/../config/koneksi_mysqli.php";

// nilai yang ditampilkan di form
$d = [];
$d['nama_produk'] = '';
$d['deskripsi'] = '';
$d['harga'] = '';
$d['link_beli'] = '';
$d['status'] = 'tersedia';

$errors = $_SESSION['form_produk_add_errors'] ?? [];
$old    = $_SESSION['form_produk_add_old'] ?? [];
unset($_SESSION['form_produk_add_errors'], $_SESSION['form_produk_add_old']);
foreach ($old as $kunci => $nilai) {
  if (array_key_exists($kunci, $d)) { $d[$kunci] = $nilai; }
}

$opsi_status = ['tersedia' => 'Tersedia', 'habis' => 'Habis'];

$judul = 'Form Tambah Produk';
$menu  = 'produk';
require __DIR__ . "/../includes/header.php";
?>
<div class="card card-primary card-outline">
  <div class="card-header">
    <h3 class="card-title mb-0">Form Tambah Produk</h3>
  </div>
  <form action="simpanproduk.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
    <div class="card-body">
      <?php if ($errors) { ?>
        <div class="alert alert-danger"><ul class="mb-0">
          <?php foreach ($errors as $pesan) { ?><li><?php echo e($pesan); ?></li><?php } ?>
        </ul></div>
      <?php } ?>

      <div class="mb-3">
        <label class="form-label" for="f_nama_produk">Nama produk *</label>
        <input type="text" class="form-control" id="f_nama_produk" name="nama_produk" maxlength="100" value="<?php echo e($d['nama_produk']); ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label" for="f_deskripsi">Deskripsi singkat (opsional)</label>
        <textarea class="form-control" id="f_deskripsi" name="deskripsi" rows="4"><?php echo e($d['deskripsi']); ?></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label" for="f_harga">Harga (rupiah) *</label>
        <input type="number" class="form-control" id="f_harga" name="harga" min="0" value="<?php echo e($d['harga']); ?>" required>
        <div class="form-text">Angka saja, tanpa titik. Contoh: 150000</div>
      </div>
      <div class="mb-3">
        <label class="form-label" for="f_gambar">Foto produk *</label>
        <input type="file" class="form-control" id="f_gambar" name="gambar" accept="image/jpeg,image/png,image/gif" required>
        <div class="form-text">JPG, JPEG, PNG, atau GIF, maksimal 1 MB.</div>
      </div>
      <div class="mb-3">
        <label class="form-label" for="f_link_beli">Tautan tombol Buy Now (marketplace) *</label>
        <input type="url" class="form-control" id="f_link_beli" name="link_beli" value="<?php echo e($d['link_beli']); ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label" for="f_status">Ketersediaan *</label>
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
      <a href="<?php echo e(BASE_URL . '/produk/bacaproduk.php'); ?>" class="btn btn-outline-secondary">Kembali</a>
    </div>
  </form>
</div>
<?php require __DIR__ . "/../includes/footer.php"; ?>
