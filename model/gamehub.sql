-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
<<<<<<< HEAD
-- Generation Time: Nov 30, 2025 at 07:58 PM
=======
-- Generation Time: Dec 06, 2025 at 07:06 PM
>>>>>>> user-task
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gamehub`
--

-- --------------------------------------------------------

--
<<<<<<< HEAD
=======
-- Table structure for table `login_log`
--

CREATE TABLE `login_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `success` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
>>>>>>> user-task
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` text NOT NULL,
  `cin` int(8) NOT NULL,
  `tel` int(8) NOT NULL,
  `gender` text NOT NULL,
  `role` text NOT NULL,
<<<<<<< HEAD
  `verified` tinyint(1) DEFAULT 0
=======
  `verified` tinyint(1) DEFAULT 0,
  `verification_requested` tinyint(1) DEFAULT 0,
  `totp_secret` varchar(32) DEFAULT NULL,
  `failed_attempts` int(11) DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `passkey_credential` longtext DEFAULT NULL
>>>>>>> user-task
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

<<<<<<< HEAD
INSERT INTO `user` (`id_user`, `name`, `lastname`, `email`, `password`, `cin`, `tel`, `gender`, `role`, `verified`) VALUES
(1, 'kais', 'guesmi', 'kais.guesmmi@gmail.com', 'kaisfarah', 12345678, 21222324, 'male', 'client', 0),
(2, 'Nour', 'Kahlaoui', 'nourkahlaoui@gmail.com', 'Nawara05', 14540597, 25183228, 'female', 'admin', 0),
(3, 'Taha', 'Chroud', 'tahachroud06@gmail.com', '$2y$10$BKxKI5wufA/b6PY2jI0gPeMcEnvwxKKNfapMnEssLztkRgHdtzi9G', 14540595, 26203351, 'male', 'client', 1);
=======
INSERT INTO `user` (`id_user`, `name`, `lastname`, `email`, `password`, `cin`, `tel`, `gender`, `role`, `verified`, `verification_requested`, `totp_secret`, `failed_attempts`, `locked_until`, `last_login`, `created_at`, `passkey_credential`) VALUES
(2, 'Nour', 'Kahlaoui', 'nourkahlaoui@gmail.com', 'Nawara05', 14540597, 25183228, 'female', 'admin', 0, 0, NULL, 0, NULL, NULL, '2025-12-02 16:25:44', NULL),
(3, 'Taha', 'Chroud', 'tahachroud06@gmail.com', 'Nawara@05', 14540594, 21296203, 'M', 'client', 1, 0, NULL, 0, NULL, NULL, '2025-12-02 16:25:44', NULL),
(44, 'Farah', 'Benasker', 'Farah123@gmail.com', 'Nawara05', 12345678, 55426802, 'male', 'client', 0, 0, NULL, 0, NULL, NULL, '2025-12-02 16:25:44', NULL),
(46, 'Kais', 'Guesmi', 'kais@gmail.com', 'Nawara@05', 12345678, 21296203, 'M', 'client', 1, 1, NULL, 0, NULL, NULL, '2025-12-02 16:25:44', NULL),
(47, 'Ala', 'Gouider', 'ala@gmail.com', 'Nawara@05', 12345678, 21296203, 'F', 'client', 1, 1, NULL, 0, NULL, NULL, '2025-12-02 16:25:44', NULL),
(48, 'Wiem', 'Aouididi', 'wiem123@gmail.com', 'Nawara0505', 12345678, 21296203, 'F', 'client', 1, 1, NULL, 0, NULL, NULL, '2025-12-02 16:25:44', NULL),
(49, 'Taha', 'Chroud', 'taha123@gmail.com', 'Taha@123', 12345678, 21296203, 'M', 'client', 1, 1, '7P2RFB4RZX6FIERA', 0, NULL, NULL, '2025-12-02 16:41:32', NULL),
(50, 'Asma', 'Ouelhezi', 'asma123@gmail.com', 'Nawara05', 14540597, 21296203, 'F', 'client', 1, 1, 'TG4USIO5KNZE266B', 3, '2025-12-06 18:10:39', '2025-12-06 17:54:16', '2025-12-06 15:55:36', NULL);
>>>>>>> user-task

--
-- Indexes for dumped tables
--

--
<<<<<<< HEAD
=======
-- Indexes for table `login_log`
--
ALTER TABLE `login_log`
  ADD PRIMARY KEY (`id`);

--
>>>>>>> user-task
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
<<<<<<< HEAD
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;
=======
-- AUTO_INCREMENT for table `login_log`
--
ALTER TABLE `login_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;
>>>>>>> user-task
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
