<?php
require_once __DIR__ . '/../includes/crud.php';
crud_run([
    'table' => 'produk', 'pk' => 'id_produk', 'dir' => '/produk', 'menu' => 'produk', 'admin' => true,
    'judul' => 'Produk', 'judul_daftar' => 'Katalog Merchandise',
    'fields' => [
        ['name' => 'nama_produk', 'label' => 'Nama produk', 'type' => 'text', 'required' => true, 'max' => 100],
        ['name' => 'deskripsi', 'label' => 'Deskripsi singkat (opsional)', 'type' => 'textarea'],
        ['name' => 'harga', 'label' => 'Harga (rupiah)', 'type' => 'int', 'required' => true, 'hint' => 'Angka saja, tanpa titik. Contoh: 150000'],
        ['name' => 'gambar', 'label' => 'Foto produk', 'type' => 'image', 'folder' => 'produk', 'required' => true],
        ['name' => 'link_beli', 'label' => 'Tautan tombol Buy Now (marketplace)', 'type' => 'url', 'required' => true],
        ['name' => 'status', 'label' => 'Ketersediaan', 'type' => 'select', 'required' => true, 'default' => 'tersedia',
         'options' => ['tersedia' => 'Tersedia', 'habis' => 'Habis']],
    ],
    'columns' => [['gambar', 'Foto', 'image'], ['nama_produk', 'Produk'], ['harga', 'Harga', 'rupiah'], ['status', 'Status', 'badge'], ['link_beli', 'Link beli', 'link']],
    'order' => 'id_produk DESC',
]);
