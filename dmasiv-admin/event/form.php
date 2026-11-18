<?php
require_once __DIR__ . '/../includes/auth.php';

$id = (int)($_GET['id'] ?? 0);
$d  = ['id_kategori' => '', 'judul' => '', 'lokasi' => '', 'tanggal_event' => '', 'waktu_mulai' => '', 'deskripsi' => ''];

if ($id) {
    $q = $pdo->prepare('SELECT * FROM event WHERE id_event = ?');
    $q->execute([$id]);
    $row = $q->fetch();
    if (!$row) {
        set_flash('danger', 'Event tidak ditemukan.');
        redirect('/event/index.php');
    }
    $d = array_merge($d, $row);
    $d['waktu_mulai'] = $row['waktu_mulai'] ? substr($row['waktu_mulai'], 0, 5) : '';
}

$kategori = $pdo->query('SELECT id_kategori, nama_kategori FROM kategori_event ORDER BY nama_kategori')->fetchAll();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $d['id_kategori']   = (int)($_POST['id_kategori'] ?? 0);
    $d['judul']         = trim($_POST['judul'] ?? '');
    $d['lokasi']        = trim($_POST['lokasi'] ?? '');
    $d['tanggal_event'] = trim($_POST['tanggal_event'] ?? '');
    $d['waktu_mulai']   = trim($_POST['waktu_mulai'] ?? '');
    $d['deskripsi']     = trim($_POST['deskripsi'] ?? '');

    $ada = $pdo->prepare('SELECT COUNT(*) FROM kategori_event WHERE id_kategori = ?');
    $ada->execute([$d['id_kategori']]);
    if (!$ada->fetchColumn()) $errors[] = 'Pilih kategori yang valid.';

    if ($d['judul'] === '' || mb_strlen($d['judul']) > 150)   $errors[] = 'Judul wajib diisi (maksimal 150 karakter).';
    if ($d['lokasi'] === '' || mb_strlen($d['lokasi']) > 100) $errors[] = 'Lokasi wajib diisi (maksimal 100 karakter).';

    $tgl = DateTime::createFromFormat('Y-m-d', $d['tanggal_event']);
    if (!$tgl || $tgl->format('Y-m-d') !== $d['tanggal_event']) $errors[] = 'Tanggal event tidak valid.';

    if ($d['waktu_mulai'] !== '' && !preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $d['waktu_mulai'])) {
        $errors[] = 'Jam mulai tidak valid.';
    }

    if (!$errors) {
        $waktu = $d['waktu_mulai'] === '' ? null : $d['waktu_mulai'];
        $desk  = $d['deskripsi'] === '' ? null : $d['deskripsi'];
        if ($id) {
            $s = $pdo->prepare('UPDATE event SET id_kategori=?, judul=?, lokasi=?, tanggal_event=?, waktu_mulai=?, deskripsi=? WHERE id_event=?');
            $s->execute([$d['id_kategori'], $d['judul'], $d['lokasi'], $d['tanggal_event'], $waktu, $desk, $id]);
            set_flash('success', 'Event berhasil diperbarui.');
        } else {
            $s = $pdo->prepare('INSERT INTO event (id_kategori, judul, lokasi, tanggal_event, waktu_mulai, deskripsi, id_admin) VALUES (?,?,?,?,?,?,?)');
            $s->execute([$d['id_kategori'], $d['judul'], $d['lokasi'], $d['tanggal_event'], $waktu, $desk, $_SESSION['id_admin']]);
            set_flash('success', 'Event berhasil ditambahkan.');
        }
        redirect('/event/index.php');
    }
}

$judul = $id ? 'Ubah Event' : 'Tambah Event';
$menu  = 'event';
require __DIR__ . '/../includes/header.php';
?>
<div class="card">
  <form method="post">
    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
    <div class="card-body">
      <?php if ($errors): ?>
        <div class="alert alert-danger mb-3"><ul class="mb-0">
          <?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?>
        </ul></div>
      <?php endif; ?>

      <div class="mb-3">
        <label class="form-label" for="id_kategori">Kategori</label>
        <select class="form-select" id="id_kategori" name="id_kategori" required>
          <option value="">- Pilih kategori -</option>
          <?php foreach ($kategori as $k): ?>
            <option value="<?= (int)$k['id_kategori'] ?>" <?= (int)$d['id_kategori'] === (int)$k['id_kategori'] ? 'selected' : '' ?>><?= e($k['nama_kategori']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label" for="judul">Judul event</label>
        <input type="text" class="form-control" id="judul" name="judul" maxlength="150" required value="<?= e($d['judul']) ?>">
      </div>
      <div class="mb-3">
        <label class="form-label" for="lokasi">Lokasi</label>
        <input type="text" class="form-control" id="lokasi" name="lokasi" maxlength="100" required value="<?= e($d['lokasi']) ?>">
      </div>
      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label" for="tanggal_event">Tanggal event</label>
          <input type="date" class="form-control" id="tanggal_event" name="tanggal_event" required value="<?= e($d['tanggal_event']) ?>">
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label" for="waktu_mulai">Jam mulai (opsional)</label>
          <input type="time" class="form-control" id="waktu_mulai" name="waktu_mulai" value="<?= e($d['waktu_mulai']) ?>">
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label" for="deskripsi">Deskripsi (opsional)</label>
        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4"><?= e($d['deskripsi']) ?></textarea>
      </div>
    </div>
    <div class="card-footer">
      <button type="submit" class="btn btn-primary">Simpan</button>
      <a href="<?= BASE_URL ?>/event/index.php" class="btn btn-secondary">Batal</a>
    </div>
  </form>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
