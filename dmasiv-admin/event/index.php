<?php
require_once __DIR__ . '/../includes/auth.php';

// Hapus data (hanya lewat POST + token CSRF), diproses sebelum ada output HTML
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'hapus') {
    csrf_check();
    $hapus = $pdo->prepare('DELETE FROM event WHERE id_event = ?');
    $hapus->execute([(int)($_POST['id'] ?? 0)]);
    set_flash('success', 'Event berhasil dihapus.');
    redirect('/event/index.php');
}

$judul = 'Data Event';
$menu  = 'event';
require __DIR__ . '/../includes/header.php';

$data = $pdo->query(
    'SELECT e.*, k.nama_kategori
       FROM event e
       JOIN kategori_event k ON k.id_kategori = e.id_kategori
      ORDER BY e.tanggal_event DESC'
)->fetchAll();
?>
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h3 class="card-title mb-0">Daftar Event</h3>
    <a href="<?= BASE_URL ?>/event/form.php" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Tambah Event</a>
  </div>
  <div class="card-body table-responsive p-0">
    <table class="table table-striped align-middle mb-0">
      <thead>
        <tr><th style="width:50px">No</th><th>Judul</th><th>Kategori</th><th>Lokasi</th><th>Tanggal</th><th>Mulai</th><th style="width:150px">Aksi</th></tr>
      </thead>
      <tbody>
      <?php if (!$data): ?>
        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada data event.</td></tr>
      <?php endif; ?>
      <?php foreach ($data as $i => $r): ?>
        <tr>
          <td><?= $i + 1 ?></td>
          <td><?= e($r['judul']) ?></td>
          <td><span class="badge text-bg-secondary"><?= e($r['nama_kategori']) ?></span></td>
          <td><?= e($r['lokasi']) ?></td>
          <td><?= e(date('d-m-Y', strtotime($r['tanggal_event']))) ?></td>
          <td><?= $r['waktu_mulai'] ? e(substr($r['waktu_mulai'], 0, 5)) : '-' ?></td>
          <td>
            <a href="<?= BASE_URL ?>/event/form.php?id=<?= (int)$r['id_event'] ?>" class="btn btn-warning btn-sm">Ubah</a>
            <form method="post" class="d-inline" onsubmit="return confirm('Hapus event ini?');">
              <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
              <input type="hidden" name="aksi" value="hapus">
              <input type="hidden" name="id" value="<?= (int)$r['id_event'] ?>">
              <button class="btn btn-danger btn-sm">Hapus</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
