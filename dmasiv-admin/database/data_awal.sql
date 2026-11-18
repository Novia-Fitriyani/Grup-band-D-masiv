-- D'MASIV: data awal (disalin dari konten frontend lama yang sebelumnya ditulis manual di HTML)
-- Jalankan SETELAH dmasiv_database.sql
USE dmasiv_db;
SET NAMES utf8mb4;

-- Admin awal. Kata sandi sementara: Admin#2026  (WAJIB diganti lewat menu Kelola Admin)
INSERT INTO admin (id_admin,username,password,nama_lengkap,email,status) VALUES (1,'admin','$2y$10$vsFCBmb33FOXh0SBZKaaOeoOP3z0y0MoANnSQa0ft.YI14ccYgiI.','Administrator','admin@dmasiv.local','aktif');

INSERT INTO kategori_event (id_kategori,nama_kategori) VALUES (1,'Release Promo'),(2,'Concert'),(3,'On Air Radio'),(4,'Others');

INSERT INTO event (id_event,id_kategori,judul,lokasi,tanggal_event,waktu_mulai,deskripsi,id_admin,created_at) VALUES
(1,1,'Breakout NET','NET TV','2018-09-21','12:47:35',NULL,1,'2018-09-21 12:47:35'),
(2,1,'linikini.com','Jakarta','2018-09-25','12:48:01',NULL,1,'2018-09-25 12:48:01'),
(3,1,'Shooting Video Clip','Jakarta','2018-09-24','12:53:45',NULL,1,'2018-09-24 12:53:45'),
(4,2,'DMASIV EVENT','Jombang','2018-09-29','12:48:58',NULL,1,'2018-09-29 12:48:58'),
(5,2,'DMASIV EVENT','Puri Mall','2019-02-03','11:44:25',NULL,1,'2019-02-03 11:44:25'),
(6,2,'D''MASIV EVENT','Semarang','2019-02-08','11:45:15',NULL,1,'2019-02-08 11:45:15'),
(7,2,'Smartfren Event','CFD','2019-02-10','11:46:56',NULL,1,'2019-02-10 11:46:56'),
(8,2,'D''MASIV EVENT','Atambua','2019-03-09','07:00:18',NULL,1,'2019-03-09 07:00:18'),
(9,2,'D''MASIV EVENT','Bali','2019-03-16','07:02:28',NULL,1,'2019-03-16 07:02:28'),
(10,2,'D''MASIV EVENT','Semarang','2019-04-01','10:04:27',NULL,1,'2019-04-01 10:04:27'),
(11,2,'D''MASIV EVENT','Bandung','2019-04-06','10:05:40',NULL,1,'2019-04-06 10:05:40'),
(12,2,'D''MASIV EVENT','Pali/Palembang','2019-04-09','10:05:59',NULL,1,'2019-04-09 10:05:59'),
(13,3,'Promo Radio','Jakarta','2019-03-13','02:01:10',NULL,1,'2019-03-13 02:01:10'),
(14,3,'Promo Radio','Jawa Tengah','2019-04-02','10:04:48',NULL,1,'2019-04-02 10:04:48'),
(15,4,'Press Conf Antologi Rasa','Plaza Senayan','2019-02-09','11:46:00',NULL,1,'2019-02-09 11:46:00');

INSERT INTO album (id_album,judul_album,tanggal_rilis,cover,link_spotify,id_admin) VALUES
(1,'Ingin Lekas Memelukmu Lagi','2018-08-01','gambar/album/lagu_pertama.jpg',NULL,1),
(2,'Selamat Jalan Kasih','2018-08-01','gambar/album/lagu_kedua.jpg',NULL,1),
(3,'Pernah Memiliki','2018-03-01','gambar/album/lagu_ketiga.jpg',NULL,1),
(4,'Orange Album','2017-04-01','gambar/album/lagu_keempat.jpg',NULL,1),
(5,'D''MASIV with Raef','2016-05-01','gambar/album/lagu_kelima.png','https://open.spotify.com/album/3ZZLSlNhsDYCUHhMtbyd9p?si=liJj9hFgTT-ONcDSS5AeyA',1),
(6,'Hidup Lebih Indah','2014-07-01','gambar/album/lagu_keenam.jpg','https://open.spotify.com/album/43q5YRL1xn8ngZoGa7eIgP?si=466b90fadf27498a',1),
(7,'Persiapan','2012-02-01','gambar/album/lagu_ketujuh.jpg','https://open.spotify.com/album/3Q6oW24ccQhsfLE7PEW36v?si=04f122e737e34e39',1),
(8,'Perjalanan','2009-12-01','gambar/album/lagu_kedelapan.jpg','https://open.spotify.com/album/1ygbAOzcTsfP2JCBOYIUrT?si=_zzbHnBISpWudbG6e757xw',1),
(9,'Perubahan','2008-09-01','gambar/album/lagu_kesembilan.jpg','https://open.spotify.com/album/6eDcL5Onn3OPEFnEfnsWRb?si=0b03c16441084ef5',1);

INSERT INTO lagu (id_album,judul_lagu,no_urut,link_spotify,id_admin) VALUES
(1,'Ingin Lekas Memelukmu Lagi',1,'https://open.spotify.com/track/40r31knlz4HIx2hbs55dh9?si=76b4df6572244354',1),
(2,'Selamat Jalan Kasih',1,'https://open.spotify.com/track/1mjTITSFnucqtdkDa7rDGM?si=2cd03166154846ab',1),
(3,'PERNAH MEMILIKI',1,'https://open.spotify.com/track/3f0XPPOLznQq5rSFCDXYvr?si=d7b3bb0598ac49ae',1),
(4,'DIBAWAH LANGIT YANG SAMA',1,'https://open.spotify.com/track/1GD3qlRikNoTU3zHQZ5YsQ?si=a7fca001c9694986',1),
(5,'D''MASIV with Raef',1,'https://open.spotify.com/album/3ZZLSlNhsDYCUHhMtbyd9p?si=liJj9hFgTT-ONcDSS5AeyA',1),
(6,'ESOK KAN BAHAGIA',1,'https://open.spotify.com/album/43q5YRL1xn8ngZoGa7eIgP?si=466b90fadf27498a',1),
(7,'NATURAL / PERSIAPAN',1,'https://open.spotify.com/album/3Q6oW24ccQhsfLE7PEW36v?si=04f122e737e34e39',1),
(8,'APA SALAHKU',1,'https://open.spotify.com/album/1ygbAOzcTsfP2JCBOYIUrT?si=_zzbHnBISpWudbG6e757xw',1),
(9,'CINTA INI MEMBUNUHKU',1,'https://open.spotify.com/album/6eDcL5Onn3OPEFnEfnsWRb?si=0b03c16441084ef5',1);

INSERT INTO personel (nama_lengkap,nama_panggilan,peran,bio,foto,urutan,medsos_anggota,id_admin) VALUES
('Rian Ekky Pradipta','Rian Ekky','Vokal','Suara utama band yang membawa karakter kuat pada setiap lagu D''MASIV.','gambar/personel/rian.vokal.png',1,'https://www.instagram.com/dmasiv?igsh=MWxwdjdhNW85N3RsdA==',1),
('Dwiki Aditya Marsali','Dwiki Aditya','Gitar','Riff dan melodi gitar yang menjadi fondasi musik D''MASIV sejak awal berdiri.','gambar/personel/Dwikigitaris.jpg',2,'https://www.instagram.com/dmasiv?igsh=MWxwdjdhNW85N3RsdA==',1),
('Nurul Damar Ramadan','Nurul Damar','Gitar','Harmoni dan lead guitar yang memperkaya warna musik D''MASIV.','gambar/personel/Nurulgitaris.jpg',3,'https://www.instagram.com/dmasiv?igsh=MWxwdjdhNW85N3RsdA==',1),
('Rayyi Kurniawan Iskandar Dinata','Rayyi Kurniawan','Bass','Groove bass yang kokoh menjaga ritme dan dinamika tiap penampilan live.','gambar/personel/Rayyibass.jpg',4,'https://www.instagram.com/dmasiv?igsh=MWxwdjdhNW85N3RsdA==',1),
('Wahyu Piadji','Wahyu Piadji','Drum','Ketukan drum energik yang menjadi tulang punggung performa D''MASIV di atas panggung.','gambar/personel/Wahyu.drumm.png',5,'https://www.instagram.com/dmasiv?igsh=MWxwdjdhNW85N3RsdA==',1);

INSERT INTO media_sosial (platform,url,ikon,nama_kontak,no_telepon,id_admin) VALUES
('Facebook','https://www.facebook.com/dmasivband','fab fa-facebook-f','Ario','+62 813 8808 3303',1),
('Twitter','https://x.com/DMASIV','fab fa-twitter','Ario','+62 813 8808 3303',1),
('Instagram','https://www.instagram.com/dmasiv?igsh=MWxwdjdhNW85N3RsdA==','fab fa-instagram','Sisil','+62 877 7604 1906',1),
('YouTube','https://youtube.com/@dmasivbandofficial?si=_iknEBoi1T_FTSZV','fab fa-youtube','Sisil','+62 877 7604 1906',1);

INSERT INTO mitra (nama_mitra,url,id_admin) VALUES
('JOOX','https://www.joox.com',1),
('iTunes','https://www.apple.com/itunes/',1),
('Spotify','https://open.spotify.com',1),
('Nada Kita',NULL,1);

INSERT INTO galeri (tipe,judul,tanggal,file_thumbnail,url_video,status_aktif,id_admin) VALUES
('foto','#DMASIV5150','2018-03-09','gambar/galeri/photo1.jpeg',NULL,'arsip',1),
('foto','D''MASIV Live di Kediri','2017-05-13','gambar/galeri/gambar_kedua.jpeg',NULL,'arsip',1),
('foto','D''MASIV Live Salam Ramadhan 2017','2017-05-30','gambar/galeri/gambar_ketiga.jpeg',NULL,'arsip',1),
('foto','Salam Ramadhan 2017 – Bintaro Xchange','2017-06-13','gambar/galeri/gambar_keempat.jpeg',NULL,'arsip',1),
('foto','Bukber Musica Studio''s','2017-06-16','gambar/galeri/gambar_kelima.jpeg',NULL,'arsip',1),
('foto','Countdown ASIAN GAMES 2018','2018-08-20','gambar/galeri/foto_ketigabelas.jpg',NULL,'arsip',1),
('video','D''MASIV - Dengarlah Sayang (Official Video)','2016-09-28','gambar/galeri/foto_keenam.jpg',NULL,'arsip',1),
('video','D''MASIV - Melodi (Official Video)','2017-03-14','gambar/galeri/foto_ketujuh.jpg',NULL,'arsip',1),
('video','Umrah Bersama D''MASIV dan Raef','2017-04-11','gambar/galeri/foto_kedelapan.jpg',NULL,'arsip',1),
('video','Cerita Ramadhan : Pengalaman Pertama Puasa Rian D''MASIV','2017-05-30','gambar/galeri/foto_kesebelas.jpg',NULL,'arsip',1),
('video','Cerita Ramadhan : Tips Puasa Ramadhan Rian D''MASIV','2017-05-30','gambar/galeri/foto__keduabelas.jpg',NULL,'arsip',1),
('video','D''MASIV, Rossa Feat David NOAH - Pernah Memiliki','2018-03-06','gambar/galeri/foto_keempatbelas.jpg',NULL,'arsip',1),
('video','D''MASIV – Cinta Ini Membunuhku','2026-01-01','https://img.youtube.com/vi/DyaIFYb-qyM/hqdefault.jpg','https://www.youtube.com/watch?v=DyaIFYb-qyM','tampil',1);

INSERT INTO produk (nama_produk,deskripsi,harga,gambar,link_beli,status,id_admin) VALUES
('Kaos Official Hitam','Kaos katun premium combed 30s dengan sablon grafis eksklusif d''Masiv di bagian belakang.',119000,'gambar/produk/baju_satu.jpg','https://blibli.onelink.me/GNtk/p6cxb3y7','tersedia',1),
('Polo Shirt d''Masiv','Kaos polo semi-formal hitam dengan bordir logo d''Masiv jingga minimalis di dada.',129000,'gambar/produk/baju_kedua.jpg','https://blibli.onelink.me/GNtk/xotgerzr','tersedia',1),
('Kemeja Koko Gray','Kemeja muslim berkerah mandarin abu-abu dengan aksen garis vertikal putih yang elegan.',150000,'gambar/produk/baju_ketiga.jpg','https://www.blibli.com/p/pt-musica-studios-d-masiv-baju-koko-limited-edition-merchandise-grey/ps--GRM-26195-00154','tersedia',1),
('Official Tote Bag','Tas jinjing kanvas hitam serbaguna dengan cetakan lirik lagu ikonik d''Masiv.',60000,'gambar/produk/baju_keempat.jpg','https://blibli.onelink.me/GNtk/yg67s4p5','tersedia',1),
('Kaos Edisi Spesial','Kaos hitam edisi terbatas dengan ilustrasi artwork album d''Masiv bernuansa rock.',135000,'gambar/produk/baju_kelima.jpg','https://blibli.onelink.me/GNtk/x8mwgr6t','tersedia',1);
