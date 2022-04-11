-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 15, 2021 at 10:33 PM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 7.4.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `eror`
--

-- --------------------------------------------------------

--
-- Table structure for table `bukti_laporan`
--

CREATE TABLE `bukti_laporan` (
  `id` int(11) NOT NULL,
  `laporan_id` int(11) NOT NULL,
  `gambar_1` varchar(255) NOT NULL,
  `gambar_2` varchar(255) NOT NULL,
  `gambar_3` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bukti_laporan`
--

INSERT INTO `bukti_laporan` (`id`, `laporan_id`, `gambar_1`, `gambar_2`, `gambar_3`) VALUES
(19, 48, '1636936522_EROR.png', '1636936522_photo_profile.png', '1636936522_pintu.jpg'),
(20, 49, '1636936545_EROR.png', '1636936545_photo_profile.png', '1636936545_pintu.jpg'),
(21, 67, '1637010859_image-sensor.jpg', '', ''),
(22, 68, '1637010985_bukti_uts_iot.png', '1637010985_image-sensor.jpg', '1637010985_iot.png');

-- --------------------------------------------------------

--
-- Table structure for table `faq`
--

CREATE TABLE `faq` (
  `id` int(11) NOT NULL,
  `pertanyaan` varchar(156) NOT NULL,
  `jawaban` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `faq`
--

INSERT INTO `faq` (`id`, `pertanyaan`, `jawaban`) VALUES
(1, 'bagaiamana ini?', 'YNTKTS');

-- --------------------------------------------------------

--
-- Table structure for table `forgot_password`
--

CREATE TABLE `forgot_password` (
  `id` int(11) NOT NULL,
  `email` varchar(155) NOT NULL,
  `kode` int(11) NOT NULL,
  `expired_at` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `forgot_password`
--

INSERT INTO `forgot_password` (`id`, `email`, `kode`, `expired_at`) VALUES
(10, 'haditsalkhafidl@gmail.com', 3440, '04:03:50');

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id` int(11) NOT NULL,
  `nama` varchar(156) NOT NULL,
  `kd_kategori` char(5) NOT NULL,
  `icon` varchar(155) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id`, `nama`, `kd_kategori`, `icon`) VALUES
(1, 'Bangunan', 'BG', '/assets/svg/electrical.svg'),
(3, 'Mechanical Electrical', 'ME', '/assets/svg/electrical.svg'),
(4, 'Taman', 'TM', '/assets/svg/electrical.svg'),
(15, 'Perangkat Pribadi', 'PP', '/assets/img/1636931359_electrical.svg');

-- --------------------------------------------------------

--
-- Table structure for table `laporan`
--

CREATE TABLE `laporan` (
  `id` int(11) NOT NULL,
  `kode_laporan` char(15) NOT NULL,
  `pelapor_id` int(11) NOT NULL,
  `jenis_kerusakan` varchar(156) NOT NULL,
  `lokasi` varchar(156) NOT NULL,
  `keterangan` varchar(156) NOT NULL,
  `keterangan_admin` varchar(156) NOT NULL,
  `keterangan_teknisi` varchar(156) NOT NULL,
  `tanggal_lapor` datetime NOT NULL,
  `tanggal_pengecekan` datetime NOT NULL,
  `kategori_id` int(11) NOT NULL,
  `status_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `laporan`
--

INSERT INTO `laporan` (`id`, `kode_laporan`, `pelapor_id`, `jenis_kerusakan`, `lokasi`, `keterangan`, `keterangan_admin`, `keterangan_teknisi`, `tanggal_lapor`, `tanggal_pengecekan`, `kategori_id`, `status_id`) VALUES
(10, '#RP-BG-12345', 9, 'Ac rusak', 'Gedung serba bisa lantai 12', 'Ac tidak dingin dan menimbulkan suara bising', 'Laporan diterima', 'Sudah diperbaiki bosku.', '2021-11-04 19:33:23', '2021-11-04 19:33:23', 1, 3),
(11, '#RP-BG-15334', 5, 'Ac rusak', 'Gedung serba bisa lantai 12', 'Ac tidak dingin dan menimbulkan suara bising', 'Laporan diterima dan kita akan mengirim teknisi untuk memeriksa', 'Rusak berat nih, butuh pihak ketiga.', '2021-11-04 19:38:44', '2021-11-04 19:38:44', 1, 1),
(12, '#RP-BG-12375', 5, 'Ac rusak', 'Gedung serba bisa lantai 11', 'Ac tidak dingin dan menimbulkan suara bising', ' Terima kasih atas laporannya', ' Sudah diperbaiki ya', '2021-11-04 19:35:08', '2021-11-04 19:35:08', 1, 1),
(48, '#RP-49172BG', 9, 'Test', 'Test', 'Test', '', '', '2021-11-15 01:35:22', '0000-00-00 00:00:00', 1, 4),
(49, '#RP-71314BG', 9, 'Test', 'Test', 'Test', '', '', '2021-11-15 01:35:45', '0000-00-00 00:00:00', 1, 4),
(51, '#RP-52475BG', 14, 'rusak', 'gedung a', 'asiap', '', '', '2021-11-15 21:51:09', '0000-00-00 00:00:00', 1, 4),
(52, '#RP-96892BG', 14, 'rusak', 'gedung a', 'asiap', '', '', '2021-11-15 21:51:52', '0000-00-00 00:00:00', 1, 4),
(53, '#RP-79125BG', 14, 'rusak', 'gedung a', 'asiap', '', '', '2021-11-15 21:52:49', '0000-00-00 00:00:00', 1, 4),
(54, '#RP-19792BG', 14, 'rusak', 'gedung a', 'asiap', '', '', '2021-11-15 21:55:01', '0000-00-00 00:00:00', 1, 4),
(55, '#RP-52512BG', 14, 'rusak', 'gedung a', 'asiap', '', '', '2021-11-15 21:56:00', '0000-00-00 00:00:00', 1, 4),
(56, '#RP-44994BG', 14, 'rusak', 'gedung a', 'asiap', '', '', '2021-11-15 21:58:06', '0000-00-00 00:00:00', 1, 4),
(57, '#RP-67252BG', 14, 'rusak', 'gedung a', 'asiap', '', '', '2021-11-15 22:00:12', '0000-00-00 00:00:00', 1, 4),
(58, '#RP-63690BG', 14, 'rusak', 'gedung a', 'asiap', '', '', '2021-11-15 22:01:10', '0000-00-00 00:00:00', 1, 4),
(59, '#RP-54585BG', 14, 'rusak', 'gedung a', 'asiap', '', '', '2021-11-15 22:01:53', '0000-00-00 00:00:00', 1, 4),
(60, '#RP-50897BG', 14, 'rusak', 'gedung a', 'asiap', '', '', '2021-11-15 22:03:52', '0000-00-00 00:00:00', 1, 4),
(61, '#RP-62951BG', 14, 'rusak', 'gedung a', 'asiap', '', '', '2021-11-15 22:04:45', '0000-00-00 00:00:00', 1, 4),
(62, '#RP-60037BG', 14, 'rusak', 'gedung a', 'asiap', '', '', '2021-11-15 22:05:29', '0000-00-00 00:00:00', 1, 4),
(63, '#RP-83814BG', 14, 'rusak', 'gedung a', 'asiap', '', '', '2021-11-15 22:07:57', '0000-00-00 00:00:00', 1, 4),
(64, '#RP-51065BG', 14, 'rusak', 'gedung a', 'asiap', '', '', '2021-11-15 22:08:06', '0000-00-00 00:00:00', 1, 4),
(65, '#RP-22728BG', 14, 'rusak', 'gedung a', 'asiap', '', '', '2021-11-15 22:08:24', '0000-00-00 00:00:00', 1, 4),
(66, '#RP-42354BG', 14, 'rusak', 'gedung a', 'asiap', '', '', '2021-11-15 22:08:44', '0000-00-00 00:00:00', 1, 4),
(67, '#RP-32691BG', 14, 'rusak', 'gedung b', 'ac bocor', '', '', '2021-11-15 22:14:19', '0000-00-00 00:00:00', 1, 4),
(68, '#RP-44045BG', 14, 'rusak', 'gedung b', 'ac bocor', '', '', '2021-11-15 22:16:25', '0000-00-00 00:00:00', 1, 4);

-- --------------------------------------------------------

--
-- Table structure for table `notifikasi`
--

CREATE TABLE `notifikasi` (
  `id` int(11) NOT NULL,
  `kode_laporan` int(11) NOT NULL,
  `pesan` text NOT NULL,
  `keterangan` varchar(156) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `pangkat`
--

CREATE TABLE `pangkat` (
  `id` int(11) NOT NULL,
  `nama` varchar(156) NOT NULL,
  `exp` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pangkat`
--

INSERT INTO `pangkat` (`id`, `nama`, `exp`) VALUES
(1, 'Bronze', 100),
(2, 'Silver', 200),
(3, 'Gold', 400),
(4, 'Diamond', 800),
(5, 'Ruby', 1600),
(6, 'Saphire', 3200),
(7, 'Emerald', 3201);

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `id` int(11) NOT NULL,
  `nama` varchar(156) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`id`, `nama`) VALUES
(1, 'Pengguna'),
(2, 'Admin'),
(3, 'Teknisi');

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE `status` (
  `id` int(11) NOT NULL,
  `nama` varchar(156) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `status`
--

INSERT INTO `status` (`id`, `nama`) VALUES
(1, 'Terkirim'),
(2, 'Duplikat'),
(3, 'Validasi'),
(4, 'Pengecekan'),
(5, 'Diperbaiki'),
(6, 'Selesai'),
(7, 'Ditolak');

-- --------------------------------------------------------

--
-- Table structure for table `superadmin`
--

CREATE TABLE `superadmin` (
  `id` int(11) NOT NULL,
  `username` varchar(156) NOT NULL,
  `password` varchar(156) NOT NULL,
  `nama_lengkap` varchar(156) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `superadmin`
--

INSERT INTO `superadmin` (`id`, `username`, `password`, `nama_lengkap`, `created_at`, `updated_at`) VALUES
(4, 'admin1234', '$2y$10$B62ew8.JH9/jpbUbXcdeN.N3OCrkei2WO53Cylea3nNwwf0D6OPC2', 'Muhammad Sarjono', '2021-11-01 21:04:26', '2021-11-01 21:04:26'),
(5, 'admin13', '$2y$10$WBgeBgEJY.zLRWVnJAxMEOGOPEoy5KGYKcGwzzyqNfBGc30TgBUSa', 'Muhammad Yamin', '2021-11-01 21:09:43', '2021-11-01 21:09:43'),
(6, 'admin13', '$2y$10$/s.NN0fFWrgXkppTI5uUMO3SqKWapMsmOM3GekZsr85RPilpYYtl2', 'Muhammad Yamin', '2021-11-02 16:00:59', '2021-11-02 16:00:59'),
(7, 'admin13', '$2y$10$PCKiy0.0DD7wxA5rSOtPxucDAvP5WVxOwTWfISqXa8jGQyNls2UoC', 'Muhammad Yamin', '2021-11-02 16:22:04', '2021-11-02 16:22:04'),
(8, 'admin1234', '$2y$10$Zhsx50VtRX4NjugCKJJIUe6OatNvL0yS0eQoITcQN7bwXzQskzffS', 'Ahmad Yamin', '2021-11-02 16:25:53', '2021-11-02 16:25:53'),
(9, 'admin1234', '$2y$10$i0z6sxevMxzHglNq2AT.P.xvkk7VdO44SRyi/XH5SIZTk889fhZo.', 'Ahmad Yamin', '2021-11-02 16:26:00', '2021-11-02 16:26:00'),
(10, 'admin1234', '$2y$10$eip4wh2/wrM5yd1QH1cdOeSN4JgtemWlVaAoii5s/QEiClJu4R5Vq', 'Ahmad Yamin', '2021-11-02 16:26:16', '2021-11-02 16:26:16'),
(11, 'admin1234', '$2y$10$VPkx2kJopfEVYU2mchOaderij/5OUn8rrRsFfWBC166wJsqlxyr.e', 'Ahmad Yamin', '2021-11-02 16:27:23', '2021-11-02 16:27:23'),
(12, 'admin1234', '$2y$10$8rJm5FRbWs/ZzLQ4EsUVbeEwoPBVLzXnGWcbZhqDO5lfrdiKbiwKS', 'Ahmad Yamin', '2021-11-02 16:27:52', '2021-11-02 16:27:52'),
(13, 'admin1234', '$2y$10$lyEHX1Tvb.jNEc.g/ouCb.OweECsEKFjeHv2IbfwNMfZSiWKRBSnm', 'Ahmad Yamin', '2021-11-02 16:28:12', '2021-11-02 16:28:12'),
(14, 'admin1234', '$2y$10$rc9FX10QDrpqQDAK7RS4JOd8XqJ5m9KrHTQG0XVEZjHTJd532eY2S', 'Ahmad Yamin', '2021-11-02 16:28:43', '2021-11-02 16:28:43'),
(15, 'admin1234', '$2y$10$pN4b.gOnnOIS78VOJ7XJ6ukdTK9m5V.kMtEQNA.K78n2JhvsTmOOy', 'Ahmad Yamin', '2021-11-02 16:28:50', '2021-11-02 16:28:50'),
(16, 'admin1234', '$2y$10$E5JxlrKYappIAEqfBLHeju2.2qAEpWWnY75Q0oAqqcD.hH.aDDVuu', 'Ahmad Yamin', '2021-11-02 16:28:54', '2021-11-02 16:28:54'),
(17, 'admin1234x', '$2y$10$e31hB6sA52A9IBG6jkwfQu5Mm9pRzaX6AWlqEBhCQWxssZVKcOn2G', 'Ahmad Yamin', '2021-11-02 16:28:59', '2021-11-02 16:28:59'),
(18, 'admin1234x', '$2y$10$k1trNtNC13u5tZiYgEJSme3mEtZ.DXkXrpG4ms0P7cr/j/2L44Uz6', 'Ahmad Yamin', '2021-11-02 17:30:48', '2021-11-02 17:30:48'),
(20, 'admin', '$2y$10$sCkt8wt9Xczxir/7vXo9eOKLPWvEc7YHtJd.YoW3WwLtblucOxto2', 'administrator', '2021-11-04 17:53:39', '2021-11-04 17:53:39'),
(21, 'admon', '$2y$10$dqifXGo8KEIXxWMH/XVBPudtHNPAf8O3Yz6MiEv7jEUpVINjfaAVO', 'admon123', '2021-11-05 12:18:25', '2021-11-05 12:18:25'),
(22, 'ikbal', '$2y$10$xFPno0GSpKvY0mAJ8NACSO9qeVYaKaddHMsg.6TPHW67NffEtENke', 'ikbal', '2021-11-05 12:19:38', '2021-11-05 12:19:38'),
(23, 'alek', '$2y$10$bVdJ3Ph7G3JFV2RY6QGgHuguC0K04KFfVT.JeP8xGx.UgvOG378Pa', 'danu', '2021-11-05 12:21:55', '2021-11-05 12:21:55'),
(24, 'danu', '$2y$10$CzLwtTOUP/9kV81pJMC2J.LVWWNTZQqq0F5xa2qzK/dHMUzqyCCDC', 'ALEK', '2021-11-05 12:23:39', '2021-11-05 12:23:39');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `nama_lengkap` varchar(156) NOT NULL,
  `password` varchar(156) NOT NULL,
  `jenis_kelamin` char(15) NOT NULL,
  `email` varchar(156) NOT NULL,
  `no_telp` char(15) NOT NULL,
  `jabatan` varchar(156) NOT NULL,
  `current_exp` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `role_id`, `nama_lengkap`, `password`, `jenis_kelamin`, `email`, `no_telp`, `jabatan`, `current_exp`, `created_at`, `updated_at`) VALUES
(1, 1, 'Skyperx', '$2y$10$ssbnoF/3RiRvkjY22LEmVuP7WxApjpo3T6FCaBSkJONEpkpc9dY.C', 'laki-laki', 'iqbalkorompiz@gmail.com', '082298546467', 'Staff', 6, '2021-11-03 08:00:00', '2021-11-04 19:35:42'),
(2, 1, 'Jamal', '$2y$10$i0UCCgjhmSiiFJKH2fMLVuwvn6op.IGGLpA.AvqIF/nKxP3RBW9Wi', 'Pria', 'jamal@jackmail.com', '08776127373', 'staff', 0, '2021-11-04 19:36:48', '2021-11-04 19:37:48'),
(3, 1, 'Mahmud', '$2y$10$N4ydvaKJ1huz9CDYg2PtD.mnetsGZjjh8rszeFDEQvTdeBEC91Ovq', '', 'mahmud@gmail.com', '', '', 0, '2021-11-08 15:49:52', '2021-11-08 15:49:52'),
(4, 2, 'jajang ginanjar', '9002', 'Pria', 'jajangcool@gmail.com', '1234567891012', 'staf', 0, '2021-11-08 16:01:44', '2021-11-08 16:01:44'),
(5, 1, 'Hadis', '3429', '', 'haditsalkhafidl@gmail.com', '', '', 0, '2021-11-12 09:23:37', '2021-11-12 09:23:37'),
(6, 1, 'Muhammad Iqbal Ramadhan', '$2y$10$66U41XGC8BxhmzMeZlafEO3rPbq43ZcrOoZBv0MivGpK.Rh7Iy0lC', '', 'iqbalkorompiz@gmail.com', '', '', 0, '2021-11-13 10:02:44', '2021-11-13 10:02:44'),
(9, 1, 'Muhammad Iqbal Ramadhan', '$2y$10$QDPXQdNb8JIgiD2MCGjU0eW1y5uH4zdi1k6Rjhmlf8.Ig0dEAt/R2', '', 'iqbalkorompiz123@gmail.com', '', '', 0, '2021-11-13 10:19:02', '2021-11-13 10:19:02'),
(10, 2, 'Muhammad Iqbal Ramadhan', '$2y$10$Ig0dYuIAIQ3J.WU5T0WVLeNHnHOgv1FKf/PDsC.HxC6pg7zdo007q', '', 'iqbalkorompiz123x@gmail.com', '', '', 0, '2021-11-13 22:58:17', '2021-11-13 22:58:17'),
(11, 3, 'Muhammad Iqbal Ramadhan', '$2y$10$CTey8TkmRcB3GvMZHaTIPOHhyzgs/64c6zZdTgwQZa6L10za5kt32', '', 'iqbalkorompiz123z@gmail.com', '', '', 0, '2021-11-13 22:58:45', '2021-11-13 22:58:45'),
(13, 1, 'jamal', '$2y$10$OL5PKM1v01Jj4LmMGrHHseIBYlC1jM/ZGQOlXvTzjftbkJnZUcMtO', '', 'jamaludin@gmail.com', '', '', 0, '2021-11-15 20:54:33', '2021-11-15 20:54:33'),
(14, 1, 'edi', '$2y$10$Q1ukZJK4ACX0lkArekZNDexXG.zME7sbpoNUJ822bb6ouNS21efUC', '', 'edi@gmail.com', '', '', 13, '2021-11-15 20:56:21', '2021-11-15 20:56:21');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bukti_laporan`
--
ALTER TABLE `bukti_laporan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `laporan_id` (`laporan_id`);

--
-- Indexes for table `faq`
--
ALTER TABLE `faq`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `forgot_password`
--
ALTER TABLE `forgot_password`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `laporan`
--
ALTER TABLE `laporan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pelapor_id` (`pelapor_id`),
  ADD KEY `kategori_id` (`kategori_id`),
  ADD KEY `pelapor_id_2` (`pelapor_id`),
  ADD KEY `status_id` (`status_id`);

--
-- Indexes for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kode_laporan` (`kode_laporan`);

--
-- Indexes for table `pangkat`
--
ALTER TABLE `pangkat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `superadmin`
--
ALTER TABLE `superadmin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bukti_laporan`
--
ALTER TABLE `bukti_laporan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `faq`
--
ALTER TABLE `faq`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `forgot_password`
--
ALTER TABLE `forgot_password`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `laporan`
--
ALTER TABLE `laporan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pangkat`
--
ALTER TABLE `pangkat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `status`
--
ALTER TABLE `status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `superadmin`
--
ALTER TABLE `superadmin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bukti_laporan`
--
ALTER TABLE `bukti_laporan`
  ADD CONSTRAINT `bukti` FOREIGN KEY (`laporan_id`) REFERENCES `laporan` (`id`);

--
-- Constraints for table `laporan`
--
ALTER TABLE `laporan`
  ADD CONSTRAINT `kategori` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`),
  ADD CONSTRAINT `pelapor` FOREIGN KEY (`pelapor_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `status` FOREIGN KEY (`status_id`) REFERENCES `status` (`id`);

--
-- Constraints for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD CONSTRAINT `kd_laporan` FOREIGN KEY (`kode_laporan`) REFERENCES `laporan` (`id`);

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `role` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
