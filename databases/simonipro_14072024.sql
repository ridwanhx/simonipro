-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 13, 2024 at 10:05 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `simonipro`
--

-- --------------------------------------------------------

--
-- Table structure for table `absensi_bimbingan`
--

CREATE TABLE `absensi_bimbingan` (
  `id` int(11) NOT NULL,
  `plotting_id` int(11) NOT NULL,
  `tgl_bimbingan` date NOT NULL,
  `topik` varchar(255) NOT NULL,
  `status` enum('hadir','rekomendasi') NOT NULL DEFAULT 'hadir',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dosen`
--

CREATE TABLE `dosen` (
  `id` int(11) NOT NULL,
  `nidn` char(10) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `gambar` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dosen`
--

INSERT INTO `dosen` (`id`, `nidn`, `nama`, `user_id`, `gambar`) VALUES
(8, '0428797909', 'Dr. Tyler Durden S.H.,M.IPA.', 45, 'default.jpeg'),
(9, '0988766090', 'Salim Bahanan S.Pd.,M.BKM.', 46, 'default.jpeg'),
(10, '0240259407', 'Muzammil Hasballah S.I.,M.TQ.', 47, 'default.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `jadwal`
--

CREATE TABLE `jadwal` (
  `id` int(11) NOT NULL,
  `nama_jadwal` varchar(150) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `status` enum('draft','published') NOT NULL DEFAULT 'draft'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jadwal`
--

INSERT INTO `jadwal` (`id`, `nama_jadwal`, `created_at`, `updated_at`, `status`) VALUES
(3, 'Proyek 2 Semester Genap 2024', 1719714822, 1720344730, 'published'),
(6, 'Proyek 2', 1720683931, 1720683931, 'draft');

-- --------------------------------------------------------

--
-- Table structure for table `jenis_plotting`
--

CREATE TABLE `jenis_plotting` (
  `id` int(11) NOT NULL,
  `nama` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jenis_plotting`
--

INSERT INTO `jenis_plotting` (`id`, `nama`) VALUES
(1, 'pembimbing'),
(2, 'penguji');

-- --------------------------------------------------------

--
-- Table structure for table `kegiatan`
--

CREATE TABLE `kegiatan` (
  `id` int(11) NOT NULL,
  `jadwal_id` int(11) NOT NULL,
  `tgl_awal` date NOT NULL,
  `tgl_selesai` date NOT NULL,
  `nama_kegiatan` varchar(150) NOT NULL,
  `deskripsi` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kegiatan`
--

INSERT INTO `kegiatan` (`id`, `jadwal_id`, `tgl_awal`, `tgl_selesai`, `nama_kegiatan`, `deskripsi`) VALUES
(1, 3, '2024-06-30', '2024-07-01', 'Pembekalan dan Pengarahan', '&lt;p&gt;&lt;strong&gt;Proses pembekalan dan pengarahan&lt;/strong&gt; mahasiswa yang mengikuti dan terlibat di Mata Kuliah Proyek 2&lt;/p&gt;\r\n&lt;ol&gt;\r\n&lt;li style=&quot;font-style: italic;&quot;&gt;&lt;em&gt;Penyampaian pembekalan&lt;/em&gt;&lt;/li&gt;\r\n&lt;li&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;Pengisian absensi&lt;/span&gt;&lt;/li&gt;\r\n&lt;li style=&quot;font-weight: bold; font-style: italic;&quot;&gt;&lt;em&gt;&lt;strong&gt;Pengumuman jadwal kegiatan&lt;/strong&gt;&lt;/em&gt;&lt;/li&gt;\r\n&lt;/ol&gt;'),
(7, 3, '2024-07-02', '2024-07-06', 'Pengajuan judul dan pengumpulan proposal proyek 2', '&lt;div&gt;\r\n&lt;p&gt;&lt;strong&gt;Lorem ipsum dolor sit amet, consectetur adipisicing elit. Doloribus, eos.&lt;/strong&gt;&lt;/p&gt;\r\n&lt;ol&gt;\r\n&lt;li&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;em&gt;lorem ipsum&lt;/em&gt;&lt;/span&gt;&lt;/li&gt;\r\n&lt;li&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;em&gt;dolor sit amet&lt;/em&gt;&lt;/span&gt;&lt;/li&gt;\r\n&lt;/ol&gt;\r\n&lt;/div&gt;'),
(9, 3, '2024-07-07', '2024-07-14', 'Rancangan BPMN dan Business User', '&lt;div&gt;\r\n&lt;div&gt;Lorem ipsum dolor sit amet consectetur adipisicing elit. Dolores, &lt;em&gt;&lt;strong&gt;ex&lt;/strong&gt;&lt;/em&gt; voluptate! &lt;span style=&quot;text-decoration: underline;&quot;&gt;&lt;em&gt;Voluptates, voluptatum quo! Voluptas sequi voluptatibus laboriosam molestias asperiores.&lt;/em&gt;&lt;/span&gt;&lt;/div&gt;\r\n&lt;div&gt;&amp;nbsp;&lt;/div&gt;\r\n&lt;div&gt;\r\n&lt;ol&gt;\r\n&lt;li&gt;&lt;span style=&quot;text-decoration: underline;&quot;&gt;hello world!&lt;/span&gt;&lt;/li&gt;\r\n&lt;li style=&quot;font-weight: bold;&quot;&gt;&lt;strong&gt;lorem ipsum dolor sit amet&lt;/strong&gt;&lt;/li&gt;\r\n&lt;li style=&quot;font-style: italic;&quot;&gt;&lt;em&gt;kameha meha&lt;/em&gt;&lt;/li&gt;\r\n&lt;/ol&gt;\r\n&lt;/div&gt;\r\n&lt;/div&gt;');

-- --------------------------------------------------------

--
-- Table structure for table `kelas`
--

CREATE TABLE `kelas` (
  `id` int(11) NOT NULL,
  `nama_kelas` char(2) NOT NULL,
  `prodi_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kelas`
--

INSERT INTO `kelas` (`id`, `nama_kelas`, `prodi_id`) VALUES
(1, '1A', 1),
(2, '2A', 1),
(3, '3A', 1);

-- --------------------------------------------------------

--
-- Table structure for table `kelompok`
--

CREATE TABLE `kelompok` (
  `id` int(11) NOT NULL,
  `dosen_pembimbing_id` int(11) NOT NULL,
  `semester` varchar(20) NOT NULL,
  `tahun_ajaran` varchar(20) NOT NULL,
  `kelas_id` int(11) NOT NULL,
  `kode_kelompok` char(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kelompok`
--

INSERT INTO `kelompok` (`id`, `dosen_pembimbing_id`, `semester`, `tahun_ajaran`, `kelas_id`, `kode_kelompok`) VALUES
(4, 8, '4', '2023/2024', 2, '10'),
(5, 9, '4', '2023/2024', 2, '11'),
(6, 10, '4', '2023/2024', 2, '12');

-- --------------------------------------------------------

--
-- Table structure for table `koordinator`
--

CREATE TABLE `koordinator` (
  `id` int(11) NOT NULL,
  `nidn` char(10) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `gambar` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `koordinator`
--

INSERT INTO `koordinator` (`id`, `nidn`, `nama`, `user_id`, `gambar`) VALUES
(2, '0427128204', 'Ridwan Gosling S.T.,M.T.', 31, '6684bf5e48b14.png'),
(3, '1401202409', 'Supono Syafiq S.T.,M.T.', 39, '6687dc7a93b23.png'),
(4, '0428041420', 'Supono S.T.,M.T.', 44, 'default.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `npm` char(9) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `prodi_id` int(11) NOT NULL,
  `kelas_id` int(11) NOT NULL,
  `semester` int(11) NOT NULL,
  `tahun_angkatan` year(4) NOT NULL,
  `gambar` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mahasiswa`
--

INSERT INTO `mahasiswa` (`id`, `user_id`, `npm`, `nama`, `prodi_id`, `kelas_id`, `semester`, `tahun_angkatan`, `gambar`) VALUES
(20, 42, '623220001', 'Hana Meysha Berliana', 1, 2, 4, '2022', '6688e722d3d51.png'),
(21, 43, '623220002', 'May Hanny Khoirunnisa', 1, 2, 4, '2022', 'default.jpeg'),
(22, 48, '623220003', 'Neneng Qokom Komalasari', 1, 2, 4, '2022', 'default.jpeg'),
(23, 49, '623220004', 'Asep Supriyadi', 1, 2, 4, '2022', 'default.jpeg'),
(24, 51, '623220005', 'Sarah Harianja', 1, 2, 4, '2022', 'default.jpeg'),
(25, 52, '623220006', 'Zulfan Hamka', 1, 2, 4, '2022', 'default.jpeg'),
(26, 53, '623220007', 'Arini Putri Pramesti', 1, 2, 8, '2022', '669127316ce77.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `plotting`
--

CREATE TABLE `plotting` (
  `id` int(11) NOT NULL,
  `koordinator_id` int(11) NOT NULL,
  `kelompok_id` int(11) NOT NULL,
  `dosen_penguji_1_id` int(11) DEFAULT NULL,
  `dosen_penguji_2_id` int(11) DEFAULT NULL,
  `mahasiswa_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `jenis_plotting_id` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `plotting`
--

INSERT INTO `plotting` (`id`, `koordinator_id`, `kelompok_id`, `dosen_penguji_1_id`, `dosen_penguji_2_id`, `mahasiswa_id`, `project_id`, `jenis_plotting_id`, `created_at`, `updated_at`) VALUES
(11, 4, 4, NULL, NULL, 20, 1, 1, 1720890100, 1720891653),
(12, 4, 4, NULL, NULL, 21, 1, 1, 1720890151, 1720890151),
(13, 4, 5, NULL, NULL, 22, 1, 1, 1720890175, 1720890175),
(14, 4, 6, NULL, NULL, 23, 1, 1, 1720890200, 1720890200),
(15, 4, 4, NULL, NULL, 24, 1, 1, 1720890216, 1720890216),
(16, 4, 5, NULL, NULL, 25, 1, 1, 1720890233, 1720890233),
(17, 4, 6, NULL, NULL, 26, 1, 1, 1720890245, 1720894355);

-- --------------------------------------------------------

--
-- Table structure for table `prodi`
--

CREATE TABLE `prodi` (
  `id` int(11) NOT NULL,
  `kode_prodi` char(5) NOT NULL,
  `nama_prodi` varchar(255) NOT NULL,
  `jenjang` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prodi`
--

INSERT INTO `prodi` (`id`, `kode_prodi`, `nama_prodi`, `jenjang`) VALUES
(1, '57401', 'Manajemen Informatika', 'D3');

-- --------------------------------------------------------

--
-- Table structure for table `project`
--

CREATE TABLE `project` (
  `id` int(11) NOT NULL,
  `nama_project` varchar(50) NOT NULL,
  `deskripsi` varchar(255) NOT NULL,
  `tgl_mulai` date NOT NULL,
  `tgl_selesai` date NOT NULL,
  `prodi_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `project`
--

INSERT INTO `project` (`id`, `nama_project`, `deskripsi`, `tgl_mulai`, `tgl_selesai`, `prodi_id`) VALUES
(1, 'Mata Kuliah Proyek 2', 'Mata kuliah proyek 2 adalah mata kuliah wajib diikuti oleh semua mahasiswa D3 Manajemen Majestica', '2024-06-28', '2024-09-24', 1);

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `id` int(11) NOT NULL,
  `nama_role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`id`, `nama_role`) VALUES
(1, 'staff'),
(2, 'koordinator'),
(3, 'dosen'),
(4, 'mahasiswa'),
(5, 'kaprodi');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `no_telp` varchar(15) DEFAULT NULL,
  `is_active` int(11) NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `role_id`, `nama`, `no_telp`, `is_active`, `created_at`, `updated_at`) VALUES
(41, 'admin01', '$2y$10$q2JsSVVj0VONUtUoavpSZu9LkcoPqIXygYuiYLKxZDGla/oPfAPy6', 1, 'Muhamad Ridwan', '081579729332', 1, 1720237549, 1720237549),
(42, '623220001', '$2y$10$ZbLpkynWm.XKhACgtkJycO9j/jroAVIpClT9fehk2dUnC./mEr3he', 4, 'Hana Meysha Berliana', '087879729338', 1, 1720238255, 1720238667),
(43, '623220002', '$2y$10$M5EhwTskYP3cWISLx2BdbOnBp9JinFvvhph/Dv.aq86MVC2FkvBCi', 4, 'May Hanny Khoirunnisa', '087879707977', 1, 1720238832, 1720239447),
(44, 'supono@koordinator', '$2y$10$cTGv.4KWO3qeNyv8eeXVHOuv4.XwrnYOQN9ud5tI1Kz3PQFGVBbkS', 2, 'Supono S.T.,M.T.', '', 1, 1720240940, 1720240940),
(45, 'dosen01', '$2y$10$VrmeOeG3.Th8AvpNZ20xIu9teFcscdhWlm7AXMDCG51WxPdIp75Cm', 3, 'Dr. Tyler Durden S.H.,M.IPA.', '', 1, 1720244273, 1720244273),
(46, 'dosen02', '$2y$10$tlw8PbeombdRunTUfTpXc.7IopUcDATTirumif1BcwZutoR3YEJM.', 3, 'Salim Bahanan S.Pd.,M.BKM.', '', 1, 1720245699, 1720245699),
(47, 'dosen03', '$2y$10$QB6uY3ic4NFOVv1DNVXlMOZ62ZYEFd0cEoPRG4cz7NzEURVh00UAa', 3, 'Muzammil Hasballah S.I.,M.TQ.', '', 1, 1720245864, 1720245864),
(48, '623220003', '$2y$10$hpv9Pi7yoXJAHQ3o4eQEJOGg2ZkCkDlCXUMTg18ptbBDWhgxOXrOK', 4, 'Neneng Qokom Komalasari', '', 1, 1720332526, 1720332526),
(49, '623220004', '$2y$10$6ewcAaR.IsLYlE6cXZQYcu02mmj/teGZ/en40GEB6plNdpnefwde6', 4, 'Asep Supriyadi', '', 1, 1720355091, 1720355091),
(50, 'kaprodi01', '$2y$10$2nQGR4uUIEiBwuTEAoAFi.SEV9LKTLlifxXFjLTSwReoXSLAIueMS', 5, 'Mubassiran S.T.,M.T.', '085163837291', 1, 1720422972, 1720422972),
(51, '623220005', '$2y$10$XPL00TSHG9jDQwHc6vOQj.bKMUMwYrKjOmXwgs.J9.kxZ9Tna3txS', 4, 'Sarah Harianja', '', 1, 1720423209, 1720423449),
(52, '623220006', '$2y$10$NJjSHErHs2XSFpd4EndqduwM/Noi9KnBLxXC7hI928HI4ChhVfBPG', 4, 'Zulfan Hamka', '', 1, 1720423427, 1720423427),
(53, '623220007', '$2y$10$yRcwDHIhDlNa0ars/YX1l.bbWk0s9PNXmWf20DTAOr/YNCzIgW4xW', 4, 'Arini Putri Pramesti', '', 1, 1720591202, 1720591202);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `absensi_bimbingan`
--
ALTER TABLE `absensi_bimbingan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dosen`
--
ALTER TABLE `dosen`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jadwal`
--
ALTER TABLE `jadwal`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jenis_plotting`
--
ALTER TABLE `jenis_plotting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kegiatan`
--
ALTER TABLE `kegiatan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kelompok`
--
ALTER TABLE `kelompok`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `koordinator`
--
ALTER TABLE `koordinator`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `plotting`
--
ALTER TABLE `plotting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `prodi`
--
ALTER TABLE `prodi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project`
--
ALTER TABLE `project`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `absensi_bimbingan`
--
ALTER TABLE `absensi_bimbingan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dosen`
--
ALTER TABLE `dosen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `jadwal`
--
ALTER TABLE `jadwal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `jenis_plotting`
--
ALTER TABLE `jenis_plotting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `kegiatan`
--
ALTER TABLE `kegiatan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `kelas`
--
ALTER TABLE `kelas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `kelompok`
--
ALTER TABLE `kelompok`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `koordinator`
--
ALTER TABLE `koordinator`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `plotting`
--
ALTER TABLE `plotting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `prodi`
--
ALTER TABLE `prodi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `project`
--
ALTER TABLE `project`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
