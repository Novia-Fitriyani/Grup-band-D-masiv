<?php
require_once __DIR__ . '/../includes/auth.php';

// Hapus data fans (POST + CSRF)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'hapus') {
    csrf_check();
    $pdo->prepare('DELETE FROM fans WHERE id_fans = ?')->execute([(int)($_POST['id'] ?? 0)]);
    set_flash('success', 'Data fans berhasil dihapus.');
    redirect('/fans/index.php');
}

$q = trim($_GET['q'] ?? '');
$where = '';
$p = [];
if ($q !== '') {
    $where = 'WHERE nama_lengkap LIKE ? OR email LIKE ?';
    $like = '%' . $q . '%';
    $p = [$like, $like];
}

// Ekspor CSV (mengikuti pencarian yang sedang aktif)
if (($_GET['aksi'] ?? '') === 'csv') {
    $s = $pdo->prepare("SELECT nama_lengkap, jenis_kelamin, tanggal_lahir, alamat, email, no_telepon, url_sosmed, tanggal_daftar FROM fans $where ORDER BY tanggal_daftar DESC");
    $s->execute($p);
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="data-fans-' . date('Ymd') . '.csv"');
    $out = fopen('php://output', 'w');
    fwrite($out, "\xEF\xBB\xBF"); // BOM agar Excel membaca UTF-8
    fputcsv($out, ['Nama lengkap', 'Jenis kelamin', 'Tanggal lahir', 'Alamat', 'Email', 'No. telepon', 'URL sosmed', 'Tanggal daftar'], ',', '"', '\\');
    $aman = fn($v) => preg_match('/^[=+\-@\t\r]/', (string)$v) ? "'" . $v : $v; // cegah CSV injection
    while ($r = $s->fetch()) fputcsv($out, array_map($aman, array_values($r)), ',', '"', '\\');
    fclose($out);
    exit;
}

$per = 20;
$cnt = $pdo->prepare("SELECT COUNT(*) FROM fans $where");
$cnt->execute($p);
$total = (int)$cnt->fetchColumn();
$jml_hal = max(1, (int)ceil($total / $per));
$hal = min($jml_hal, max(1, (int)($_GET['hal'] ?? 1)));
$off = ($hal - 1) * $per;

$s = $pdo->prepare("SELECT * FROM fans $where ORDER BY tanggal_daftar DESC LIMIT $per OFFSET $off");
$s->execute($p);
$data = $s->fetchAll();

$judul = 'Data Fans';
$menu  = 'fans';
require __DIR__ . '/../includes/header.php';
$qs = $q !== '' ? '&q=' . rawurlencode($q) : '';
?>
<div class="card">
  <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center">
    <form method="get" class="d-flex gap-2">
      <input type="text" name="q" class="form-control form-control-sm" placeholder="Cari nama atau email" value="<?= e($q) ?>">
      <button class="btn btn-primary btn-sm">Cari</button>
      <?php if ($q !== ''): ?><a href="<?= BASE_URL ?>/fans/index.php" class="btn btn-outline-secondary btn-sm">Reset</a><?php endif; ?>
    </form>
    <a href="<?= BASE_URL ?>/fans/index.php?aksi=csv<?= $qs ?>" class="btn btn-success btn-sm"><i class="bi bi-download"></i> Ekspor CSV (<?= $total ?>)</a>
  </div>
  <div class="card-body table-responsive p-0">
    <table class="table table-striped align-middle mb-0">
      <thead>
        <tr><th>No</th><th>Nama</th><th>JK</th><th>Tgl lahir</th><th>Alamat</th><th>Email</th><th>Telepon</th><th>Sosmed</th><th>Daftar</th><th>Aksi</th></tr>
      </thead>
      <tbody>
      <?php if (!$data): ?>
        <tr><td colspan="10" class="text-center text-muted py-4">Belum ada data fans.</td></tr>
      <?php endif; ?>
      <?php foreach ($data as $i => $r): ?>
        <tr>
          <td><?= $off + $i + 1 ?></td>
          <td><?= e($r['nama_lengkap']) ?></td>
          <td><?= e($r['jenis_kelamin']) ?></td>
          <td><?= e(date('d-m-Y', strtotime($r['tanggal_lahir']))) ?></td>
          <td title="<?= e($r['alamat']) ?>"><?= e(mb_strimwidth($r['alamat'], 0, 40, '...')) ?></td>
          <td><?= e($r['email']) ?></td>
          <td><?= e($r['no_telepon']) ?></td>
          <td><?= $r['url_sosmed'] ? '<a href="' . e($r['url_sosmed']) . '" target="_blank" rel="noopener noreferrer">Buka</a>' : '-' ?></td>
          <td><?= e(date('d-m-Y', strtotime($r['tanggal_daftar']))) ?></td>
          <td>
            <form method="post" onsubmit="return confirm('Hapus data fans ini?');">
              <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
              <input type="hidden" name="aksi" value="hapus">
              <input type="hidden" name="id" value="<?= (int)$r['id_fans'] ?>">
              <button class="btn btn-danger btn-sm">Hapus</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php if ($jml_hal > 1): ?>
  <div class="card-footer d-flex justify-content-between align-items-center">
    <span class="text-muted">Halaman <?= $hal ?> dari <?= $jml_hal ?></span>
    <div>
      <?php if ($hal > 1): ?><a class="btn btn-outline-secondary btn-sm" href="?hal=<?= $hal - 1 ?><?= $qs ?>">Sebelumnya</a><?php endif; ?>
      <?php if ($hal < $jml_hal): ?><a class="btn btn-outline-secondary btn-sm" href="?hal=<?= $hal + 1 ?><?= $qs ?>">Berikutnya</a><?php endif; ?>
    </div>
  </div>
  <?php endif; ?>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
