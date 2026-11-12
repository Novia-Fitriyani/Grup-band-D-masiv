-- =====================================================================
-- Basis data website D'MASIV (11 tabel)
-- Kebutuhan: MySQL 8.0.16+ (CHECK constraint dijalankan mulai versi ini)
-- Engine InnoDB, charset utf8mb4
-- Urutan pembuatan: tabel induk lebih dulu, tabel yang punya FK belakangan
-- =====================================================================

CREATE DATABASE IF NOT EXISTS dmasiv_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE dmasiv_db;

-- ---------------------------------------------------------------------
-- 1. admin
-- ---------------------------------------------------------------------
CREATE TABLE admin (
  id_admin     INT          NOT NULL AUTO_INCREMENT,
  username     VARCHAR(50)  NOT NULL,
  password     VARCHAR(255) NOT NULL COMMENT 'Hash kata sandi, bukan teks asli',
  nama_lengkap VARCHAR(100) NOT NULL,
  email        VARCHAR(100) NOT NULL,
  status       ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif'
               COMMENT 'Akun nonaktif tidak bisa login',
  created_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_admin),
  UNIQUE KEY uq_admin_username (username),
  UNIQUE KEY uq_admin_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 2. kategori_event
-- ---------------------------------------------------------------------
CREATE TABLE kategori_event (
  id_kategori   INT         NOT NULL AUTO_INCREMENT,
  nama_kategori VARCHAR(50) NOT NULL,
  PRIMARY KEY (id_kategori),
  UNIQUE KEY uq_kategori_nama (nama_kategori)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 3. fans (berdiri sendiri; alamat berupa teks, Opsi A)
-- ---------------------------------------------------------------------
CREATE TABLE fans (
  id_fans        INT          NOT NULL AUTO_INCREMENT,
  nama_lengkap   VARCHAR(100) NOT NULL,
  jenis_kelamin  ENUM('Pria','Wanita') NOT NULL,
  tanggal_lahir  DATE         NOT NULL,
  alamat         TEXT         NOT NULL COMMENT 'Alamat lengkap fans',
  email          VARCHAR(100) NOT NULL,
  no_telepon     VARCHAR(20)  NOT NULL,
  url_sosmed     VARCHAR(255) NULL,
  tanggal_daftar DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_fans),
  UNIQUE KEY uq_fans_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 4. album
-- ---------------------------------------------------------------------
CREATE TABLE album (
  id_album      INT          NOT NULL AUTO_INCREMENT,
  judul_album   VARCHAR(150) NOT NULL,
  tanggal_rilis DATE         NOT NULL,
  cover         VARCHAR(255) NOT NULL COMMENT 'Path file gambar cover',
  link_spotify  VARCHAR(255) NULL,
  id_admin      INT          NOT NULL,
  PRIMARY KEY (id_album),
  CONSTRAINT fk_album_admin FOREIGN KEY (id_admin)
    REFERENCES admin (id_admin) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 5. personel
-- ---------------------------------------------------------------------
CREATE TABLE personel (
  id_personel    INT          NOT NULL AUTO_INCREMENT,
  nama_lengkap   VARCHAR(100) NOT NULL,
  nama_panggilan VARCHAR(50)  NOT NULL,
  peran          VARCHAR(30)  NOT NULL COMMENT 'Vokal, Gitar, Bass, Drum',
  bio            TEXT         NULL,
  foto           VARCHAR(255) NOT NULL,
  urutan         INT          NULL COMMENT 'Urutan tampil di halaman',
  medsos_anggota VARCHAR(255) NOT NULL,
  id_admin       INT          NOT NULL,
  PRIMARY KEY (id_personel),
  CONSTRAINT fk_personel_admin FOREIGN KEY (id_admin)
    REFERENCES admin (id_admin) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 6. media_sosial
-- Catatan: nama_kontak dan no_telepon dibuat NOT NULL sesuai dokumen.
-- Jika ingin boleh kosong, jalankan:
--   ALTER TABLE media_sosial MODIFY nama_kontak VARCHAR(50) NULL,
--                            MODIFY no_telepon  VARCHAR(20) NULL;
-- ---------------------------------------------------------------------
CREATE TABLE media_sosial (
  id_sosmed   INT          NOT NULL AUTO_INCREMENT,
  platform    VARCHAR(30)  NOT NULL COMMENT 'Facebook, Twitter, Instagram, YouTube',
  url         VARCHAR(255) NOT NULL,
  ikon        VARCHAR(50)  NULL COMMENT 'Nama class ikon Font Awesome',
  nama_kontak VARCHAR(50)  NOT NULL,
  no_telepon  VARCHAR(20)  NOT NULL,
  id_admin    INT          NOT NULL,
  PRIMARY KEY (id_sosmed),
  CONSTRAINT fk_sosmed_admin FOREIGN KEY (id_admin)
    REFERENCES admin (id_admin) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 7. mitra
-- ---------------------------------------------------------------------
CREATE TABLE mitra (
  id_mitra   INT          NOT NULL AUTO_INCREMENT,
  nama_mitra VARCHAR(50)  NOT NULL,
  url        VARCHAR(255) NULL,
  id_admin   INT          NOT NULL,
  PRIMARY KEY (id_mitra),
  CONSTRAINT fk_mitra_admin FOREIGN KEY (id_admin)
    REFERENCES admin (id_admin) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 8. event
-- ---------------------------------------------------------------------
CREATE TABLE event (
  id_event      INT          NOT NULL AUTO_INCREMENT,
  id_kategori   INT          NOT NULL,
  judul         VARCHAR(150) NOT NULL,
  lokasi        VARCHAR(100) NOT NULL,
  tanggal_event DATE         NOT NULL,
  waktu_mulai   TIME         NULL,
  deskripsi     TEXT         NULL,
  id_admin      INT          NOT NULL,
  created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_event),
  CONSTRAINT fk_event_kategori FOREIGN KEY (id_kategori)
    REFERENCES kategori_event (id_kategori) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_event_admin FOREIGN KEY (id_admin)
    REFERENCES admin (id_admin) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 9. galeri
-- ---------------------------------------------------------------------
CREATE TABLE galeri (
  id_galeri      INT          NOT NULL AUTO_INCREMENT,
  tipe           ENUM('foto','video') NOT NULL,
  judul          VARCHAR(150) NOT NULL,
  tanggal        DATE         NOT NULL,
  file_thumbnail VARCHAR(255) NOT NULL,
  url_video      VARCHAR(255) NULL COMMENT 'Tautan YouTube, hanya bila tipe = video',
  status_aktif   ENUM('tampil','arsip') NOT NULL DEFAULT 'arsip'
                 COMMENT 'tampil = muncul di Latest Video footer',
  id_admin       INT          NOT NULL,
  created_at     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id_galeri),
  CONSTRAINT chk_galeri_url_video CHECK (tipe = 'video' OR url_video IS NULL),
  CONSTRAINT fk_galeri_admin FOREIGN KEY (id_admin)
    REFERENCES admin (id_admin) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 10. lagu
-- ---------------------------------------------------------------------
CREATE TABLE lagu (
  id_lagu      INT          NOT NULL AUTO_INCREMENT,
  id_album     INT          NOT NULL,
  judul_lagu   VARCHAR(150) NOT NULL,
  no_urut      INT          NULL COMMENT 'Nomor urut lagu dalam album',
  link_spotify VARCHAR(255) NULL,
  id_admin     INT          NOT NULL,
  PRIMARY KEY (id_lagu),
  UNIQUE KEY uq_lagu_album_urut (id_album, no_urut),
  CONSTRAINT fk_lagu_album FOREIGN KEY (id_album)
    REFERENCES album (id_album) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_lagu_admin FOREIGN KEY (id_admin)
    REFERENCES admin (id_admin) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 11. produk
-- ---------------------------------------------------------------------
CREATE TABLE produk (
  id_produk   INT          NOT NULL AUTO_INCREMENT,
  nama_produk VARCHAR(100) NOT NULL,
  deskripsi   TEXT         NULL,
  harga       INT          NOT NULL COMMENT 'Harga dalam rupiah',
  gambar      VARCHAR(255) NOT NULL,
  link_beli   VARCHAR(255) NOT NULL COMMENT 'Tautan Buy Now ke marketplace',
  status      ENUM('tersedia','habis') NOT NULL DEFAULT 'tersedia',
  id_admin    INT          NOT NULL,
  PRIMARY KEY (id_produk),
  CONSTRAINT fk_produk_admin FOREIGN KEY (id_admin)
    REFERENCES admin (id_admin) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
