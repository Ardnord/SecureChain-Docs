-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 17, 2025 at 04:54 AM
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
-- Database: `poa_admin_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) UNSIGNED NOT NULL,
  `action_type` varchar(50) NOT NULL COMMENT 'CREATE, UPDATE, DELETE, MANIPULATE, RECOVER, CHECK',
  `block_id` int(11) UNSIGNED DEFAULT NULL,
  `identifier` varchar(255) DEFAULT NULL COMMENT 'Nomor permohonan atau identifier lain',
  `original_data` text DEFAULT NULL COMMENT 'JSON data sebelum perubahan',
  `modified_data` text DEFAULT NULL COMMENT 'JSON data setelah perubahan',
  `status` varchar(50) NOT NULL DEFAULT 'INFO' COMMENT 'Manipulated, Recovered, Success, Failed, INFO',
  `description` text DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blockchain_backup`
--

CREATE TABLE `blockchain_backup` (
  `id` int(10) UNSIGNED NOT NULL,
  `nomor_permohonan` varchar(100) NOT NULL,
  `nomor_dokumen` varchar(100) NOT NULL,
  `tanggal_dokumen` date NOT NULL,
  `tanggal_filing` date NOT NULL,
  `dokumen_base64` longtext NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `block_hash` varchar(64) NOT NULL,
  `previous_hash` varchar(64) NOT NULL,
  `timestamp` datetime NOT NULL,
  `backup_type` enum('auto','manual') NOT NULL DEFAULT 'auto',
  `backup_timestamp` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `blockchain_backup`
--
DELIMITER $$
CREATE TRIGGER `prevent_delete_blockchain_backup` BEFORE DELETE ON `blockchain_backup` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Data pada tabel blockchain_backup (poa_admin_db) tidak boleh dihapus';
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `ip_whitelist`
--

CREATE TABLE `ip_whitelist` (
  `id` int(10) UNSIGNED NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `added_by` varchar(50) NOT NULL DEFAULT 'admin',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ip_whitelist`
--

INSERT INTO `ip_whitelist` (`id`, `ip_address`, `description`, `is_active`, `added_by`, `created_at`, `updated_at`) VALUES
(1, '127.0.0.1', 'Localhost IPv4 (Development)', 1, 'system', '2025-10-27 02:20:49', NULL),
(2, '::1', 'Localhost IPv6 (Development)', 1, 'system', '2025-10-27 02:20:49', '2025-11-09 07:22:02');

-- --------------------------------------------------------

--
-- Table structure for table `recovery_history`
--

CREATE TABLE `recovery_history` (
  `id` int(11) UNSIGNED NOT NULL,
  `recovery_type` enum('consensus_auto','consensus_manual','rollback') DEFAULT 'consensus_auto',
  `source_db` varchar(50) DEFAULT NULL COMMENT 'Database yang menjadi sumber data (majority winner)',
  `target_db` varchar(50) DEFAULT NULL COMMENT 'Database yang di-repair (minority/corrupt)',
  `table_name` varchar(100) DEFAULT NULL,
  `record_key` varchar(255) DEFAULT NULL COMMENT 'Primary key atau identifier record (block_hash atau nomor_permohonan)',
  `before_checksum` varchar(64) DEFAULT NULL COMMENT 'Checksum data sebelum recovery',
  `after_checksum` varchar(64) DEFAULT NULL COMMENT 'Checksum data setelah recovery',
  `before_data` text DEFAULT NULL COMMENT 'JSON snapshot data sebelum recovery (untuk rollback)',
  `after_data` text DEFAULT NULL COMMENT 'JSON snapshot data setelah recovery',
  `consensus_result` text DEFAULT NULL COMMENT 'JSON hasil voting: {userdb: hash, admindb: hash, konsensus: hash, majority: hash}',
  `status` enum('success','failed','rolled_back') DEFAULT 'success',
  `error_message` text DEFAULT NULL,
  `performed_by` varchar(100) DEFAULT 'system' COMMENT 'User yang trigger recovery (system/admin username)',
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `last_login_ip` varchar(45) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `role`, `is_active`, `last_login`, `last_login_ip`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@blockchain.local', '$2y$10$wPLhH3GFD4MRDYlCO06iwuM1sY4lUs.Agx6LjTIgQHwwD1F20TZaK', 'Administrator', 'admin', 1, '2025-11-17 08:16:08', '::1', '2025-11-09 06:39:48', '2025-11-17 08:16:08'),
(4, 'admin123', 'gaminggege601@gmail.com', '$2y$10$dBhyvLaBuBIyaidg9uy7zutz4Vj2IAuxvG3VAXBX.rPjKNk4nzw0.', 'ganggaragagoo', 'admin', 1, NULL, NULL, '2025-11-11 10:47:21', '2025-11-11 10:47:21');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `action_type` (`action_type`),
  ADD KEY `block_id` (`block_id`),
  ADD KEY `status` (`status`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `blockchain_backup`
--
ALTER TABLE `blockchain_backup`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nomor_permohonan` (`nomor_permohonan`),
  ADD KEY `tanggal_dokumen` (`tanggal_dokumen`),
  ADD KEY `backup_timestamp` (`backup_timestamp`);

--
-- Indexes for table `ip_whitelist`
--
ALTER TABLE `ip_whitelist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_ip_address` (`ip_address`),
  ADD KEY `is_active` (`is_active`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1099;

--
-- AUTO_INCREMENT for table `blockchain_backup`
--
ALTER TABLE `blockchain_backup`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `ip_whitelist`
--
ALTER TABLE `ip_whitelist`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
