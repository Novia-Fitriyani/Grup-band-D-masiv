<?php
require_once __DIR__ . "/../config/koneksi_mysqli.php";

// Tabel ini sudah ikut terbuat saat dmasiv_database.sql dijalankan; file ini hanya untuk membuat satu tabel saja.

// sql to create table
$sql = "CREATE TABLE kategori_event (
  id_kategori   INT         NOT NULL AUTO_INCREMENT,
  nama_kategori VARCHAR(50) NOT NULL,
  PRIMARY KEY (id_kategori),
  UNIQUE KEY uq_kategori_nama (nama_kategori)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if (mysqli_query($conn, $sql)) {
    echo "Table <strong>kategori_event</strong> created successfully";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}

mysqli_close($conn);
?>