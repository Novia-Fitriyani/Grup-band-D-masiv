<?php
require_once __DIR__ . "/../config/koneksi_mysqli.php";

// Tabel ini sudah ikut terbuat saat dmasiv_database.sql dijalankan; file ini hanya untuk membuat satu tabel saja.

// sql to create table
$sql = "CREATE TABLE event (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if (mysqli_query($conn, $sql)) {
    echo "Table <strong>event</strong> created successfully";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}

mysqli_close($conn);
?>