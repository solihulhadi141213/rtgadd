-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 29, 2025 at 02:21 PM
-- Server version: 9.1.0
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rtgadd`
--

-- --------------------------------------------------------

--
-- Table structure for table `access`
--

DROP TABLE IF EXISTS `access`;
CREATE TABLE IF NOT EXISTS `access` (
  `id_access` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_access_group` int DEFAULT NULL,
  `access_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_contact` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `access_password` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `access_foto` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `access_client` tinyint(1) NOT NULL COMMENT 'If true, the account is a client.',
  PRIMARY KEY (`id_access`),
  KEY `id_access_group` (`id_access_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `access_client`
--

DROP TABLE IF EXISTS `access_client`;
CREATE TABLE IF NOT EXISTS `access_client` (
  `id_access_client` int NOT NULL AUTO_INCREMENT,
  `id_access` int UNSIGNED NOT NULL,
  `id_region` int DEFAULT NULL,
  `level` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'National OR District OR Province',
  `keterangan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_access_client`),
  KEY `id_access` (`id_access`),
  KEY `id_region` (`id_region`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `access_feature`
--

DROP TABLE IF EXISTS `access_feature`;
CREATE TABLE IF NOT EXISTS `access_feature` (
  `id_access_feature` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `feature_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `feature_category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `feature_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `datetime_creat` timestamp NOT NULL,
  PRIMARY KEY (`id_access_feature`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `access_group`
--

DROP TABLE IF EXISTS `access_group`;
CREATE TABLE IF NOT EXISTS `access_group` (
  `id_access_group` int NOT NULL AUTO_INCREMENT,
  `group_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_access_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `access_log`
--

DROP TABLE IF EXISTS `access_log`;
CREATE TABLE IF NOT EXISTS `access_log` (
  `id_access_log` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_access` int UNSIGNED NOT NULL,
  `log_datetime` datetime NOT NULL,
  `log_category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `log_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_access_log`),
  KEY `access_log_id_access_index` (`id_access`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `access_login`
--

DROP TABLE IF EXISTS `access_login`;
CREATE TABLE IF NOT EXISTS `access_login` (
  `id_access_login` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_access` int UNSIGNED NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `datetime_creat` datetime NOT NULL,
  `datetime_expired` datetime NOT NULL,
  PRIMARY KEY (`id_access_login`),
  KEY `access_login_id_access_index` (`id_access`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `access_permission`
--

DROP TABLE IF EXISTS `access_permission`;
CREATE TABLE IF NOT EXISTS `access_permission` (
  `id_permission` int NOT NULL AUTO_INCREMENT,
  `id_access` int UNSIGNED NOT NULL,
  `id_access_feature` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id_permission`),
  KEY `id_access` (`id_access`),
  KEY `id_access_feature` (`id_access_feature`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `access_reference`
--

DROP TABLE IF EXISTS `access_reference`;
CREATE TABLE IF NOT EXISTS `access_reference` (
  `id_access_reference` int NOT NULL AUTO_INCREMENT,
  `id_access_group` int NOT NULL,
  `id_access_feature` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id_access_reference`),
  KEY `id_access_group` (`id_access_group`),
  KEY `id_access_fitures` (`id_access_feature`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `app_configuration`
--

DROP TABLE IF EXISTS `app_configuration`;
CREATE TABLE IF NOT EXISTS `app_configuration` (
  `id_configuration` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `app_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `app_keyword` json NOT NULL,
  `app_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `app_favicon` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `app_logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `app_base_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `app_author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `app_year` int NOT NULL,
  `app_company` json NOT NULL,
  PRIMARY KEY (`id_configuration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `captcha`
--

DROP TABLE IF EXISTS `captcha`;
CREATE TABLE IF NOT EXISTS `captcha` (
  `id_captcha` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `captcha` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `datetime_creat` datetime NOT NULL,
  `datetime_expired` datetime NOT NULL,
  PRIMARY KEY (`id_captcha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `help`
--

DROP TABLE IF EXISTS `help`;
CREATE TABLE IF NOT EXISTS `help` (
  `id_help` int NOT NULL AUTO_INCREMENT,
  `author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `judul` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `datetime_creat` datetime NOT NULL,
  `datetime_update` datetime NOT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Publish, Draft',
  PRIMARY KEY (`id_help`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `position`
--

DROP TABLE IF EXISTS `position`;
CREATE TABLE IF NOT EXISTS `position` (
  `id_position` int NOT NULL AUTO_INCREMENT,
  `position_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `position_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `position_region`
--

DROP TABLE IF EXISTS `position_region`;
CREATE TABLE IF NOT EXISTS `position_region` (
  `id_position_region` int NOT NULL AUTO_INCREMENT,
  `province` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Provinsi',
  `regency` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Kabupaten/Kota',
  `department` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Jabatan',
  `workload` int DEFAULT NULL COMMENT 'ABK',
  `officials_public` int DEFAULT NULL COMMENT 'ASN di Sekolah negeri',
  `officials_private` int DEFAULT NULL COMMENT 'ASN di Sekolah swasta',
  `manpower_gap` int DEFAULT NULL COMMENT 'Kekurangan ASN',
  PRIMARY KEY (`id_position_region`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `position_school`
--

DROP TABLE IF EXISTS `position_school`;
CREATE TABLE IF NOT EXISTS `position_school` (
  `id_position_school` int NOT NULL AUTO_INCREMENT,
  `id_school` int NOT NULL,
  `id_position` int NOT NULL,
  `abk` int DEFAULT NULL,
  `asn` int DEFAULT NULL,
  `PPPK2024` int DEFAULT NULL,
  `NonASN_sblmOkt2022` int DEFAULT NULL,
  `NonASN_stlhOkt2022` int DEFAULT NULL,
  `JmlGuru` int DEFAULT NULL,
  `KurangGuru` int DEFAULT NULL,
  PRIMARY KEY (`id_position_school`),
  KEY `id_school` (`id_school`),
  KEY `id_position` (`id_position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `region`
--

DROP TABLE IF EXISTS `region`;
CREATE TABLE IF NOT EXISTS `region` (
  `id_region` int NOT NULL AUTO_INCREMENT,
  `category` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Province OR District',
  `province_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Kode Provinsi',
  `province_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nama Provinsi',
  `district_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Kode Kab/Kot',
  `district_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nama Kab/Kot',
  `code_map` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Kode Peta',
  PRIMARY KEY (`id_region`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `school`
--

DROP TABLE IF EXISTS `school`;
CREATE TABLE IF NOT EXISTS `school` (
  `id_school` int NOT NULL AUTO_INCREMENT,
  `id_region` int NOT NULL,
  `npsn` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `school_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_school`),
  KEY `id_region` (`id_region`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `setting_email_gateway`
--

DROP TABLE IF EXISTS `setting_email_gateway`;
CREATE TABLE IF NOT EXISTS `setting_email_gateway` (
  `id_setting_email_gateway` int NOT NULL AUTO_INCREMENT,
  `email_gateway` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `password_gateway` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `url_provider` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `port_gateway` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_pengirim` varchar(25) CHARACTER SET latin1 COLLATE latin1_swedish_ci DEFAULT NULL,
  `url_service` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `validasi_email` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `redirect_validasi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pesan_validasi_email` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_setting_email_gateway`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `access`
--
ALTER TABLE `access`
  ADD CONSTRAINT `access_to_group` FOREIGN KEY (`id_access_group`) REFERENCES `access_group` (`id_access_group`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Constraints for table `access_client`
--
ALTER TABLE `access_client`
  ADD CONSTRAINT `client_to_access` FOREIGN KEY (`id_access`) REFERENCES `access` (`id_access`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `client_to_region` FOREIGN KEY (`id_region`) REFERENCES `region` (`id_region`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `access_log`
--
ALTER TABLE `access_log`
  ADD CONSTRAINT `access_log_id_access_foreign` FOREIGN KEY (`id_access`) REFERENCES `access` (`id_access`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `access_login`
--
ALTER TABLE `access_login`
  ADD CONSTRAINT `access_login_id_access_foreign` FOREIGN KEY (`id_access`) REFERENCES `access` (`id_access`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `access_permission`
--
ALTER TABLE `access_permission`
  ADD CONSTRAINT `permission_to_access` FOREIGN KEY (`id_access`) REFERENCES `access` (`id_access`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `permission_to_features` FOREIGN KEY (`id_access_feature`) REFERENCES `access_feature` (`id_access_feature`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `access_reference`
--
ALTER TABLE `access_reference`
  ADD CONSTRAINT `reference_to_feature` FOREIGN KEY (`id_access_feature`) REFERENCES `access_feature` (`id_access_feature`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reference_to_group` FOREIGN KEY (`id_access_group`) REFERENCES `access_group` (`id_access_group`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `position_school`
--
ALTER TABLE `position_school`
  ADD CONSTRAINT `position_to_position` FOREIGN KEY (`id_position`) REFERENCES `position` (`id_position`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `position_to_school` FOREIGN KEY (`id_school`) REFERENCES `school` (`id_school`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `school`
--
ALTER TABLE `school`
  ADD CONSTRAINT `shool_to_region` FOREIGN KEY (`id_region`) REFERENCES `region` (`id_region`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
