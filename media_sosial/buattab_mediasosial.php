<?php
require_once __DIR__ . "/../config/koneksi_mysqli.php";

// Tabel ini sudah ikut terbuat saat dmasiv_database.sql dijalankan; file ini hanya untuk membuat satu tabel saja.

// sql to create table
$sql = "CREATE TABLE media_sosial (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if (mysqli_query($conn, $sql)) {
    echo "Table <strong>media_sosial</strong> created successfully";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}

mysqli_close($conn);
?>