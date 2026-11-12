-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 12, 2026 at 11:39 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dmasiv_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL COMMENT 'Hash kata sandi, bukan teks asli',
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif' COMMENT 'Akun nonaktif tidak bisa login',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `username`, `password`, `nama_lengkap`, `email`, `status`, `created_at`) VALUES
(1, '@dmasiv', '$2y$10$/iTrWfo5m3qJRcp2.RcAP.PuETSOxo//uYsTQTi5.J4j5gU8aO80a', 'dmasiv', 'dmasiv@gmail.com', 'aktif', '2026-11-04 11:28:39');

-- --------------------------------------------------------

--
-- Table structure for table `album`
--

CREATE TABLE `album` (
  `id_album` int(11) NOT NULL,
  `judul_album` varchar(150) NOT NULL,
  `tanggal_rilis` date NOT NULL,
  `cover` varchar(255) NOT NULL COMMENT 'Path file gambar cover',
  `link_spotify` varchar(255) DEFAULT NULL,
  `id_admin` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `album`
--

INSERT INTO `album` (`id_album`, `judul_album`, `tanggal_rilis`, `cover`, `link_spotify`, `id_admin`) VALUES
(1, 'INGIN LEKAS MEMELUKMU LAGI', '2018-10-08', 'gambar/album/lagu_pertama.jpg', 'https://open.spotify.com/track/40r31knlz4HIx2hbs55dh9?si=f717c7251b3c4b9a', 1),
(3, 'SELAMAT JALAN KASIH', '2018-08-24', 'gambar/album/lagu_kedua.jpg', 'https://open.spotify.com/track/1mjTITSFnucqtdkDa7rDGM?si=2bb2ffaaf3114ec7', 1),
(4, 'PERNAH MEMILIKI', '2018-03-05', 'gambar/album/lagu_ketiga.jpg', 'https://open.spotify.com/track/716VezD2uWIUB680aEYzcs?si=dbe5f2ee096d4a4f', 1),
(5, 'Dibawah Langit Yang Sama', '2017-08-05', 'gambar/album/lagu_keempat.jpg', 'https://open.spotify.com/track/1GD3qlRikNoTU3zHQZ5YsQ?si=cf72057f62e6453a', 1),
(6, 'D\'MASIV WITH RAEF', '2016-05-29', 'gambar/album/lagu_kelima.png', 'https://open.spotify.com/album/3ZZLSlNhsDYCUHhMtbyd9p?si=5g7GrdvcRk2Gi9K0JBo8Jw', 1),
(7, 'HIDUP LEBIH INDAH', '2014-07-12', 'gambar/album/lagu_keenam.jpg', 'https://open.spotify.com/album/43q5YRL1xn8ngZoGa7eIgP?si=tgDeswSwTKOzqjkt9oEnxg', 1),
(8, 'PERSIAPAN', '2012-02-26', 'gambar/album/lagu_ketujuh.jpg', 'https://open.spotify.com/album/3Q6oW24ccQhsfLE7PEW36v?si=NnD_XqKvTc6JtNZLfSPO3g', 1),
(9, 'PERJALANAN', '2009-12-14', 'gambar/album/lagu_kedelapan.jpg', 'https://open.spotify.com/album/1ygbAOzcTsfP2JCBOYIUrT?si=hDNyCt9KSEus6mcXC0K9eA', 1),
(10, 'PERUBAHAN', '2008-09-24', 'gambar/album/lagu_kesembilan.jpg', 'https://open.spotify.com/album/6eDcL5Onn3OPEFnEfnsWRb?si=zp-jCOb3QgylrsdByt05TQ', 1);

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE `event` (
  `id_event` int(11) NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `judul` varchar(150) NOT NULL,
  `lokasi` varchar(100) NOT NULL,
  `tanggal_event` date NOT NULL,
  `waktu_mulai` time DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `id_admin` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `event`
--

INSERT INTO `event` (`id_event`, `id_kategori`, `judul`, `lokasi`, `tanggal_event`, `waktu_mulai`, `deskripsi`, `id_admin`, `created_at`) VALUES
(1, 1, 'Breakout NET', 'NET TV', '2018-09-21', '12:47:35', '', 1, '2026-11-11 12:09:45'),
(2, 2, 'Dmasiv event', 'Jombang', '2018-09-29', '13:48:00', NULL, 1, '2026-11-12 15:07:47'),
(3, 2, 'Dmasiv event', 'Puri Mall', '2019-02-03', '11:44:00', NULL, 1, '2026-11-12 15:09:41'),
(4, 2, 'Dmasiv event', 'Semarang', '2019-02-08', '11:45:00', NULL, 1, '2026-11-12 15:10:31'),
(5, 2, 'Smartfen Event', 'CFD', '2019-02-10', '11:46:00', NULL, 1, '2026-11-12 15:12:23'),
(6, 2, 'Dmasiv event', 'Atambua', '2019-09-03', '07:00:00', NULL, 1, '2026-11-12 15:13:06'),
(7, 2, 'Dmasiv event', 'Bali', '2019-03-16', '07:02:00', NULL, 1, '2026-11-12 15:14:38'),
(8, 2, 'Dmasiv event', 'Semarang', '2019-04-01', '10:10:00', NULL, 1, '2026-11-12 15:16:37'),
(9, 2, 'Dmasiv event', 'Bandung', '2019-04-06', '10:05:00', NULL, 1, '2026-11-12 15:17:49'),
(10, 2, 'Dmasiv event', 'Pali/Palembang', '2019-04-09', '11:05:00', NULL, 1, '2026-11-12 15:19:05'),
(11, 1, 'Breakout.Net', 'NET TV', '2018-09-21', '12:47:00', NULL, 1, '2026-11-12 15:20:40'),
(12, 1, 'Shooting Video Clip', 'Jakarta', '2018-09-24', '12:53:00', NULL, 1, '2026-11-12 15:22:12'),
(13, 3, 'Promo Radio', 'Jakarta', '2019-03-02', '02:01:00', NULL, 1, '2026-11-12 15:23:38'),
(14, 3, 'Promo Radio', 'Jawa Tengah', '2019-02-04', '10:04:00', NULL, 1, '2026-11-12 15:24:21'),
(15, 4, 'Press Conf Antalogi Rasa', 'Plaza Senayan', '2019-09-02', '11:47:00', NULL, 1, '2026-11-12 15:25:46');

-- --------------------------------------------------------

--
-- Table structure for table `fans`
--

CREATE TABLE `fans` (
  `id_fans` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `jenis_kelamin` enum('Pria','Wanita') NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `alamat` text NOT NULL COMMENT 'Alamat lengkap fans',
  `email` varchar(100) NOT NULL,
  `no_telepon` varchar(20) NOT NULL,
  `url_sosmed` varchar(255) DEFAULT NULL,
  `tanggal_daftar` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fans`
--

INSERT INTO `fans` (`id_fans`, `nama_lengkap`, `jenis_kelamin`, `tanggal_lahir`, `alamat`, `email`, `no_telepon`, `url_sosmed`, `tanggal_daftar`) VALUES
(1, 'Andi Wijaya', 'Pria', '2000-05-14', 'Jl. Ahmad Yani No. 12, Pontianak', 'andi.wijaya@gmail.com', '081234567801', 'https://www.instagram.com/andiwijaya', '2026-11-11 12:18:22');

-- --------------------------------------------------------

--
-- Table structure for table `galeri`
--

CREATE TABLE `galeri` (
  `id_galeri` int(11) NOT NULL,
  `tipe` enum('foto','video') NOT NULL,
  `judul` varchar(150) NOT NULL,
  `tanggal` date NOT NULL,
  `file_thumbnail` varchar(255) NOT NULL,
  `url_video` varchar(255) DEFAULT NULL COMMENT 'Tautan YouTube, hanya bila tipe = video',
  `status_aktif` enum('tampil','arsip') NOT NULL DEFAULT 'arsip' COMMENT 'tampil = muncul di Latest Video footer',
  `id_admin` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `galeri`
--

INSERT INTO `galeri` (`id_galeri`, `tipe`, `judul`, `tanggal`, `file_thumbnail`, `url_video`, `status_aktif`, `id_admin`, `created_at`) VALUES
(2, 'video', 'D\'Masiv - Dengarlah Sayang (Official Music Video)', '2016-09-28', 'gambar/galeri/foto_keenam.jpg', 'https://youtu.be/hGbT9yTfcG0?si=t2_r4ceGYmzbXgRA', 'tampil', 1, '2026-11-12 13:23:37'),
(3, 'video', 'D\'Masiv - Melodi', '2017-03-14', 'gambar/galeri/foto_ketujuh.jpg', 'https://youtu.be/Wiey4IfMPlA?si=4hmZJJuOh3C9o1cY', 'tampil', 1, '2026-11-12 13:25:47'),
(4, 'video', 'Umrah Bersama D\'MASIV dan Raef', '2017-04-11', 'gambar/galeri/foto_kedelapan.jpg', 'https://youtu.be/lfmUskY4SSc?si=jvP4p8BYlawVRFUk', 'tampil', 1, '2026-11-12 13:26:30'),
(5, 'video', 'D\'Masiv, Rossa Feat David Noah - Pernah Memiliki', '2018-04-11', 'gambar/galeri/foto_keempatbelas.jpg', 'https://youtu.be/q6FgMydW7X4?si=F6IOApUtJiW1ZA1x', 'tampil', 1, '2026-11-12 13:27:45'),
(6, 'video', 'Cerita Ramadhan : Pengalaman Pertama Puasa Rian D\'MASIV', '2017-05-30', 'gambar/galeri/foto_kesebelas.jpg', 'https://youtu.be/6n74yXlmI2A?si=HocO5rxK9sylgPVi', 'tampil', 1, '2026-11-12 13:29:48'),
(7, 'video', 'Cerita Ramadhan : Tips Puasa Ramadhan Rian D\'MASIV', '2017-05-30', 'gambar/galeri/foto__keduabelas.jpg', 'https://youtu.be/Pq9jQ7sUFeU?si=Hr37bW559BgU80RJ', 'tampil', 1, '2026-11-12 13:30:58'),
(8, 'foto', '#DMASIV5150', '2017-03-09', 'gambar/galeri/photo1.jpeg', NULL, 'arsip', 1, '2026-11-12 13:33:35'),
(9, 'foto', 'D\'MASIV Live di Kediri', '2017-05-13', 'gambar/galeri/gambar_kedua.jpeg', NULL, 'arsip', 1, '2026-11-12 13:35:29'),
(10, 'foto', 'D\'MASIV Live Salam Ramadhan 2017', '2017-05-30', 'gambar/galeri/gambar_ketiga.jpeg', NULL, 'arsip', 1, '2026-11-12 13:36:27'),
(11, 'foto', 'Salam Ramadhan 2017 – Bintaro Xchange', '2017-06-13', 'gambar/galeri/gambar_keempat.jpeg', NULL, 'arsip', 1, '2026-11-12 13:37:04'),
(12, 'foto', 'Bukber Musica Studio\'s', '2017-06-16', 'gambar/galeri/gambar_kelima.jpeg', NULL, 'arsip', 1, '2026-11-12 13:37:38'),
(13, 'foto', 'Countdown ASIAN GAMES 2018', '2018-08-20', 'gambar/galeri/foto_ketigabelas.jpg', NULL, 'arsip', 1, '2026-11-12 13:38:03');

-- --------------------------------------------------------

--
-- Table structure for table `kategori_event`
--

CREATE TABLE `kategori_event` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kategori_event`
--

INSERT INTO `kategori_event` (`id_kategori`, `nama_kategori`) VALUES
(2, 'Concert'),
(3, 'On Air Radio'),
(4, 'Others'),
(1, 'Release Promo');

-- --------------------------------------------------------

--
-- Table structure for table `lagu`
--

CREATE TABLE `lagu` (
  `id_lagu` int(11) NOT NULL,
  `id_album` int(11) NOT NULL,
  `judul_lagu` varchar(150) NOT NULL,
  `no_urut` int(11) DEFAULT NULL COMMENT 'Nomor urut lagu dalam album',
  `link_spotify` varchar(255) DEFAULT NULL,
  `id_admin` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lagu`
--

INSERT INTO `lagu` (`id_lagu`, `id_album`, `judul_lagu`, `no_urut`, `link_spotify`, `id_admin`) VALUES
(1, 1, 'Cinta Ini Membunuhku', 1, 'https://www.youtube.com/watch?v=iz7hDDlW8yQ', 1);

-- --------------------------------------------------------

--
-- Table structure for table `media_sosial`
--

CREATE TABLE `media_sosial` (
  `id_sosmed` int(11) NOT NULL,
  `platform` varchar(30) NOT NULL COMMENT 'Facebook, Twitter, Instagram, YouTube',
  `url` varchar(255) NOT NULL,
  `ikon` varchar(50) DEFAULT NULL COMMENT 'Nama class ikon Font Awesome',
  `nama_kontak` varchar(50) NOT NULL,
  `no_telepon` varchar(20) NOT NULL,
  `id_admin` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `media_sosial`
--

INSERT INTO `media_sosial` (`id_sosmed`, `platform`, `url`, `ikon`, `nama_kontak`, `no_telepon`, `id_admin`) VALUES
(3, 'Instagram', 'https://www.instagram.com/dmasiv?obrf=MWxwdjdhNW85N3RsdA==', 'fa-instagram', 'Ario', '6281388083303', 1),
(4, 'Facebook', 'https://www.facebook.com/dmasivband', 'f-Facebook', 'Sisil', '6287776041906', 1),
(5, 'Twitter', 'https://x.com/dmasiv', 'X', 'Ario', '6281388083303', 1),
(6, 'Youtube', 'https://music.youtube.com/playlist?list=OLAK5uy_nBdfzVh18bILidqt9XiUuVXT-MfwaDUhQ', 'Y-Youtube', 'Sisil', '6287776041906', 1);

-- --------------------------------------------------------

--
-- Table structure for table `mitra`
--

CREATE TABLE `mitra` (
  `id_mitra` int(11) NOT NULL,
  `nama_mitra` varchar(50) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `id_admin` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mitra`
--

INSERT INTO `mitra` (`id_mitra`, `nama_mitra`, `url`, `id_admin`) VALUES
(1, 'JOOX', 'http://www.joox.com/my/en/singer?id=350432', 1),
(2, 'iTunes', 'https://itunesmusicid.com/category/dmasiv', 1),
(3, 'Spotify', 'https://open.spotify.com/artist/1hioeMAsVwJ3bvcb9lxBpB?si=NUHpK6mmQoimim398vvH7w&utm_source=copy-link', 1),
(4, 'Nada Kita', 'https://music.youtube.com/playlist?list=OLAK5uy_nBdfzVh18bILidqt9XiUuVXT-MfwaDUhQ', 1);

-- --------------------------------------------------------

--
-- Table structure for table `personel`
--

CREATE TABLE `personel` (
  `id_personel` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `nama_panggilan` varchar(50) NOT NULL,
  `peran` varchar(30) NOT NULL COMMENT 'Vokal, Gitar, Bass, Drum',
  `bio` text DEFAULT NULL,
  `foto` varchar(255) NOT NULL,
  `urutan` int(11) DEFAULT NULL COMMENT 'Urutan tampil di halaman',
  `medsos_anggota` varchar(255) NOT NULL,
  `id_admin` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personel`
--

INSERT INTO `personel` (`id_personel`, `nama_lengkap`, `nama_panggilan`, `peran`, `bio`, `foto`, `urutan`, `medsos_anggota`, `id_admin`) VALUES
(2, 'Rian Ekky', 'Rian', 'vokal', NULL, 'gambar/personel/rian.vokal.png', 1, 'https://www.instagram.com/rianekkypradipta', 1),
(3, 'Dwiki Aditya', 'Dwiki', 'Gitaris', NULL, 'gambar/personel/Dwikigitaris.jpg', 2, 'https://www.instagram.com/dwikkymarsall', 1),
(4, 'Nurul Damar', 'Damar', 'Gitaris', NULL, 'gambar/personel/Nurulgitaris.jpg', 3, 'https://www.instagram.com/ramadhanrama?psln=dTRocjIwM2dkdDF0', 1),
(5, 'Rayyi kurniawan', 'rai', 'Bass', NULL, 'gambar/personel/Rayyibass.jpg', 4, 'https://www.instagram.com/raidinata?srtk=YmtrcndwaDE1b2s4', 1),
(6, 'Wahyu piadji', 'Wahyu', 'drum', NULL, 'gambar/personel/Wahyu.drumm.png', 5, 'https://www.instagram.com/wahyupiaji?psln=MWxmNHNpam9sNXduZA==', 1);

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id_produk` int(11) NOT NULL,
  `nama_produk` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga` int(11) NOT NULL COMMENT 'Harga dalam rupiah',
  `gambar` varchar(255) NOT NULL,
  `link_beli` varchar(255) NOT NULL COMMENT 'Tautan Buy Now ke marketplace',
  `status` enum('tersedia','habis') NOT NULL DEFAULT 'tersedia',
  `id_admin` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id_produk`, `nama_produk`, `deskripsi`, `harga`, `gambar`, `link_beli`, `status`, `id_admin`) VALUES
(1, 'KAOS OFFICIAL HITAM', 'Kaos katun premium combed 30s dengan sablon grafis eksklusif d\'Masiv di bagian belakang.', 119000, 'gambar/produk/baju_satu.jpg', 'https://shopee.co.id/DMasiv-Original_Tshirt-Tour-Harmony-For-Tomorrow--i.1497557850.56159244169', 'tersedia', 1),
(2, 'POLO SHIRT D\'MASIV', 'Kaos polo semi-formal hitam dengan bordir logo d\'Masiv jingga minimalis di dada.', 129000, 'gambar/produk/baju_kedua.jpg', 'https://blibli.onelink.me/GNtk/xotgerzr?utm_source=chatgpt.com', 'habis', 1),
(3, 'KEMEJA KOKO GRAY', 'Kemeja muslim berkerah mandarin abu-abu dengan aksen garis vertikal putih yang elegan.', 150000, 'gambar/produk/baju_ketiga.jpg', 'https://www.blibli.com/p/pt-musica-studios-d-masiv-baju-koko-limited-edition-merchandise-grey/ps--GRM-26195-00154?utm_source=chatgpt.com', 'tersedia', 1),
(4, 'OFFICIAL TOTE BAG', 'Tas jinjing kanvas hitam serbaguna dengan cetakan lirik lagu ikonik d\'Masiv.', 60000, 'gambar/produk/baju_keempat.jpg', 'https://blibli.onelink.me/GNtk/yg67s4p5?utm_source=chatgpt.com', 'habis', 1),
(5, 'KAOS EDISI SPESIAL', 'Kaos hitam edisi terbatas dengan ilustrasi artwork album d\'Masiv bernuansa rock.', 135000, 'gambar/produk/baju_kelima.jpg', 'https://blibli.onelink.me/GNtk/x8mwgr6t?utm_source=chatgpt.com', 'habis', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `uq_admin_username` (`username`),
  ADD UNIQUE KEY `uq_admin_email` (`email`);

--
-- Indexes for table `album`
--
ALTER TABLE `album`
  ADD PRIMARY KEY (`id_album`),
  ADD KEY `fk_album_admin` (`id_admin`);

--
-- Indexes for table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`id_event`),
  ADD KEY `fk_event_kategori` (`id_kategori`),
  ADD KEY `fk_event_admin` (`id_admin`);

--
-- Indexes for table `fans`
--
ALTER TABLE `fans`
  ADD PRIMARY KEY (`id_fans`),
  ADD UNIQUE KEY `uq_fans_email` (`email`);

--
-- Indexes for table `galeri`
--
ALTER TABLE `galeri`
  ADD PRIMARY KEY (`id_galeri`),
  ADD KEY `fk_galeri_admin` (`id_admin`);

--
-- Indexes for table `kategori_event`
--
ALTER TABLE `kategori_event`
  ADD PRIMARY KEY (`id_kategori`),
  ADD UNIQUE KEY `uq_kategori_nama` (`nama_kategori`);

--
-- Indexes for table `lagu`
--
ALTER TABLE `lagu`
  ADD PRIMARY KEY (`id_lagu`),
  ADD UNIQUE KEY `uq_lagu_album_urut` (`id_album`,`no_urut`),
  ADD KEY `fk_lagu_admin` (`id_admin`);

--
-- Indexes for table `media_sosial`
--
ALTER TABLE `media_sosial`
  ADD PRIMARY KEY (`id_sosmed`),
  ADD KEY `fk_sosmed_admin` (`id_admin`);

--
-- Indexes for table `mitra`
--
ALTER TABLE `mitra`
  ADD PRIMARY KEY (`id_mitra`),
  ADD KEY `fk_mitra_admin` (`id_admin`);

--
-- Indexes for table `personel`
--
ALTER TABLE `personel`
  ADD PRIMARY KEY (`id_personel`),
  ADD KEY `fk_personel_admin` (`id_admin`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id_produk`),
  ADD KEY `fk_produk_admin` (`id_admin`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `album`
--
ALTER TABLE `album`
  MODIFY `id_album` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `event`
--
ALTER TABLE `event`
  MODIFY `id_event` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `fans`
--
ALTER TABLE `fans`
  MODIFY `id_fans` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `galeri`
--
ALTER TABLE `galeri`
  MODIFY `id_galeri` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `kategori_event`
--
ALTER TABLE `kategori_event`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `lagu`
--
ALTER TABLE `lagu`
  MODIFY `id_lagu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `media_sosial`
--
ALTER TABLE `media_sosial`
  MODIFY `id_sosmed` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `mitra`
--
ALTER TABLE `mitra`
  MODIFY `id_mitra` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `personel`
--
ALTER TABLE `personel`
  MODIFY `id_personel` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id_produk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `album`
--
ALTER TABLE `album`
  ADD CONSTRAINT `fk_album_admin` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`) ON UPDATE CASCADE;

--
-- Constraints for table `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `fk_event_admin` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_event_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `kategori_event` (`id_kategori`) ON UPDATE CASCADE;

--
-- Constraints for table `galeri`
--
ALTER TABLE `galeri`
  ADD CONSTRAINT `fk_galeri_admin` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`) ON UPDATE CASCADE;

--
-- Constraints for table `lagu`
--
ALTER TABLE `lagu`
  ADD CONSTRAINT `fk_lagu_admin` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_lagu_album` FOREIGN KEY (`id_album`) REFERENCES `album` (`id_album`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `media_sosial`
--
ALTER TABLE `media_sosial`
  ADD CONSTRAINT `fk_sosmed_admin` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`) ON UPDATE CASCADE;

--
-- Constraints for table `mitra`
--
ALTER TABLE `mitra`
  ADD CONSTRAINT `fk_mitra_admin` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`) ON UPDATE CASCADE;

--
-- Constraints for table `personel`
--
ALTER TABLE `personel`
  ADD CONSTRAINT `fk_personel_admin` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`) ON UPDATE CASCADE;

--
-- Constraints for table `produk`
--
ALTER TABLE `produk`
  ADD CONSTRAINT `fk_produk_admin` FOREIGN KEY (`id_admin`) REFERENCES `admin` (`id_admin`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
