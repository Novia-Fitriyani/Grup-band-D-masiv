<?php
require_once __DIR__ . '/../includes/crud.php';
crud_run([
    'table' => 'mitra', 'pk' => 'id_mitra', 'dir' => '/mitra', 'menu' => 'mitra', 'admin' => true,
    'judul' => 'Mitra', 'judul_daftar' => 'Daftar Mitra (footer website)',
    'fields' => [
        ['name' => 'nama_mitra', 'label' => 'Nama partner', 'type' => 'text', 'required' => true, 'max' => 50, 'hint' => 'Contoh: JOOX, iTunes, Spotify, Nada Kita'],
        ['name' => 'url', 'label' => 'Tautan partner (opsional)', 'type' => 'url'],
    ],
    'columns' => [['nama_mitra', 'Nama partner'], ['url', 'Tautan', 'link']],
    'order' => 'nama_mitra',
]);
