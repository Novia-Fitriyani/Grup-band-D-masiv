<?php
require_once __DIR__ . '/../includes/crud.php';
crud_run([
    'table' => 'album', 'pk' => 'id_album', 'dir' => '/album', 'menu' => 'album', 'admin' => true,
    'judul' => 'Album', 'judul_daftar' => 'Daftar Album dan Single',
    'fields' => [
        ['name' => 'judul_album', 'label' => 'Judul album/single', 'type' => 'text', 'required' => true, 'max' => 150],
        ['name' => 'tanggal_rilis', 'label' => 'Tanggal rilis', 'type' => 'date', 'required' => true],
        ['name' => 'cover', 'label' => 'Cover', 'type' => 'image', 'folder' => 'album', 'required' => true],
        ['name' => 'link_spotify', 'label' => 'Tautan album di Spotify (opsional)', 'type' => 'url'],
    ],
    'list_sql' => 'SELECT a.*, (SELECT COUNT(*) FROM lagu l WHERE l.id_album = a.id_album) AS jumlah_lagu FROM album a',
    'order' => 'a.tanggal_rilis DESC',
    'columns' => [['cover', 'Cover', 'image'], ['judul_album', 'Judul'], ['tanggal_rilis', 'Rilis', 'date'], ['jumlah_lagu', 'Jumlah lagu']],
    'aksi_tambahan' => [[
        'label' => 'Kelola lagu', 'class' => 'btn-info',
        'url' => fn($r) => BASE_URL . '/lagu/index.php?album=' . (int)$r['id_album'],
    ]],
]);
