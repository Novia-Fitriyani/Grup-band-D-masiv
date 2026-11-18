<?php
// Mesin CRUD bersama: dipakai modul Galeri, Album, Lagu, Produk, Personel,
// Media Sosial, Mitra, dan Kelola Admin. Setiap modul hanya mengirim konfigurasi.
require_once __DIR__ . '/auth.php';

const CRUD_MAKS_GAMBAR = 2097152; // 2 MB
const CRUD_FOLDER_GAMBAR = 'gambar';

function crud_pesan_db(PDOException $ex, array $c) {
    $kode = (int)($ex->errorInfo[1] ?? 0);
    if ($kode === 1451) return $c['pesan_dipakai'] ?? 'Data ini masih dipakai data lain sehingga tidak bisa dihapus.';
    if ($kode === 1062) return 'Ada nilai yang sudah dipakai data lain (harus unik).';
    return 'Terjadi kesalahan pada database. Periksa kembali isian Anda.';
}

// Simpan gambar unggahan setelah divalidasi isinya (bukan hanya ekstensi)
function crud_simpan_gambar(array $file, $folder, array &$errors, $label) {
    $kode = $file['error'] ?? UPLOAD_ERR_NO_FILE;
    if ($kode === UPLOAD_ERR_NO_FILE) return null;
    if ($kode !== UPLOAD_ERR_OK) { $errors[] = "$label: unggahan gagal (kode $kode)."; return null; }
    if ($file['size'] > CRUD_MAKS_GAMBAR) { $errors[] = "$label: ukuran maksimal 2 MB."; return null; }

    $info = @getimagesize($file['tmp_name']);
    $tipe = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'];
    if (!$info || !isset($tipe[$info[2]])) { $errors[] = "$label: hanya file JPG, PNG, atau WEBP."; return null; }

    $akar = dirname(__DIR__) . '/' . CRUD_FOLDER_GAMBAR;
    $dir  = $akar . '/' . $folder;
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    if (!file_exists($akar . '/.htaccess')) {
        file_put_contents($akar . '/.htaccess', "<FilesMatch \"\\.(php|phtml|php5|phar)\$\">\n  Require all denied\n</FilesMatch>\n");
    }
    $nama = bin2hex(random_bytes(8)) . '.' . $tipe[$info[2]];
    if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $nama)) { $errors[] = "$label: gagal menyimpan file."; return null; }
    return CRUD_FOLDER_GAMBAR . "/$folder/$nama";
}

function crud_hapus_file($path) {
    if ($path && (strpos($path, CRUD_FOLDER_GAMBAR . '/') === 0 || strpos($path, 'uploads/') === 0) && strpos($path, '..') === false) {
        $f = dirname(__DIR__) . '/' . $path;
        if (is_file($f)) @unlink($f);
    }
}

// Isi satu sel tabel sesuai tipe kolom
function crud_sel(array $kol, array $r) {
    $v = $r[$kol[0]] ?? null;
    $tipe = $kol[2] ?? 'text';
    if ($v === null || $v === '') return '<span class="text-muted">-</span>';
    switch ($tipe) {
        case 'image':  return '<img src="' . e(BASE_URL . '/' . $v) . '" alt="" class="rounded" style="height:48px;width:48px;object-fit:cover">';
        case 'date':   return e(date('d-m-Y', strtotime($v)));
        case 'rupiah': return e('Rp ' . number_format((int)$v, 0, ',', '.'));
        case 'badge':  return '<span class="badge text-bg-secondary">' . e($v) . '</span>';
        case 'trunc':  return e(mb_strimwidth($v, 0, 60, '...'));
        case 'link':   return '<a href="' . e($v) . '" target="_blank" rel="noopener noreferrer">Buka</a>';
        default:       return e($v);
    }
}

function crud_run(array $c) {
    global $pdo;
    $tabel  = $c['table'];
    $pk     = $c['pk'];
    $parent = $c['parent'] ?? null;
    $menu   = $c['menu'];
    $judul  = $c['judul'];

    $url = function (array $q = []) use ($c, $parent) {
        $s = [];
        if ($parent) $s[] = $parent['param'] . '=' . (int)$parent['value'];
        foreach ($q as $k => $v) $s[] = $k . '=' . rawurlencode((string)$v);
        return BASE_URL . $c['dir'] . '/index.php' . ($s ? '?' . implode('&', $s) : '');
    };
    $ke = function (array $q = []) use ($url) { header('Location: ' . $url($q)); exit; };

    $ambil = function ($id) use ($pdo, $tabel, $pk, $parent) {
        $sql = "SELECT * FROM `$tabel` WHERE `$pk` = ?";
        $p = [$id];
        if ($parent) { $sql .= " AND `{$parent['field']}` = ?"; $p[] = $parent['value']; }
        $s = $pdo->prepare($sql);
        $s->execute($p);
        return $s->fetch();
    };

    // ---------- HAPUS ----------
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'hapus') {
        csrf_check();
        $row = $ambil((int)($_POST['id'] ?? 0));
        if (!$row) { set_flash('danger', 'Data tidak ditemukan.'); $ke(); }
        if (isset($c['boleh_hapus'])) {
            $hasil = ($c['boleh_hapus'])($row);
            if ($hasil !== true) { set_flash('danger', $hasil); $ke(); }
        }
        try {
            $pdo->prepare("DELETE FROM `$tabel` WHERE `$pk` = ?")->execute([$row[$pk]]);
            foreach ($c['fields'] as $f) {
                if ($f['type'] === 'image') crud_hapus_file($row[$f['name']] ?? null);
            }
            set_flash('success', 'Data berhasil dihapus.');
        } catch (PDOException $ex) {
            set_flash('danger', crud_pesan_db($ex, $c));
        }
        $ke();
    }

    $mode_form = (($_GET['aksi'] ?? '') === 'form');

    if ($mode_form) {
        $id  = (int)($_GET['id'] ?? 0);
        $row = null;
        $d   = [];
        foreach ($c['fields'] as $f) $d[$f['name']] = $f['default'] ?? '';

        if ($id) {
            $row = $ambil($id);
            if (!$row) { set_flash('danger', 'Data tidak ditemukan.'); $ke(); }
            foreach ($c['fields'] as $f) {
                $n = $f['name'];
                if ($f['type'] === 'password' || $f['type'] === 'image') continue;
                $v = $row[$n] ?? '';
                if ($f['type'] === 'time' && $v !== null) $v = substr($v, 0, 5);
                $d[$n] = $v ?? '';
            }
        }
        $errors = [];

        // ---------- SIMPAN ----------
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'simpan') {
            csrf_check();
            $nilai = [];
            $baru  = [];

            foreach ($c['fields'] as $f) {
                $n = $f['name']; $t = $f['type']; $label = $f['label']; $wajib = !empty($f['required']);

                if ($t === 'image') {
                    $sebelum = count($errors);
                    $path = crud_simpan_gambar($_FILES[$n] ?? ['error' => UPLOAD_ERR_NO_FILE], $f['folder'], $errors, $label);
                    if ($path) { $nilai[$n] = $path; $baru[] = $path; }
                    elseif ($wajib && !$id && count($errors) === $sebelum) $errors[] = "$label wajib diunggah.";
                    continue;
                }

                $v = trim((string)($_POST[$n] ?? ''));
                $d[$n] = $v;

                if ($t === 'password') {
                    $d[$n] = '';
                    if ($v === '') { if (!$id) $errors[] = "$label wajib diisi."; continue; }
                    if (strlen($v) < 8) $errors[] = "$label minimal 8 karakter.";
                    else $nilai[$n] = password_hash($v, PASSWORD_DEFAULT);
                    continue;
                }

                if ($v === '') {
                    if ($wajib) $errors[] = "$label wajib diisi.";
                    $nilai[$n] = null;
                    continue;
                }

                switch ($t) {
                    case 'text':
                    case 'textarea':
                        $maks = $f['max'] ?? ($t === 'text' ? 255 : 65535);
                        if (mb_strlen($v) > $maks) $errors[] = "$label maksimal $maks karakter.";
                        else $nilai[$n] = $v;
                        break;
                    case 'int':
                        if (!preg_match('/^\d{1,9}$/', $v)) $errors[] = "$label harus berupa angka bulat (0 atau lebih).";
                        else $nilai[$n] = (int)$v;
                        break;
                    case 'date':
                        $tgl = DateTime::createFromFormat('Y-m-d', $v);
                        if (!$tgl || $tgl->format('Y-m-d') !== $v) $errors[] = "$label tidak valid.";
                        else $nilai[$n] = $v;
                        break;
                    case 'time':
                        if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $v)) $errors[] = "$label tidak valid.";
                        else $nilai[$n] = $v;
                        break;
                    case 'email':
                        if (!filter_var($v, FILTER_VALIDATE_EMAIL) || mb_strlen($v) > 100) $errors[] = "$label tidak valid.";
                        else $nilai[$n] = $v;
                        break;
                    case 'url':
                        if (!filter_var($v, FILTER_VALIDATE_URL) || !preg_match('#^https?://#i', $v) || mb_strlen($v) > 255) {
                            $errors[] = "$label harus berupa alamat lengkap berawalan http:// atau https://.";
                        } else $nilai[$n] = $v;
                        break;
                    case 'select':
                        if (!isset($f['options'][$v])) $errors[] = "$label tidak valid.";
                        else $nilai[$n] = $v;
                        break;
                }
            }

            if (isset($c['validasi'])) {
                $errors = array_merge($errors, ($c['validasi'])($d, $id));
            }

            if (!$errors) {
                try {
                    $vals = array_values($nilai);
                    if (!$id) {
                        $cols = array_keys($nilai);
                        if (!empty($c['admin'])) { $cols[] = 'id_admin'; $vals[] = $_SESSION['id_admin']; }
                        if ($parent) { $cols[] = $parent['field']; $vals[] = $parent['value']; }
                        $sql = "INSERT INTO `$tabel` (" . implode(',', array_map(fn($x) => "`$x`", $cols)) . ") VALUES ("
                             . implode(',', array_fill(0, count($cols), '?')) . ')';
                        $pdo->prepare($sql)->execute($vals);
                        set_flash('success', 'Data berhasil ditambahkan.');
                    } else {
                        $set = implode(', ', array_map(fn($x) => "`$x`=?", array_keys($nilai)));
                        $vals[] = $id;
                        $pdo->prepare("UPDATE `$tabel` SET $set WHERE `$pk`=?")->execute($vals);
                        foreach ($c['fields'] as $f) {
                            if ($f['type'] === 'image' && isset($nilai[$f['name']])) crud_hapus_file($row[$f['name']] ?? null);
                        }
                        set_flash('success', 'Data berhasil diperbarui.');
                    }
                    $ke();
                } catch (PDOException $ex) {
                    $errors[] = crud_pesan_db($ex, $c);
                }
            }
            foreach ($baru as $p) crud_hapus_file($p); // buang file baru bila gagal
        }

        $judul = ($id ? 'Ubah ' : 'Tambah ') . $c['judul'];
        require __DIR__ . '/header.php';
        ?>
<div class="card">
  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
    <input type="hidden" name="aksi" value="simpan">
    <div class="card-body">
      <?php if ($errors): ?>
        <div class="alert alert-danger"><ul class="mb-0">
          <?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?>
        </ul></div>
      <?php endif; ?>

      <?php foreach ($c['fields'] as $f):
          $n = $f['name']; $t = $f['type']; $val = $d[$n] ?? ''; $wajib = !empty($f['required']);
          $req = $wajib && !($t === 'password' && $id) && !($t === 'image' && $id) ? 'required' : ''; ?>
        <div class="mb-3">
          <label class="form-label" for="f_<?= e($n) ?>"><?= e($f['label']) ?><?= $wajib ? ' *' : '' ?></label>

          <?php if ($t === 'textarea'): ?>
            <textarea class="form-control" id="f_<?= e($n) ?>" name="<?= e($n) ?>" rows="4" <?= $req ?>><?= e($val) ?></textarea>

          <?php elseif ($t === 'select'): ?>
            <select class="form-select" id="f_<?= e($n) ?>" name="<?= e($n) ?>" <?= $req ?>>
              <option value="">- Pilih -</option>
              <?php foreach ($f['options'] as $ov => $ol): ?>
                <option value="<?= e($ov) ?>" <?= (string)$val === (string)$ov ? 'selected' : '' ?>><?= e($ol) ?></option>
              <?php endforeach; ?>
            </select>

          <?php elseif ($t === 'image'): ?>
            <?php if ($row && !empty($row[$n])): ?>
              <div class="mb-2"><img src="<?= e(BASE_URL . '/' . $row[$n]) ?>" alt="" class="rounded" style="max-height:120px"></div>
            <?php endif; ?>
            <input type="file" class="form-control" id="f_<?= e($n) ?>" name="<?= e($n) ?>" accept="image/jpeg,image/png,image/webp" <?= $req ?>>
            <div class="form-text">JPG, PNG, atau WEBP, maksimal 2 MB.<?= $id ? ' Kosongkan jika tidak ingin mengganti gambar.' : '' ?></div>

          <?php else:
              $tipeInput = ['text' => 'text', 'int' => 'number', 'date' => 'date', 'time' => 'time', 'email' => 'email', 'url' => 'url', 'password' => 'password'][$t] ?? 'text'; ?>
            <input type="<?= $tipeInput ?>" class="form-control" id="f_<?= e($n) ?>" name="<?= e($n) ?>"
                   <?= $t === 'int' ? 'min="0"' : '' ?> <?= isset($f['max']) ? 'maxlength="' . (int)$f['max'] . '"' : '' ?>
                   <?= $t === 'password' ? 'autocomplete="new-password"' : '' ?>
                   value="<?= $t === 'password' ? '' : e($val) ?>" <?= $req ?>>
          <?php endif; ?>

          <?php if (!empty($f['hint'])): ?><div class="form-text"><?= e($f['hint']) ?></div><?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
    <div class="card-footer">
      <button type="submit" class="btn btn-primary">Simpan</button>
      <a href="<?= e($url()) ?>" class="btn btn-secondary">Batal</a>
    </div>
  </form>
</div>
        <?php
        require __DIR__ . '/footer.php';
        return;
    }

    // ---------- DAFTAR ----------
    $sql = $c['list_sql'] ?? "SELECT * FROM `$tabel`";
    $p = [];
    if ($parent) {
        $sql .= (stripos($sql, ' WHERE ') !== false ? ' AND ' : ' WHERE ') . "`{$parent['field']}` = ?";
        $p[] = $parent['value'];
    }
    $sql .= ' ORDER BY ' . ($c['order'] ?? "`$pk` DESC");
    $s = $pdo->prepare($sql);
    $s->execute($p);
    $data = $s->fetchAll();

    require __DIR__ . '/header.php';
    ?>
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h3 class="card-title mb-0"><?= e($c['judul_daftar'] ?? ('Daftar ' . $c['judul'])) ?></h3>
    <div>
      <?php if (isset($c['kembali'])): ?>
        <a href="<?= e($c['kembali']['url']) ?>" class="btn btn-outline-secondary btn-sm"><?= e($c['kembali']['label']) ?></a>
      <?php endif; ?>
      <a href="<?= e($url(['aksi' => 'form'])) ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Tambah</a>
    </div>
  </div>
  <div class="card-body table-responsive p-0">
    <table class="table table-striped align-middle mb-0">
      <thead>
        <tr>
          <th style="width:50px">No</th>
          <?php foreach ($c['columns'] as $kol): ?><th><?= e($kol[1]) ?></th><?php endforeach; ?>
          <th style="width:230px">Aksi</th>
        </tr>
      </thead>
      <tbody>
      <?php if (!$data): ?>
        <tr><td colspan="<?= count($c['columns']) + 2 ?>" class="text-center text-muted py-4">Belum ada data.</td></tr>
      <?php endif; ?>
      <?php foreach ($data as $i => $r): ?>
        <tr>
          <td><?= $i + 1 ?></td>
          <?php foreach ($c['columns'] as $kol): ?><td><?= crud_sel($kol, $r) ?></td><?php endforeach; ?>
          <td>
            <?php foreach ($c['aksi_tambahan'] ?? [] as $a): ?>
              <a href="<?= e(($a['url'])($r)) ?>" class="btn <?= e($a['class'] ?? 'btn-info') ?> btn-sm"><?= e($a['label']) ?></a>
            <?php endforeach; ?>
            <a href="<?= e($url(['aksi' => 'form', 'id' => $r[$pk]])) ?>" class="btn btn-warning btn-sm">Ubah</a>
            <form method="post" class="d-inline" onsubmit="return confirm('Hapus data ini?');">
              <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
              <input type="hidden" name="aksi" value="hapus">
              <input type="hidden" name="id" value="<?= (int)$r[$pk] ?>">
              <button class="btn btn-danger btn-sm">Hapus</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
    <?php
    require __DIR__ . '/footer.php';
}
