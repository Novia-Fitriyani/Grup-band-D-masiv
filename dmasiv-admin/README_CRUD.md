# CRUD D'MASIV (pola mahasiswa(crud))

Semua tabel di `dmasiv_db` kini punya CRUD dengan susunan file yang sama seperti `mahasiswa(crud)`.
File lama (`index.php`, `form.php`, `crud.php`) **tidak dihapus**; yang diubah hanya `includes/sidebar.php`
(menu) dan `includes/crud.php` (hanya nama folder gambar).

## 1. Peta file (tiap modul punya 7 file)

| mahasiswa(crud)           | Modul baru (contoh: event)  | Fungsi                                   |
|---------------------------|-----------------------------|------------------------------------------|
| `buattab_mhsw.php`        | `buattab_event.php`         | membuat tabel (persis dari SQL asli)     |
| `bacanilaimahasiswa.php`  | `bacaevent.php`             | daftar data + tombol Edit / Delete       |
| `form_nilaimhsw.html`     | `form_event.php`            | form tambah *(tidak ada di rar, dibuat)* |
| (tidak ada)               | `simpanevent.php`           | proses INSERT *(tidak ada di rar, dibuat)* |
| `form_editnilaimhsw.php`  | `form_editevent.php`        | form edit                                |
| `updatenilaimhsw.php`     | `updateevent.php`           | proses UPDATE                            |
| `hapusnilaimhsw.php`      | `hapusevent.php`            | proses DELETE                            |
| `koneksi.php`             | `config/koneksi_mysqli.php` | koneksi (satu untuk semua modul)         |

Tabel -> folder -> akhiran nama file:
`kategori_event`->`kategori_event/`->`kategorievent` · `admin`->`kelola_admin/`->`admin` · `fans`->`fans/`->`fans` ·
`album`->`album/`->`album` · `lagu`->`lagu/`->`lagu` · `personel`->`personel/`->`personel` ·
`media_sosial`->`media_sosial/`->`mediasosial` · `mitra`->`mitra/`->`mitra` · `event`->`event/`->`event` ·
`galeri`->`galeri/`->`galeri` · `produk`->`produk/`->`produk`

## 2. Arti tanda di dalam kode

Cari teks berikut (Ctrl+Shift+F) untuk menemukan semua perubahan:

- `← TAMBAH` : baris baru yang tidak ada di mahasiswa(crud)
- `← UBAH`   : baris yang diubah (di komentar ditulis "dulu ...")
- `[RELASI]` : penghubung antar modul/tabel. **Jangan dihapus**, modul lain bergantung padanya
- `[KEAMANAN]` : perbaikan keamanan (prepared statement, token CSRF, hapus lewat POST)
- `[UPLOAD]` : penanganan gambar (album, personel, galeri, produk)

## 3. Yang saling terhubung (`[RELASI]`)

- **Login**: semua file memuat `includes/auth.php`, jadi wajib login.
- **id_admin**: tabel album, personel, media_sosial, mitra, event, galeri, lagu, produk memiliki kolom `id_admin`
  (foreign key). Saat tambah data, kolom ini diisi otomatis dari akun yang sedang login.
- **kategori_event -> event**: dropdown Kategori pada form event diisi dari tabel `kategori_event`. Kategori yang
  masih dipakai event tidak bisa dihapus (muncul pesan, bukan error).
- **album -> lagu**: tombol "Kelola Lagu" pada daftar album membuka `lagu/bacalagu.php?album=ID`. Menghapus album
  ikut menghapus lagunya (`ON DELETE CASCADE`); kotak konfirmasi sudah memberi peringatan.
- **admin**: akun yang sedang login tidak bisa dihapus/dinonaktifkan sendiri. Admin yang sudah pernah menambah
  konten tidak bisa dihapus (ubah statusnya menjadi Nonaktif).
- **Layout & menu**: `header.php`/`footer.php`/`sidebar.php` dipakai bersama. Menu Kategori Event ditambahkan.

## 4. Upload gambar (mengikuti contoh upload pengguna)

Berlaku di `simpan*.php` dan `update*.php` milik album (cover), personel (foto), galeri (thumbnail), produk (gambar).

1. Checkpoint 1: file harus gambar asli (`getimagesize`)
2. Checkpoint 2: nama file tidak boleh sudah ada di folder tujuan
3. Checkpoint 3: ukuran maksimal 1 MB
4. Checkpoint 4: ekstensi hanya jpg, jpeg, png, gif

File disimpan di `gambar/<modul>/` (`gambar/album/`, `gambar/personel/`, `gambar/galeri/`, `gambar/produk/`),
dibuat otomatis. Database hanya menyimpan path-nya, contoh `gambar/album/cover1.jpg`.
Contoh pengguna memakai folder `galeri/`, tetapi di proyek ini `galeri/` berisi kode modul, jadi gambar dipisah ke `gambar/`.
Untuk mengganti folder, ubah baris `$target_dir = "gambar/album/";` di file `simpan*.php` dan `update*.php` modul terkait.
Saat edit, tidak memilih file berarti gambar lama dipertahankan; bila diganti, file lama ikut dihapus.
Catatan: karena checkpoint 2, mengunggah file dengan nama yang sama dengan yang sudah ada akan ditolak (ganti nama file).

## 5. Perbedaan dari mahasiswa(crud) dan alasannya

| Di mahasiswa(crud)                                         | Di modul baru                                              |
|------------------------------------------------------------|------------------------------------------------------------|
| `koneksi.php` mencetak "Connected successfully"            | dihapus; teks itu membuat `header('Location')` gagal       |
| SQL memakai variabel langsung (`'$_GET[nim]'`)             | `mysqli_prepare` (mencegah SQL injection)                  |
| Hapus lewat link GET                                       | tombol form POST + token CSRF                              |
| `hapusnilaimhsw.php` menjalankan DELETE 2x, `<script>` tidak tertutup | DELETE sekali, pesan lewat flash        |
| Redirect ke `adminlte/dist/adminlte.php` (tidak ada)       | kembali ke `baca*.php` + pesan berhasil/gagal              |
| Isian tidak divalidasi                                     | divalidasi; bila salah kembali ke form dengan isian tetap  |
| `edit_mhsw.php` (versi lama, kirim ke file yang tidak ada) | tidak ditiru                                               |

## 6. Cara pakai

1. Salin folder `dmasiv-admin` ke `htdocs`. Jika `config/koneksi.php` Anda sudah disesuaikan, biarkan (tidak diubah).
2. Pastikan `dmasiv_database.sql` dan `data_awal.sql` sudah dijalankan.
3. Buat satu akun admin di tabel `admin` (hash sandi lewat `password_hash`), lalu masuk lewat `login.php`.
4. Urutan uji: Kategori Event -> Event -> Album -> Kelola Lagu -> Galeri -> Produk -> Personel -> Media Sosial ->
   Mitra -> Data Fans -> Kelola Admin.

## 7. Catatan penting

- Kode ini **belum dijalankan** langsung (penyusun tidak punya PHP). Sintaks sudah diperiksa dengan alat bantu dan
  ditinjau, tetapi uji dulu di XAMPP.
- `README.md` lama menyebut `buat_admin.php`, tetapi file itu tidak ada di rar.
- Modul `fans` lama punya pencarian, halaman, dan ekspor CSV; versi baru mengikuti pola mahasiswa(crud).

## 8. Pencarian & filter (`baca*.php`)

Kotak cari dan dropdown ada di kanan judul kartu, sebelum tombol "+ Add" (seperti referensi). Pencarian memakai
alamat `?cari=...&filter=...`, jadi hasilnya bisa dibookmark. Tombol Reset muncul saat pencarian/filter aktif.
Cari kata dicocokkan sebagian (LIKE) dan tidak membedakan huruf besar/kecil.

| Modul   | Kotak cari berdasarkan | Dropdown filter                          |
|---------|------------------------|------------------------------------------|
| event   | judul                  | lokasi (diambil dari data event)         |
| galeri  | judul                  | jenis (Foto / Video)                     |
| album   | judul                  | (tidak ada)                              |
| lagu    | judul                  | (tidak ada)                              |
| produk  | nama produk            | status (Tersedia / Habis)                |
| fans    | nama                   | alamat (diambil dari data fans)          |

Cari tanda `[CARI]` di kode untuk menemukan semua bagian pencarian.
