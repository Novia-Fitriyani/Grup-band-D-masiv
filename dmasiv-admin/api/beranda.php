<?php
// Satu permintaan untuk seluruh pratinjau di halaman beranda
require_once __DIR__ . '/_data.php';
api_jalankan(function () use ($pdo) {
    api_kirim([
        'galeri'   => data_galeri($pdo, 'foto', 3),
        'album'    => data_album($pdo, 3),
        'produk'   => data_produk($pdo, 4),
        'event'    => data_event($pdo, 4),
        'personel' => data_personel($pdo),
    ]);
});
