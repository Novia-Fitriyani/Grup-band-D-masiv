<?php
require_once __DIR__ . "/../config/koneksi_mysqli.php";

// Tabel ini sudah ikut terbuat saat dmasiv_database.sql dijalankan; file ini hanya untuk membuat satu tabel saja.

// sql to create table
$sql = "CREATE TABLE fans (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if (mysqli_query($conn, $sql)) {
    echo "Table <strong>fans</strong> created successfully";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}

mysqli_close($conn);
?>