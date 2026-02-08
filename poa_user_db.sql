-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 17, 2025 at 04:55 AM
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
-- Database: `poa_user_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `blockchain`
--

CREATE TABLE `blockchain` (
  `id` int(11) NOT NULL,
  `nomor_permohonan` varchar(100) NOT NULL,
  `nomor_dokumen` varchar(100) NOT NULL,
  `tanggal_dokumen` date NOT NULL,
  `tanggal_filing` date NOT NULL,
  `dokumen_base64` longtext NOT NULL,
  `timestamp` datetime NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `block_hash` varchar(255) NOT NULL,
  `previous_hash` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blockchain`
--


--
-- Triggers `blockchain`
--
DELIMITER $$
CREATE TRIGGER `prevent_delete_blockchain` BEFORE DELETE ON `blockchain` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Data pada tabel blockchain (poa_user_db) tidak boleh dihapus';
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blockchain`
--
ALTER TABLE `blockchain`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `block_hash` (`block_hash`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blockchain`
--
ALTER TABLE `blockchain`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
