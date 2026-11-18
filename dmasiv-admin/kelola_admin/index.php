<?php
require_once __DIR__ . '/../includes/crud.php';
crud_run([
    'table' => 'admin', 'pk' => 'id_admin', 'dir' => '/kelola_admin', 'menu' => 'admin', 'admin' => false,
    'judul' => 'Admin', 'judul_daftar' => 'Daftar Akun Admin',
    'pesan_dipakai' => 'Admin ini sudah pernah menambahkan konten sehingga tidak bisa dihapus. Ubah statusnya menjadi Nonaktif.',
    'fields' => [
        ['name' => 'username', 'label' => 'Username', 'type' => 'text', 'required' => true, 'max' => 50],
        ['name' => 'nama_lengkap', 'label' => 'Nama lengkap', 'type' => 'text', 'required' => true, 'max' => 100],
        ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true],
        ['name' => 'password', 'label' => 'Kata sandi', 'type' => 'password', 'required' => true, 'hint' => 'Minimal 8 karakter. Saat mengubah data, kosongkan jika tidak ingin mengganti.'],
        ['name' => 'status', 'label' => 'Status akun', 'type' => 'select', 'required' => true, 'default' => 'aktif',
         'options' => ['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif (tidak bisa login)']],
    ],
    'validasi' => function (array $d, $id) {
        if ($id === (int)$_SESSION['id_admin'] && $d['status'] === 'nonaktif') {
            return ['Anda tidak bisa menonaktifkan akun yang sedang dipakai.'];
        }
        return [];
    },
    'boleh_hapus' => function (array $row) {
        return (int)$row['id_admin'] === (int)$_SESSION['id_admin'] ? 'Anda tidak bisa menghapus akun yang sedang dipakai.' : true;
    },
    'columns' => [['username', 'Username'], ['nama_lengkap', 'Nama'], ['email', 'Email'], ['status', 'Status', 'badge'], ['created_at', 'Dibuat', 'date']],
    'order' => 'id_admin',
]);
