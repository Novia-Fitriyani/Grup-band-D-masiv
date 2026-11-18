<?php
require_once __DIR__ . "/../config/koneksi_mysqli.php";

// Tabel ini sudah ikut terbuat saat dmasiv_database.sql dijalankan; file ini hanya untuk membuat satu tabel saja.

// sql to create table
$sql = "CREATE TABLE galeri (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if (mysqli_query($conn, $sql)) {
    echo "Table <strong>galeri</strong> created successfully";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}

mysqli_close($conn);
?>