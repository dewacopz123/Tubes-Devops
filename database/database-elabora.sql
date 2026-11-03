-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 19, 2026 at 01:28 PM
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
-- Database: `elabora_db`
--
CREATE DATABASE IF NOT EXISTS `elabora_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `elabora_db`;

-- --------------------------------------------------------

--
-- Table structure for table `akun`
--

CREATE TABLE `akun` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(120) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('PASIEN','DOKTER','PETUGAS') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `akun`
--

INSERT INTO `akun` (`id`, `username`, `email`, `password_hash`, `role`, `created_at`, `updated_at`) VALUES
(1, 'pasienbaru', 'pasienbaru@example.com', '$2b$10$u6sTenk8N8DRXUVNZVkLLOqO.foc236y.xO1TfxrjJi/E2oV7HDO2', 'PASIEN', '2025-12-18 20:43:18', '2025-12-18 20:43:18'),
(2, 'ilhamridzuan', 'ilhamridzuaan@gmail.com', '$2b$10$yLUIfN1bUHpqlkpsGaGwNex7xCy6S/xhq7uQxZQfG1s7s31FtDJq.', 'PASIEN', '2025-12-19 11:26:47', '2025-12-19 11:26:47'),
(3, 'alfian', 'alfian@gmail.com', '$2b$10$p4hm9bi/2FXw9sfveQboGed2APPxoP4GVcK3g3lpAfE2lhqreCsiq', 'PASIEN', '2025-12-19 11:29:34', '2025-12-19 11:29:34'),
(5, 'pasien1', 'pasien@mail.com', '$2b$10$H/0g/EhwZr7O1tyGjqiBg.fLIPa6l16E3Y0EQWFtFB2GcbyQdMvJS', 'PASIEN', NULL, NULL),
(10, 'Andre Wijaya', 'andre@elabora.test', '$2b$10$Q0z0lQq7Xb5jN4cZ3Z8M8u9t7rWlYf8sR8z4Zy4kC8yJ9Yp1QJXn6', 'DOKTER', '2025-12-21 07:59:36', NULL),
(11, 'Siti Aminah', 'lab1@elabora.test', '$2b$10$Q0z0lQq7Xb5jN4cZ3Z8M8u9t7rWlYf8sR8z4Zy4kC8yJ9Yp1QJXn6', 'PETUGAS', '2025-12-21 07:59:36', NULL),
(13, 'PetugasDemo', 'lab2@elabora.test', 'password123', 'PETUGAS', '2025-12-21 08:23:26', NULL),
(14, 'petugas1', 'petugas123@mail.com', '$2b$10$DYdy1rGxTiUAypTSPBaseep9b8Ur0apBvAWO0Js6Yv6APRd21w36C', 'PETUGAS', '2025-12-21 08:57:48', '2025-12-21 08:57:48'),
(15, 'doktertest', 'doktertest@mail.com', '$2b$10$2X8hFbzH6CazdHDykyyhcuc2mei1QgGZmPeJXcpxhDVSeymW3/VVe', 'PETUGAS', '2025-12-21 08:59:18', '2025-12-21 08:59:18'),
(16, 'doktertest1', 'doktertest1@mail.com', '$2b$10$eUDBNtVwGo.eGHLTy9MXKuKkeOjV.F7mlXPMaSRrhgCTiMu9apGrm', 'DOKTER', '2025-12-21 08:59:40', '2025-12-21 08:59:40'),
(17, 'Pieter', 'pieterg@mail.com', '$2b$10$UQrYEKMuYbFNQwnmG8e7FOHliuL5ss6hU5xBjq5pakIF3VYuv.13y', 'PASIEN', '2025-12-22 22:10:31', '2025-12-22 22:10:31'),
(18, 'misteraloy', 'misteraloy@gmail.com', '$2b$12$EN.4KfKxwd81kE4N6ldP9./Ykr5piYsO9NzT0rcXpvnfCZl8mr3fe', 'PASIEN', '2026-04-28 06:05:27', '2026-04-28 06:05:27'),
(19, 'pakroni', 'roni@mail.com', '$2b$12$H44rf7a568eh30gUJA281.4fedvDqj1ohNLfjmD1mPuHSQVv74Txy', 'PASIEN', '2026-04-28 06:10:25', '2026-04-28 06:10:25'),
(20, 'kiboo', 'kiboo@gmail.com', '$2b$12$ZKhZrf2qs6EG6V8C06/a6eVjjiuVRLep0lEcUiTAYvECe02PUu3q.', 'PASIEN', '2026-05-13 02:42:15', '2026-05-13 02:42:15'),
(21, 'ButuhUang', 'darrel@happi.co', '$2b$12$jeXwi2OMajMO6Mk4yO2HUuFFLLTrJYliAkvQ1SnB9sIdbl8f9QuSu', 'PASIEN', '2026-05-13 02:56:00', '2026-05-13 02:56:00'),
(22, 'muhammadfarras', 'lucyinthesky@yahoo.com', '$2b$12$DrT5tQLTd9AArIOx2zQiieDeOhbCPpyA2QD.W7JOKabKi1Fs.AXgy', 'PASIEN', '2026-05-13 03:09:57', '2026-05-13 03:09:57');

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `entity` varchar(50) NOT NULL,
  `entity_id` bigint(20) UNSIGNED NOT NULL,
  `aksi` enum('CREATE','UPDATE','DELETE','VALIDATE') NOT NULL,
  `changed_by_akun_id` bigint(20) UNSIGNED DEFAULT NULL,
  `changed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `detail` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`detail`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_log`
--

INSERT INTO `audit_log` (`id`, `entity`, `entity_id`, `aksi`, `changed_by_akun_id`, `changed_at`, `detail`) VALUES
(1, 'pendaftaran', 2, '', 14, '2025-12-21 09:24:32', '{\"from\":\"MENUNGGU\",\"to\":\"DILAYANI\"}'),
(2, 'pendaftaran', 2, '', 14, '2025-12-21 09:24:54', '{\"from\":\"DILAYANI\",\"to\":\"SELESAI\"}'),
(3, 'pendaftaran', 3, '', 14, '2025-12-21 09:24:54', '{\"from\":\"MENUNGGU\",\"to\":\"DILAYANI\",\"autoNext\":true}'),
(4, 'pendaftaran', 3, '', 14, '2025-12-21 09:25:16', '{\"from\":\"DILAYANI\",\"to\":\"SELESAI\"}'),
(5, 'pendaftaran', 4, '', 14, '2025-12-21 09:25:16', '{\"from\":\"MENUNGGU\",\"to\":\"DILAYANI\",\"autoNext\":true}'),
(6, 'pendaftaran', 4, '', 14, '2025-12-21 09:25:25', '{\"from\":\"DILAYANI\",\"to\":\"DIBATALKAN\",\"reason\":null}'),
(7, 'pendaftaran', 6, '', 14, '2025-12-22 22:03:31', '{\"from\":\"MENUNGGU\",\"to\":\"DIBATALKAN\",\"reason\":null}'),
(8, 'pendaftaran', 7, '', 14, '2025-12-22 22:13:48', '{\"from\":\"MENUNGGU\",\"to\":\"DILAYANI\"}'),
(9, 'pemeriksaan', 7, 'CREATE', 14, '2025-12-22 22:22:57', '\"Pemeriksaan created\"'),
(10, 'pemeriksaan', 8, 'CREATE', 14, '2025-12-22 22:23:48', '\"Pemeriksaan created\"'),
(11, 'pemeriksaan', 8, 'UPDATE', 14, '2025-12-22 22:24:19', '\"File attached to pemeriksaan\"'),
(12, 'pemeriksaan', 9, 'CREATE', 14, '2025-12-22 22:29:16', '\"Pemeriksaan created\"'),
(13, 'pemeriksaan', 7, 'UPDATE', 14, '2025-12-22 22:30:43', '\"Pemeriksaan updated\"'),
(14, 'pemeriksaan', 9, 'UPDATE', 14, '2025-12-22 22:32:08', '\"Pemeriksaan updated\"');

-- --------------------------------------------------------

--
-- Table structure for table `dokter`
--

CREATE TABLE `dokter` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `akun_id` bigint(20) UNSIGNED NOT NULL,
  `nip` varchar(30) DEFAULT NULL,
  `nama` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dokter`
--

INSERT INTO `dokter` (`id`, `akun_id`, `nip`, `nama`, `created_at`, `updated_at`) VALUES
(2, 10, '12345678901234567890', 'dr. Andre Wijaya', '2025-12-21 07:59:36', NULL),
(3, 16, '09876543210987654321', 'Test Dokter 1', '2025-12-21 08:59:40', '2025-12-21 08:59:40');

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(80) NOT NULL,
  `deskripsi` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id`, `nama`, `deskripsi`, `created_at`, `updated_at`) VALUES
(1, 'Patologi', 'patologi klinik merupakan pemeriksaan morfologis, mikroskopis, kimia, mikrobiologis, serologis, hematologis, imunologis, parasitologis, dan pemeriksaan laboratorium lainnya.', '2025-12-21 09:49:48', '2025-12-21 09:49:48'),
(2, 'Anatomi', NULL, '2025-12-22 10:42:41', '2025-12-22 10:42:41'),
(3, 'Mikrobiologi', NULL, '2025-12-22 10:43:02', '2025-12-22 10:43:02');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2025_12_18_083830_create_akuns_table', 1),
(2, '2025_12_18_083830_create_pasiens_table', 1),
(3, '2025_12_18_083831_create_dokters_table', 1),
(4, '2025_12_18_083831_create_petugas_labs_table', 1),
(5, '2025_12_18_083832_create_kategoris_table', 1),
(6, '2025_12_18_083833_create_pendaftarans_table', 1),
(7, '2025_12_18_083835_create_pemeriksaans_table', 1),
(8, '2025_12_18_083836_create_pemeriksaan_details_table', 1),
(9, '2025_12_18_083837_create_pemeriksaan_files_table', 1),
(10, '2025_12_18_083838_create_audit_logs_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `pasien`
--

CREATE TABLE `pasien` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `akun_id` bigint(20) UNSIGNED NOT NULL,
  `nik` char(16) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `tgl_lahir` date DEFAULT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `no_telepon` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pasien`
--

INSERT INTO `pasien` (`id`, `akun_id`, `nik`, `nama`, `tgl_lahir`, `jenis_kelamin`, `alamat`, `no_telepon`, `created_at`, `updated_at`) VALUES
(1, 1, '3170000000000002', 'Siti Aisyah', '2001-01-20', 'L', 'Tanjungpinang', '081234000002', '2025-12-18 20:43:18', '2025-12-18 20:43:18'),
(2, 2, '1234567890123456', 'Muhammad Ilham Ridzuan', NULL, 'L', 'Jl.D.I Pandjaitan', '089621733577', '2025-12-19 11:26:47', '2025-12-19 11:26:47'),
(3, 3, '1234567890123457', 'Alfian Rizky Sabian', '2025-12-19', 'L', 'Jl.D.I Pandjaitan', '089621733576', '2025-12-19 11:29:34', '2025-12-19 11:29:34'),
(5, 5, '1234567890123458', 'Pasien Satu', '1999-01-01', 'L', 'Jl. Contoh', '08123456789', NULL, NULL),
(6, 17, '0987654321123456', 'Pieter Immanuel', '0000-00-00', '', 'baleendah', '081234654321', '2025-12-22 22:10:31', '2025-12-22 22:10:31'),
(7, 18, '0000111177774444', 'mister aloy', '2000-01-28', 'L', 'Jl.D.I Pandjaitan', '08976543221234', '2026-04-28 06:05:27', '2026-04-28 06:05:27'),
(8, 19, '1111000022224444', 'Yuliant Sibaroni', '1969-07-28', 'L', 'Jl.D.I Pandjaitan', '08452345673456', '2026-04-28 06:10:25', '2026-04-28 06:10:25'),
(9, 20, '1234567891011121', 'jack kelvin', '2005-03-24', 'L', 'jln girimekar', '082240004990', '2026-05-13 02:42:15', '2026-05-13 02:42:15'),
(10, 21, '1849285869490204', 'Timotius Darrel', '2005-01-01', 'L', 'Gang PGA', '084522567921', '2026-05-13 02:56:00', '2026-05-13 02:56:00'),
(11, 22, '3671111290533333', 'Muhammad Farras', '2005-05-29', 'L', 'tangerang', '9826262626262', '2026-05-13 03:09:57', '2026-05-13 03:09:57');

-- --------------------------------------------------------

--
-- Table structure for table `pemeriksaan`
--

CREATE TABLE `pemeriksaan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pendaftaran_id` bigint(20) UNSIGNED NOT NULL,
  `kategori_id` bigint(20) UNSIGNED NOT NULL,
  `dokter_id` bigint(20) UNSIGNED DEFAULT NULL,
  `petugas_lab_id` bigint(20) UNSIGNED NOT NULL,
  `tgl_pemeriksaan` datetime NOT NULL DEFAULT current_timestamp(),
  `status_validasi` enum('DRAFT','TERVALIDASI') NOT NULL DEFAULT 'DRAFT',
  `status_hasil` enum('MENUNGGU_HASIL','HASIL_TERSEDIA','TIDAK_TERSEDIA') NOT NULL DEFAULT 'MENUNGGU_HASIL',
  `catatan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pemeriksaan`
--

INSERT INTO `pemeriksaan` (`id`, `pendaftaran_id`, `kategori_id`, `dokter_id`, `petugas_lab_id`, `tgl_pemeriksaan`, `status_validasi`, `status_hasil`, `catatan`, `created_at`, `updated_at`) VALUES
(6, 2, 1, NULL, 4, '2025-12-21 17:57:01', 'TERVALIDASI', 'HASIL_TERSEDIA', 'Pemeriksaan awal', '2025-12-21 10:57:01', '2025-12-21 11:10:13'),
(7, 3, 2, NULL, 4, '2025-12-23 05:22:57', 'DRAFT', 'MENUNGGU_HASIL', 'Pemeriksaan awal', '2025-12-22 22:22:57', '2025-12-22 22:30:43'),
(8, 2, 3, NULL, 4, '2025-12-23 05:23:48', 'DRAFT', 'HASIL_TERSEDIA', 'Pemeriksaan awal', '2025-12-22 22:23:48', '2025-12-22 22:23:48'),
(9, 7, 3, NULL, 4, '2025-12-23 05:29:16', 'DRAFT', 'MENUNGGU_HASIL', 'Pemeriksaan awal', '2025-12-22 22:29:16', '2025-12-22 22:32:08');

-- --------------------------------------------------------

--
-- Table structure for table `pemeriksaan_detail`
--

CREATE TABLE `pemeriksaan_detail` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pemeriksaan_id` bigint(20) UNSIGNED NOT NULL,
  `parameter_nama` varchar(80) NOT NULL,
  `nilai` varchar(80) NOT NULL,
  `satuan` varchar(30) DEFAULT NULL,
  `rujukan_min` varchar(30) DEFAULT NULL,
  `rujukan_max` varchar(30) DEFAULT NULL,
  `flag` enum('NORMAL','ABNORMAL','KRITIS') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pemeriksaan_file`
--

CREATE TABLE `pemeriksaan_file` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pemeriksaan_id` bigint(20) UNSIGNED NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` enum('PDF','JPG','PNG') NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pemeriksaan_file`
--

INSERT INTO `pemeriksaan_file` (`id`, `pemeriksaan_id`, `file_path`, `file_type`, `uploaded_at`) VALUES
(1, 6, '/uploads/1766315658591-5e5e8097edcae.pdf', '', '2025-12-21 11:14:18'),
(2, 6, '/uploads/1766315677632-3fc6bd4aaaab28.pdf', '', '2025-12-21 11:14:37'),
(3, 8, '/uploads/1766442259451-05466b6a56b01.pdf', '', '2025-12-22 22:24:19');

-- --------------------------------------------------------

--
-- Table structure for table `pendaftaran`
--

CREATE TABLE `pendaftaran` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pasien_id` bigint(20) UNSIGNED NOT NULL,
  `no_antrian` int(10) UNSIGNED NOT NULL,
  `no_lab` varchar(25) NOT NULL,
  `jadwal_pemeriksaan_at` datetime NOT NULL,
  `tanggal_antrian` date NOT NULL,
  `status` enum('MENUNGGU','DILAYANI','SELESAI','DIBATALKAN') NOT NULL DEFAULT 'MENUNGGU',
  `surat_rujukan_path` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pendaftaran`
--

INSERT INTO `pendaftaran` (`id`, `pasien_id`, `no_antrian`, `no_lab`, `jadwal_pemeriksaan_at`, `tanggal_antrian`, `status`, `surat_rujukan_path`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 'LAB-20251225-0001', '2025-12-25 10:00:00', '2025-12-25', 'MENUNGGU', 'src/uploads/surat-rujukan/rujukan_1766308529640_323145236.pdf', '2025-12-21 09:15:29', '2025-12-21 09:15:29'),
(2, 2, 1, 'LAB-20251221-0002', '2025-12-25 10:00:00', '2025-12-21', 'SELESAI', 'src/uploads/surat-rujukan/rujukan_1766308663141_218759126.pdf', '2025-12-21 09:17:43', '2025-12-21 09:17:43'),
(3, 2, 2, 'LAB-20251221-0003', '2025-12-25 10:00:00', '2025-12-21', 'SELESAI', 'src/uploads/surat-rujukan/rujukan_1766308678778_919222058.pdf', '2025-12-21 09:17:58', '2025-12-21 09:17:58'),
(4, 2, 3, 'LAB-20251221-0004', '2025-12-25 10:00:00', '2025-12-21', 'DIBATALKAN', 'src/uploads/surat-rujukan/rujukan_1766308680365_822194458.pdf', '2025-12-21 09:18:00', '2025-12-21 09:18:00'),
(5, 2, 1, 'LAB-20251222-0005', '2025-12-22 22:31:00', '2025-12-22', 'MENUNGGU', 'src/uploads/surat-rujukan/rujukan_1766417476517_798928991.pdf', '2025-12-22 15:31:16', '2025-12-22 15:31:16'),
(6, 2, 1, 'LAB-20251223-0006', '2025-12-23 01:21:00', '2025-12-23', 'DIBATALKAN', 'src/uploads/surat-rujukan/rujukan_1766427724729_394322052.pdf', '2025-12-22 18:22:05', '2025-12-22 18:22:05'),
(7, 3, 2, 'LAB-20251223-0007', '2025-12-23 09:15:00', '2025-12-23', 'DILAYANI', 'src/uploads/surat-rujukan/rujukan_1766441596478_697608970.pdf', '2025-12-22 22:13:16', '2025-12-22 22:13:16'),
(8, 6, 3, 'LAB-20251223-0008', '2025-12-23 09:25:00', '2025-12-23', 'MENUNGGU', 'src/uploads/surat-rujukan/rujukan_1766441791849_62321503.pdf', '2025-12-22 22:16:31', '2025-12-22 22:16:31'),
(9, 9, 1, 'LAB-20260513-0009', '2026-05-13 10:43:00', '2026-05-13', 'MENUNGGU', 'src/uploads/surat-rujukan/rujukan_1778640233843_897555678.jpg', '2026-05-13 02:43:53', '2026-05-13 02:43:53'),
(10, 10, 1, 'LAB-20260518-0010', '2026-05-18 12:00:00', '2026-05-18', 'MENUNGGU', 'src/uploads/surat-rujukan/rujukan_1778641098633_148723081.png', '2026-05-13 02:58:18', '2026-05-13 02:58:18'),
(11, 11, 2, 'LAB-20260513-0011', '2026-05-13 01:10:00', '2026-05-13', 'MENUNGGU', 'src/uploads/surat-rujukan/rujukan_1778641853209_359586589.jpg', '2026-05-13 03:10:53', '2026-05-13 03:10:53');

-- --------------------------------------------------------

--
-- Table structure for table `petugas_lab`
--

CREATE TABLE `petugas_lab` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `akun_id` bigint(20) UNSIGNED NOT NULL,
  `nip` varchar(30) DEFAULT NULL,
  `nama` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `petugas_lab`
--

INSERT INTO `petugas_lab` (`id`, `akun_id`, `nip`, `nama`, `created_at`, `updated_at`) VALUES
(1, 11, '09876543210987654321', 'Siti Aminah', '2025-12-21 07:59:37', NULL),
(3, 13, '09876543210987654321', 'Petugas Demo', '2025-12-21 08:24:09', NULL),
(4, 14, '12345678901234567890', 'Pasien Satu', '2025-12-21 08:57:48', '2025-12-21 08:57:48'),
(5, 15, '09876543210987654321', 'Test Dokter', '2025-12-21 08:59:18', '2025-12-21 08:59:18');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `akun`
--
ALTER TABLE `akun`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `akun_username_unique` (`username`),
  ADD UNIQUE KEY `akun_email_unique` (`email`);

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_log_changed_by_akun_id_foreign` (`changed_by_akun_id`),
  ADD KEY `idx_audit_entity` (`entity`,`entity_id`);

--
-- Indexes for table `dokter`
--
ALTER TABLE `dokter`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_dokter_akun` (`akun_id`),
  ADD KEY `dokter_nip_index` (`nip`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kategori_nama_unique` (`nama`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pasien`
--
ALTER TABLE `pasien`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_pasien_akun` (`akun_id`),
  ADD UNIQUE KEY `pasien_nik_unique` (`nik`),
  ADD KEY `idx_pasien_nama` (`nama`),
  ADD KEY `idx_pasien_nik` (`nik`),
  ADD KEY `idx_pasien_no_telepon` (`no_telepon`),
  ADD KEY `idx_pasien_tgl_lahir` (`tgl_lahir`),
  ADD KEY `idx_pasien_created_at` (`created_at`);

--
-- Indexes for table `pemeriksaan`
--
ALTER TABLE `pemeriksaan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_pendaftaran_kategori` (`pendaftaran_id`,`kategori_id`),
  ADD KEY `pemeriksaan_kategori_id_foreign` (`kategori_id`),
  ADD KEY `pemeriksaan_dokter_id_foreign` (`dokter_id`),
  ADD KEY `pemeriksaan_petugas_lab_id_foreign` (`petugas_lab_id`),
  ADD KEY `idx_pemeriksaan_tgl` (`tgl_pemeriksaan`);

--
-- Indexes for table `pemeriksaan_detail`
--
ALTER TABLE `pemeriksaan_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pemeriksaan_detail_pemeriksaan_id_foreign` (`pemeriksaan_id`);

--
-- Indexes for table `pemeriksaan_file`
--
ALTER TABLE `pemeriksaan_file`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pemeriksaan_file_pemeriksaan_id_foreign` (`pemeriksaan_id`);

--
-- Indexes for table `pendaftaran`
--
ALTER TABLE `pendaftaran`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_tanggal_no_antrian` (`tanggal_antrian`,`no_antrian`),
  ADD UNIQUE KEY `uq_no_lab` (`no_lab`),
  ADD KEY `pendaftaran_pasien_id_foreign` (`pasien_id`),
  ADD KEY `idx_pendaftaran_tgl` (`jadwal_pemeriksaan_at`),
  ADD KEY `idx_pendaftaran_status` (`status`);

--
-- Indexes for table `petugas_lab`
--
ALTER TABLE `petugas_lab`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_petugas_akun` (`akun_id`),
  ADD KEY `petugas_lab_nip_index` (`nip`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `akun`
--
ALTER TABLE `akun`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `dokter`
--
ALTER TABLE `dokter`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `pasien`
--
ALTER TABLE `pasien`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `pemeriksaan`
--
ALTER TABLE `pemeriksaan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `pemeriksaan_detail`
--
ALTER TABLE `pemeriksaan_detail`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pemeriksaan_file`
--
ALTER TABLE `pemeriksaan_file`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pendaftaran`
--
ALTER TABLE `pendaftaran`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `petugas_lab`
--
ALTER TABLE `petugas_lab`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD CONSTRAINT `audit_log_changed_by_akun_id_foreign` FOREIGN KEY (`changed_by_akun_id`) REFERENCES `akun` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `dokter`
--
ALTER TABLE `dokter`
  ADD CONSTRAINT `dokter_akun_id_foreign` FOREIGN KEY (`akun_id`) REFERENCES `akun` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `pasien`
--
ALTER TABLE `pasien`
  ADD CONSTRAINT `pasien_akun_id_foreign` FOREIGN KEY (`akun_id`) REFERENCES `akun` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `pemeriksaan`
--
ALTER TABLE `pemeriksaan`
  ADD CONSTRAINT `pemeriksaan_dokter_id_foreign` FOREIGN KEY (`dokter_id`) REFERENCES `dokter` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `pemeriksaan_kategori_id_foreign` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `pemeriksaan_pendaftaran_id_foreign` FOREIGN KEY (`pendaftaran_id`) REFERENCES `pendaftaran` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `pemeriksaan_petugas_lab_id_foreign` FOREIGN KEY (`petugas_lab_id`) REFERENCES `petugas_lab` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `pemeriksaan_detail`
--
ALTER TABLE `pemeriksaan_detail`
  ADD CONSTRAINT `pemeriksaan_detail_pemeriksaan_id_foreign` FOREIGN KEY (`pemeriksaan_id`) REFERENCES `pemeriksaan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pemeriksaan_file`
--
ALTER TABLE `pemeriksaan_file`
  ADD CONSTRAINT `pemeriksaan_file_pemeriksaan_id_foreign` FOREIGN KEY (`pemeriksaan_id`) REFERENCES `pemeriksaan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pendaftaran`
--
ALTER TABLE `pendaftaran`
  ADD CONSTRAINT `pendaftaran_pasien_id_foreign` FOREIGN KEY (`pasien_id`) REFERENCES `pasien` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `petugas_lab`
--
ALTER TABLE `petugas_lab`
  ADD CONSTRAINT `petugas_lab_akun_id_foreign` FOREIGN KEY (`akun_id`) REFERENCES `akun` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
