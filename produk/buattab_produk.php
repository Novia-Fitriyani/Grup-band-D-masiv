<?php
require_once __DIR__ . "/../config/koneksi_mysqli.php";

// Tabel ini sudah ikut terbuat saat dmasiv_database.sql dijalankan; file ini hanya untuk membuat satu tabel saja.

// sql to create table
$sql = "CREATE TABLE produk (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if (mysqli_query($conn, $sql)) {
    echo "Table <strong>produk</strong> created successfully";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}

mysqli_close($conn);
?>