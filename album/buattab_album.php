<?php
require_once __DIR__ . "/../config/koneksi_mysqli.php";

// Tabel ini sudah ikut terbuat saat dmasiv_database.sql dijalankan; file ini hanya untuk membuat satu tabel saja.

// sql to create table
$sql = "CREATE TABLE album (
  id_album      INT          NOT NULL AUTO_INCREMENT,
  judul_album   VARCHAR(150) NOT NULL,
  tanggal_rilis DATE         NOT NULL,
  cover         VARCHAR(255) NOT NULL COMMENT 'Path file gambar cover',
  link_spotify  VARCHAR(255) NULL,
  id_admin      INT          NOT NULL,
  PRIMARY KEY (id_album),
  CONSTRAINT fk_album_admin FOREIGN KEY (id_admin)
    REFERENCES admin (id_admin) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if (mysqli_query($conn, $sql)) {
    echo "Table <strong>album</strong> created successfully";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}

mysqli_close($conn);
?>