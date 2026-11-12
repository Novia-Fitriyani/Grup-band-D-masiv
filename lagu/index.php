<?php
require_once __DIR__ . '/../includes/crud.php';

$idAlbum = (int)($_GET['album'] ?? 0);
$st = $pdo->prepare('SELECT judul_album FROM album WHERE id_album = ?');
$st->execute([$idAlbum]);
$album = $st->fetch();
if (!$album) {
    set_flash('danger', 'Album tidak ditemukan.');
    redirect('/album/index.php');
}

crud_run([
    'table' => 'lagu', 'pk' => 'id_lagu', 'dir' => '/lagu', 'menu' => 'album', 'admin' => true,
    'parent' => ['field' => 'id_album', 'value' => $idAlbum, 'param' => 'album'],
    'judul' => 'Lagu', 'judul_daftar' => 'Lagu dalam album: ' . $album['judul_album'],
    'kembali' => ['label' => 'Kembali ke album', 'url' => BASE_URL . '/album/index.php'],
    'fields' => [
        ['name' => 'judul_lagu', 'label' => 'Judul lagu', 'type' => 'text', 'required' => true, 'max' => 150],
        ['name' => 'no_urut', 'label' => 'Nomor urut dalam album (opsional)', 'type' => 'int'],
        ['name' => 'link_spotify', 'label' => 'Tautan lagu di Spotify (opsional)', 'type' => 'url'],
    ],
    'validasi' => function (array $d, $id) use ($pdo, $idAlbum) {
        if ($d['no_urut'] === '' || !ctype_digit($d['no_urut'])) return [];
        $q = $pdo->prepare('SELECT COUNT(*) FROM lagu WHERE id_album = ? AND no_urut = ? AND id_lagu <> ?');
        $q->execute([$idAlbum, (int)$d['no_urut'], $id]);
        return $q->fetchColumn() ? ['Nomor urut ' . (int)$d['no_urut'] . ' sudah dipakai lagu lain di album ini.'] : [];
    },
    'columns' => [['no_urut', 'No. urut'], ['judul_lagu', 'Judul lagu'], ['link_spotify', 'Spotify', 'link']],
    'order' => 'COALESCE(no_urut, 999999), id_lagu',
]);
