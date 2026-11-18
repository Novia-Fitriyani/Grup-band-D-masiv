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

## API publik untuk frontend (folder `api/`)
Frontend membaca konten dari database lewat endpoint JSON berikut (tanpa login, hanya-baca, kecuali `fans.php`):

| Endpoint            | Isi                                                              | Tabel                              |
|---------------------|------------------------------------------------------------------|------------------------------------|
| `api/situs.php`     | Follow Us, kontak, partner, Latest Video (topbar + footer)       | media_sosial, mitra, galeri        |
| `api/beranda.php`   | Pratinjau beranda (galeri, album, produk, event, personel)       | galeri, album, produk, event, personel |
| `api/event.php`     | Kategori + jumlah event dan seluruh event                        | kategori_event, event              |
| `api/galeri.php`    | Foto dan video                                                   | galeri                             |
| `api/album.php`     | Album beserta daftar lagu                                        | album, lagu                        |
| `api/produk.php`    | Produk shop (status `habis` tampil SOLD OUT)                     | produk                             |
| `api/personel.php`  | Anggota band                                                     | personel                           |
| `api/fans.php`      | **POST** JSON, pendaftaran fans                                  | fans                               |

Catatan: file berawalan `_` adalah file bantu. Path gambar di database (mis. `gambar/album/x.jpg`) diubah API menjadi URL lengkap.
`database/dmasiv_database.sql` dan `database/data_awal.sql` dibuat dari `buattab_*.php` dan konten frontend lama.
