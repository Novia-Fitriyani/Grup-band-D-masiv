<?php
// Kumpulan query yang dipakai bersama oleh beberapa endpoint (beranda.php, event.php, dst.)
require_once __DIR__ . '/_bootstrap.php';

function data_event(PDO $pdo, $limit = 0) {
    $sql = "SELECT e.id_event, e.judul, e.lokasi, e.tanggal_event, e.waktu_mulai, e.deskripsi,
                   k.id_kategori, k.nama_kategori
            FROM event e JOIN kategori_event k ON k.id_kategori = e.id_kategori
            ORDER BY e.tanggal_event DESC, e.id_event DESC" . ($limit ? " LIMIT " . (int)$limit : "");
    return array_map(fn($r) => [
        'id'        => (int)$r['id_event'],
        'judul'     => $r['judul'],
        'lokasi'    => $r['lokasi'],
        'tanggal'   => $r['tanggal_event'],
        'waktu'     => $r['waktu_mulai'],
        'deskripsi' => $r['deskripsi'],
        'kategori_id'   => (int)$r['id_kategori'],
        'kategori_nama' => $r['nama_kategori'],
    ], $pdo->query($sql)->fetchAll());
}

function data_kategori_event(PDO $pdo) {
    $sql = "SELECT k.id_kategori, k.nama_kategori, COUNT(e.id_event) AS jumlah
            FROM kategori_event k LEFT JOIN event e ON e.id_kategori = k.id_kategori
            GROUP BY k.id_kategori, k.nama_kategori ORDER BY k.id_kategori";
    return array_map(fn($r) => ['id' => (int)$r['id_kategori'], 'nama' => $r['nama_kategori'], 'jumlah' => (int)$r['jumlah']],
        $pdo->query($sql)->fetchAll());
}

function data_galeri(PDO $pdo, $tipe = null, $limit = 0) {
    $sql = "SELECT id_galeri, tipe, judul, tanggal, file_thumbnail, url_video, status_aktif FROM galeri"
         . ($tipe ? " WHERE tipe = :t" : "") . " ORDER BY tanggal DESC, id_galeri DESC" . ($limit ? " LIMIT " . (int)$limit : "");
    $st = $pdo->prepare($sql);
    $st->execute($tipe ? [':t' => $tipe] : []);
    return array_map(fn($r) => [
        'id'        => (int)$r['id_galeri'],
        'tipe'      => $r['tipe'],
        'judul'     => $r['judul'],
        'tanggal'   => $r['tanggal'],
        'thumbnail' => api_url_file($r['file_thumbnail']),
        'url_video' => api_url_link($r['url_video']),
        'tampil_footer' => $r['status_aktif'] === 'tampil',
    ], $st->fetchAll());
}

function data_album(PDO $pdo, $limit = 0) {
    $sql = "SELECT id_album, judul_album, tanggal_rilis, cover, link_spotify FROM album
            ORDER BY tanggal_rilis DESC, id_album ASC" . ($limit ? " LIMIT " . (int)$limit : "");
    $album = $pdo->query($sql)->fetchAll();
    $lagu = [];
    if ($album) {
        $ids = implode(',', array_map('intval', array_column($album, 'id_album')));
        $q = $pdo->query("SELECT id_album, judul_lagu, no_urut, link_spotify FROM lagu
                          WHERE id_album IN ($ids) ORDER BY id_album, (no_urut IS NULL), no_urut, id_lagu");
        foreach ($q as $l) {
            $lagu[$l['id_album']][] = ['judul' => $l['judul_lagu'], 'no_urut' => $l['no_urut'] === null ? null : (int)$l['no_urut'],
                                       'link_spotify' => api_url_link($l['link_spotify'])];
        }
    }
    return array_map(fn($a) => [
        'id'           => (int)$a['id_album'],
        'judul'        => $a['judul_album'],
        'tanggal_rilis'=> $a['tanggal_rilis'],
        'cover'        => api_url_file($a['cover']),
        'link_spotify' => api_url_link($a['link_spotify']),
        'lagu'         => $lagu[$a['id_album']] ?? [],
    ], $album);
}

function data_produk(PDO $pdo, $limit = 0) {
    $sql = "SELECT id_produk, nama_produk, deskripsi, harga, gambar, link_beli, status FROM produk
            ORDER BY (status = 'tersedia') DESC, id_produk ASC" . ($limit ? " LIMIT " . (int)$limit : "");
    return array_map(fn($r) => [
        'id'        => (int)$r['id_produk'],
        'nama'      => $r['nama_produk'],
        'deskripsi' => $r['deskripsi'],
        'harga'     => (int)$r['harga'],
        'gambar'    => api_url_file($r['gambar']),
        'link_beli' => api_url_link($r['link_beli']),
        'tersedia'  => $r['status'] === 'tersedia',
    ], $pdo->query($sql)->fetchAll());
}

function data_personel(PDO $pdo) {
    $sql = "SELECT id_personel, nama_lengkap, nama_panggilan, peran, bio, foto, medsos_anggota
            FROM personel ORDER BY (urutan IS NULL), urutan, id_personel";
    return array_map(fn($r) => [
        'id'       => (int)$r['id_personel'],
        'nama_lengkap'   => $r['nama_lengkap'],
        'nama_panggilan' => $r['nama_panggilan'],
        'peran'    => $r['peran'],
        'bio'      => $r['bio'],
        'foto'     => api_url_file($r['foto']),
        'medsos'   => api_url_link($r['medsos_anggota']),
    ], $pdo->query($sql)->fetchAll());
}

// Data yang tampil di semua halaman: Follow Us (topbar), footer (sosmed, kontak, partner, latest video)
function data_situs(PDO $pdo) {
    $sosmed = array_map(fn($r) => ['platform' => $r['platform'], 'url' => api_url_link($r['url']), 'ikon' => $r['ikon']],
        $pdo->query("SELECT platform, url, ikon FROM media_sosial ORDER BY id_sosmed")->fetchAll());
    $kontak = array_map(fn($r) => ['nama' => $r['nama_kontak'], 'telepon' => $r['no_telepon']],
        $pdo->query("SELECT nama_kontak, no_telepon, MIN(id_sosmed) AS urut FROM media_sosial
                     GROUP BY nama_kontak, no_telepon ORDER BY urut")->fetchAll());
    $mitra = array_map(fn($r) => ['nama' => $r['nama_mitra'], 'url' => api_url_link($r['url'])],
        $pdo->query("SELECT nama_mitra, url FROM mitra ORDER BY id_mitra")->fetchAll());
    $v = $pdo->query("SELECT id_galeri, tipe, judul, tanggal, file_thumbnail, url_video, status_aktif FROM galeri
                      WHERE tipe = 'video' AND status_aktif = 'tampil' ORDER BY tanggal DESC, id_galeri DESC LIMIT 1")->fetch();
    $video = $v ? ['judul' => $v['judul'], 'thumbnail' => api_url_file($v['file_thumbnail']), 'url_video' => api_url_link($v['url_video'])] : null;
    return ['media_sosial' => $sosmed, 'kontak' => $kontak, 'mitra' => $mitra, 'latest_video' => $video];
}
