<?php
require_once __DIR__ . '/../includes/crud.php';
crud_run([
    'table' => 'personel', 'pk' => 'id_personel', 'dir' => '/personel', 'menu' => 'personel', 'admin' => true,
    'judul' => 'Personel', 'judul_daftar' => 'Daftar Personel Band',
    'fields' => [
        ['name' => 'nama_lengkap', 'label' => 'Nama lengkap', 'type' => 'text', 'required' => true, 'max' => 100],
        ['name' => 'nama_panggilan', 'label' => 'Nama panggilan (tampil di kartu)', 'type' => 'text', 'required' => true, 'max' => 50],
        ['name' => 'peran', 'label' => 'Posisi di band', 'type' => 'text', 'required' => true, 'max' => 30, 'hint' => 'Contoh: Vokal, Gitar, Bass, Drum'],
        ['name' => 'bio', 'label' => 'Deskripsi singkat (opsional)', 'type' => 'textarea'],
        ['name' => 'foto', 'label' => 'Foto', 'type' => 'image', 'folder' => 'personel', 'required' => true],
        ['name' => 'urutan', 'label' => 'Urutan tampil (opsional)', 'type' => 'int', 'hint' => 'Angka kecil tampil lebih dulu'],
        ['name' => 'medsos_anggota', 'label' => 'Alamat akun media sosial anggota', 'type' => 'url', 'required' => true],
    ],
    'columns' => [['foto', 'Foto', 'image'], ['nama_panggilan', 'Nama'], ['peran', 'Posisi'], ['urutan', 'Urutan']],
    'order' => 'COALESCE(urutan, 999999), id_personel',
]);
