<?php
require_once __DIR__ . "/../config/koneksi_mysqli.php";

// Tabel ini sudah ikut terbuat saat dmasiv_database.sql dijalankan; file ini hanya untuk membuat satu tabel saja.

// sql to create table
$sql = "CREATE TABLE personel (
  id_personel    INT          NOT NULL AUTO_INCREMENT,
  nama_lengkap   VARCHAR(100) NOT NULL,
  nama_panggilan VARCHAR(50)  NOT NULL,
  peran          VARCHAR(30)  NOT NULL COMMENT 'Vokal, Gitar, Bass, Drum',
  bio            TEXT         NULL,
  foto           VARCHAR(255) NOT NULL,
  urutan         INT          NULL COMMENT 'Urutan tampil di halaman',
  medsos_anggota VARCHAR(255) NOT NULL,
  id_admin       INT          NOT NULL,
  PRIMARY KEY (id_personel),
  CONSTRAINT fk_personel_admin FOREIGN KEY (id_admin)
    REFERENCES admin (id_admin) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if (mysqli_query($conn, $sql)) {
    echo "Table <strong>personel</strong> created successfully";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}

mysqli_close($conn);
?>