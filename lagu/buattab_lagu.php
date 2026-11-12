<?php
require_once __DIR__ . "/../config/koneksi_mysqli.php";

// Tabel ini sudah ikut terbuat saat dmasiv_database.sql dijalankan; file ini hanya untuk membuat satu tabel saja.

// sql to create table
$sql = "CREATE TABLE lagu (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if (mysqli_query($conn, $sql)) {
    echo "Table <strong>lagu</strong> created successfully";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}

mysqli_close($conn);
?>