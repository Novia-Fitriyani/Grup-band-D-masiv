<?php
require_once __DIR__ . '/../includes/crud.php';
crud_run([
    'table' => 'galeri', 'pk' => 'id_galeri', 'dir' => '/galeri', 'menu' => 'galeri', 'admin' => true,
    'judul' => 'Galeri', 'judul_daftar' => 'Galeri Foto dan Video',
    'fields' => [
        ['name' => 'tipe', 'label' => 'Jenis', 'type' => 'select', 'required' => true, 'options' => ['foto' => 'Foto', 'video' => 'Video']],
        ['name' => 'judul', 'label' => 'Judul dokumentasi', 'type' => 'text', 'required' => true, 'max' => 150],
        ['name' => 'tanggal', 'label' => 'Tanggal kegiatan', 'type' => 'date', 'required' => true],
        ['name' => 'file_thumbnail', 'label' => 'Gambar / thumbnail', 'type' => 'image', 'folder' => 'galeri', 'required' => true],
        ['name' => 'url_video', 'label' => 'Tautan YouTube (khusus video)', 'type' => 'url', 'hint' => 'Wajib untuk jenis Video, kosongkan untuk Foto'],
        ['name' => 'status_aktif', 'label' => 'Status tampil', 'type' => 'select', 'required' => true, 'default' => 'arsip',
         'options' => ['arsip' => 'Arsip (tidak ditampilkan)', 'tampil' => 'Tampil di Latest Video footer']],
    ],
    'validasi' => function (array $d, $id) {
        $err = [];
        if ($d['tipe'] === 'foto' && $d['url_video'] !== '') $err[] = 'Tautan YouTube hanya boleh diisi untuk jenis Video.';
        if ($d['tipe'] === 'video' && $d['url_video'] === '') $err[] = 'Tautan YouTube wajib diisi untuk jenis Video.';
        if ($d['tipe'] === 'foto' && $d['status_aktif'] === 'tampil') $err[] = 'Status "Tampil di Latest Video" hanya untuk jenis Video.';
        return $err;
    },
    'columns' => [['file_thumbnail', 'Gambar', 'image'], ['judul', 'Judul'], ['tipe', 'Jenis', 'badge'], ['tanggal', 'Tanggal', 'date'], ['status_aktif', 'Status', 'badge']],
    'order' => 'tanggal DESC, id_galeri DESC',
]);
