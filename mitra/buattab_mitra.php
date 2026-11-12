<?php
require_once __DIR__ . "/../config/koneksi_mysqli.php";

// Tabel ini sudah ikut terbuat saat dmasiv_database.sql dijalankan; file ini hanya untuk membuat satu tabel saja.

// sql to create table
$sql = "CREATE TABLE mitra (
  id_mitra   INT          NOT NULL AUTO_INCREMENT,
  nama_mitra VARCHAR(50)  NOT NULL,
  url        VARCHAR(255) NULL,
  id_admin   INT          NOT NULL,
  PRIMARY KEY (id_mitra),
  CONSTRAINT fk_mitra_admin FOREIGN KEY (id_admin)
    REFERENCES admin (id_admin) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if (mysqli_query($conn, $sql)) {
    echo "Table <strong>mitra</strong> created successfully";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}

mysqli_close($conn);
?>