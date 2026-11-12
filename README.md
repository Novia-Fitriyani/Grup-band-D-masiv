# D'MASIV Admin (AdminLTE 4 + PHP native)

## Cara menjalankan
1. Pasang XAMPP atau Laragon (PHP 8+, MySQL 8.0.16+).
2. Salin folder `dmasiv-admin` ke `htdocs` (XAMPP) atau `www` (Laragon).
3. Buka phpMyAdmin, jalankan `dmasiv_database.sql`, lalu `data_awal.sql`.
4. Sesuaikan `config/koneksi.php` (user, password, dan BASE_URL jika nama folder berbeda).
5. Buka `http://localhost/dmasiv-admin/buat_admin.php`, buat admin pertama, lalu HAPUS file itu.
6. Masuk lewat `http://localhost/dmasiv-admin/login.php`.

Perlu internet saat membuka halaman, karena AdminLTE dimuat dari CDN (jsDelivr).

## Struktur
- config/koneksi.php : koneksi database (PDO), sesi, fungsi bantu
- includes/          : auth, header, sidebar, footer (layout bersama)
- login.php, logout.php, index.php (dashboard)
- event/             : modul CRUD contoh (daftar, tambah, ubah, hapus)

## Membuat modul baru
Salin folder `event/`, ganti nama tabel dan kolom, lalu ubah 'ada' => true di includes/sidebar.php.
