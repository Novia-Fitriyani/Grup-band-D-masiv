<?php
require_once __DIR__ . '/../includes/crud.php';
crud_run([
    'table' => 'media_sosial', 'pk' => 'id_sosmed', 'dir' => '/media_sosial', 'menu' => 'sosmed', 'admin' => true,
    'judul' => 'Media Sosial', 'judul_daftar' => 'Daftar Media Sosial Resmi',
    'fields' => [
        ['name' => 'platform', 'label' => 'Platform', 'type' => 'text', 'required' => true, 'max' => 30, 'hint' => 'Contoh: Facebook, Twitter, Instagram, YouTube'],
        ['name' => 'url', 'label' => 'Alamat akun', 'type' => 'url', 'required' => true],
        ['name' => 'ikon', 'label' => 'Nama class ikon Font Awesome (opsional)', 'type' => 'text', 'max' => 50, 'hint' => 'Contoh: fa-brands fa-instagram'],
        ['name' => 'nama_kontak', 'label' => 'Nama narahubung', 'type' => 'text', 'required' => true, 'max' => 50],
        ['name' => 'no_telepon', 'label' => 'No. telepon narahubung', 'type' => 'text', 'required' => true, 'max' => 20],
    ],
    'columns' => [['platform', 'Platform'], ['url', 'Akun', 'link'], ['nama_kontak', 'Narahubung'], ['no_telepon', 'Telepon']],
    'order' => 'platform',
]);
