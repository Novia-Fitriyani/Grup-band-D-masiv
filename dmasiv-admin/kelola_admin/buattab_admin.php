<?php
require_once __DIR__ . "/../config/koneksi_mysqli.php";

// Tabel ini sudah ikut terbuat saat dmasiv_database.sql dijalankan; file ini hanya untuk membuat satu tabel saja.

// sql to create table
$sql = "CREATE TABLE admin (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if (mysqli_query($conn, $sql)) {
    echo "Table <strong>admin</strong> created successfully";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}

mysqli_close($conn);
?>