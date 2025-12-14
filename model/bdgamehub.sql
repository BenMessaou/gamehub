-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : dim. 14 déc. 2025 à 11:22
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `bdgamehub`
--

-- --------------------------------------------------------

--
-- Structure de la table `collab_members`
--

CREATE TABLE `collab_members` (
  `id` int(11) NOT NULL,
  `collab_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `role` varchar(50) DEFAULT 'membre'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `collab_members`
--

INSERT INTO `collab_members` (`id`, `collab_id`, `user_id`, `role`) VALUES
(2, 2, 12, 'owner'),
(3, 3, 155, 'owner'),
(5, 5, 99, 'owner'),
(7, 2, 1, 'membre'),
(9, 5, 1, 'membre'),
(11, 7, 1, 'owner'),
(13, 3, 4, 'moderateur'),
(14, 8, 155, 'owner'),
(15, 3, 1, 'membre'),
(16, 9, 1, 'owner'),
(17, 8, 1, 'membre'),
(18, 10, 1555, 'owner'),
(19, 10, 789, 'membre'),
(21, 12, 1002, 'owner'),
(23, 13, 1003, 'owner'),
(24, 14, 1004, 'owner'),
(25, 15, 1005, 'owner'),
(26, 14, 1, 'membre'),
(28, 15, 1, 'membre'),
(29, 12, 1, 'membre'),
(30, 15, 123654, 'moderateur'),
(32, 14, 5555, 'membre'),
(39, 13, 1, 'membre'),
(40, 12, 1221, 'membre'),
(41, 13, 255, 'membre'),
(42, 13, 2222, 'membre'),
(43, 13, 884664, 'membre'),
(44, 14, 2555, 'membre'),
(45, 14, 25555, 'membre'),
(46, 14, 11, 'membre'),
(47, 21, 1, 'owner'),
(48, 22, 51, 'owner'),
(49, 23, 51, 'owner'),
(50, 21, 51, 'membre'),
(51, 14, 51, 'membre'),
(52, 12, 51, 'membre'),
(53, 22, 555, 'membre');

-- --------------------------------------------------------

--
-- Structure de la table `collab_messages`
--

CREATE TABLE `collab_messages` (
  `id` int(11) NOT NULL,
  `collab_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `audio_path` varchar(500) DEFAULT NULL COMMENT 'Chemin vers le fichier audio du message vocal',
  `audio_duration` int(11) DEFAULT NULL COMMENT 'Durée du message vocal en secondes',
  `date_message` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `collab_messages`
--

INSERT INTO `collab_messages` (`id`, `collab_id`, `user_id`, `message`, `audio_path`, `audio_duration`, `date_message`) VALUES
(1, 2, 1, 'uzildokpldz', NULL, NULL, '2025-11-28 21:54:38'),
(2, 2, 1, 'uzildokpldz', NULL, NULL, '2025-11-28 21:55:16'),
(6, 3, 1, 'bhjnklom', NULL, NULL, '2025-11-29 19:16:21'),
(10, 10, 1, 'hjkldzsfk,fl;dmsù', NULL, NULL, '2025-11-30 16:31:00'),
(11, 12, 1, 'meet at 8pm here is the link of our meet  wwww.exempleof meets .com', NULL, NULL, '2025-11-30 21:07:06'),
(12, 15, 1, 'rtyuiopklyghuijo', NULL, NULL, '2025-12-01 15:04:29'),
(46, 12, 1, 'hi stupid', NULL, NULL, '2025-12-09 00:14:05'),
(47, 12, 1, 'hi stupid you look si ugly\r\n😂😂😂', NULL, NULL, '2025-12-09 00:15:17'),
(48, 12, 1, '📎 Fichiers: c3de0b840761002a6d4db98d415feb6d.jpg', NULL, NULL, '2025-12-09 00:22:25'),
(49, 12, 1, '🤭', NULL, NULL, '2025-12-09 01:00:23'),
(51, 12, 1, '📎 Fichiers: Rapport_PIC16F877_Seance9.pdf\n📎 Fichiers: Rapport_PIC16F877_Seance9.pdf', NULL, NULL, '2025-12-09 09:58:05'),
(53, 12, 1, '😇😇😇😇😇', NULL, NULL, '2025-12-09 10:35:56'),
(54, 13, 1, 'salut ugly prople', NULL, NULL, '2025-12-09 10:49:58'),
(55, 13, 1, 'hi stupid', NULL, NULL, '2025-12-09 10:51:21'),
(59, 13, 1, 'i hate all of you stupid toxic', NULL, NULL, '2025-12-09 12:10:52'),
(60, 13, 1, 'Générer des avatars ?\r\n\r\nCréer un chatbot ?\r\n\r\nFaire une IA de résumé ou traduction ?\r\n\r\nGénérer des images avec Stable Diffusion ?\r\n\r\nDis-moi ton objectif, et je te donne un exemple de code prêt à l’usage (JS, Python, PHP, etc.', NULL, NULL, '2025-12-09 12:11:48'),
(61, 13, 1, 'salut', NULL, NULL, '2025-12-09 12:21:08'),
(62, 13, 1, '📎 Fichiers: 8e77709e232a2dd225b952ededbd32e6.jpg\n📎 Fichiers: 8e77709e232a2dd225b952ededbd32e6.jpg', NULL, NULL, '2025-12-09 12:21:28'),
(63, 12, 1, 'Personne ne veut bosser avec toi, tu le sais', NULL, NULL, '2025-12-09 14:36:01'),
(64, 12, 1, 'mort', NULL, NULL, '2025-12-09 14:41:36'),
(65, 12, 1, '', 'uploads/voices/voice_12_1_1765295110_693844069fb15.webm', 5, '2025-12-09 16:45:10'),
(66, 12, 1, 'i hate u', NULL, NULL, '2025-12-09 16:46:38'),
(67, 21, 51, 'ygudzgoiz', NULL, NULL, '2025-12-13 20:34:09');

-- --------------------------------------------------------

--
-- Structure de la table `collab_project`
--

CREATE TABLE `collab_project` (
  `id` int(11) NOT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `titre` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `date_creation` date DEFAULT NULL,
  `statut` enum('ouvert','en_cours','ferme') DEFAULT 'ouvert',
  `max_membres` int(11) DEFAULT 10,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `collab_project`
--

INSERT INTO `collab_project` (`id`, `owner_id`, `titre`, `description`, `date_creation`, `statut`, `max_membres`, `image`) VALUES
(2, 12, 'hjnkelrfd', 'djek,rek,dmecfk,lemcv,klfcd', '2025-11-28', 'ferme', 15, 'zedrftgedzs'),
(3, 155, 'hjnkelrfd', 'zhbjdnk,lmcfbhjnkd,lzm;;szjkvfl,dllclvn,elvvm;c;msdcdc', '2025-11-28', 'ferme', 5, '/gamehubprjt/view/frontoffice/backoffice/uploads/collab_692b7db77d3fc4.44880066.jpg'),
(5, 99, 'loll', 'c888888888888883dsx', '2025-11-28', 'en_cours', 12, '8888885'),
(7, 1, 'pbg2222', '200000000000000000000000000', '2025-11-28', 'en_cours', 1, 'zedrftgedzs'),
(8, 155, 'hjnkelrfd', 'zhbjdnk,lmcfbhjnkd,lzm;;szjkvfl,dllclvn,elvvm;c;msdcdc', '2025-11-28', 'ouvert', 5, '/gamehubprjt/view/frontoffice/backoffice/uploads/collab_692c245c95d3f5.84106958.jpg'),
(9, 1, 'cdc', '0000000000000000000', '2025-11-30', 'ferme', 5, '/gamehubprjt/view/frontoffice/backoffice/uploads/collab_692b7f08123331.70313393.jpg'),
(10, 1555, 'test', 'in this collab', '2025-11-30', 'en_cours', 2, '/gamehubprjt/view/frontoffice/backoffice/uploads/collab_692c61156666a3.77662587.jpg'),
(12, 1002, 'Neon Protocol – Cyberpunk Action RPG', 'Develop an action-packed cyberpunk RPG set in a neon-lit megacity controlled by corporations and rogue AI. Players hack systems, upgrade cybernetic implants, and uncover conspiracies that threaten humanity.\r\nWe need futuristic UI designers, pixel artists, combat designers, and coders familiar with fast-paced gameplay', '2025-11-30', 'ouvert', 4, '/gamehubprjt/view/frontoffice/backoffice/uploads/collab_692c9c40ed4962.58075756.png'),
(13, 1003, 'Dragonfall Chronicles – Open-World Adventure', 'Build a massive open-world adventure where players bond with dragons, explore ancient kingdoms, and fight in large-scale battles. The game blends exploration, crafting, and story-driven quests.\r\nWe are looking for worldbuilders, 3D/2D artists, animation people, and quest writers.', '2025-11-30', 'ouvert', 5, '/gamehubprjt/view/frontoffice/backoffice/uploads/collab_692c9e2140b2a3.28683590.png'),
(14, 1004, 'Silent Shadows – Horror Adventure Game', 'Create a psychological horror experience set in an abandoned research facility. Players uncover disturbing secrets while avoiding terrifying entities that react to sound and light.\r\nLooking for horror artists, sound designers, and narrative designers.', '2025-11-30', 'ouvert', 8, '/gamehubprjt/view/frontoffice/backoffice/uploads/collab_692c9f559d6f61.54394710.png'),
(15, 1005, 'Ocean of Spirits – Underwater Fantasy RPG', 'A vibrant underwater RPG where players explore coral kingdoms, battle aquatic monsters, and uncover an ancient oceanic prophecy.\r\nLooking for fantasy artists who can create glowing underwater environments.\r\nDive into a magical world beneath the waves.', '2025-11-30', 'ouvert', 12, '/gamehubprjt/view/frontoffice/backoffice/uploads/collab_692ca11d5dc0e8.64701450.png'),
(21, 1, 'bkhb', 'guyijkpk^l^kjk', '2025-12-13', 'ouvert', 2, NULL),
(22, 51, 'hjnkelrfd', 'ghjklmùhjkl', '2025-12-13', 'ouvert', 5, NULL),
(23, 51, 'hujkilo', 'gfhjklmjhklm', '2025-12-13', 'ouvert', 1, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `collab_skills_required`
--

CREATE TABLE `collab_skills_required` (
  `id` int(11) NOT NULL,
  `collab_id` int(11) DEFAULT NULL,
  `skill` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `collab_tasks`
--

CREATE TABLE `collab_tasks` (
  `id` int(11) NOT NULL,
  `collab_id` int(11) DEFAULT NULL,
  `task` text DEFAULT NULL,
  `done` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `collab_tasks`
--

INSERT INTO `collab_tasks` (`id`, `collab_id`, `task`, `done`) VALUES
(1, 2, 'ziudjok', 1),
(2, 8, 'create the logo of the game', 0),
(3, 12, 'the logo of our game', 1),
(4, 12, 'the game design', 0),
(5, 15, 'ziudjok', 1);

-- --------------------------------------------------------

--
-- Structure de la table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `is_online` tinyint(1) DEFAULT 0,
  `capacity` int(11) DEFAULT 0,
  `reserved_count` int(11) DEFAULT 0,
  `banner` varchar(255) DEFAULT NULL,
  `status` enum('active','canceled') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `events`
--

INSERT INTO `events` (`id`, `title`, `description`, `start_date`, `end_date`, `location`, `is_online`, `capacity`, `reserved_count`, `banner`, `status`, `created_at`, `updated_at`) VALUES
(2, 'jkolp', '00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000', '2025-11-24 01:18:00', '2025-11-30 01:18:00', 'tunis', 0, 21, 0, 'freeds', 'active', '2025-11-24 01:18:29', '2025-11-24 01:18:29');

-- --------------------------------------------------------

--
-- Structure de la table `login_log`
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
-- Structure de la table `message_moderation_logs`
--

CREATE TABLE `message_moderation_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `collab_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `moderation_result` text NOT NULL COMMENT 'JSON avec le résultat de la modération',
  `scores` text DEFAULT NULL COMMENT 'JSON avec les scores IA',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `message_moderation_logs`
--

INSERT INTO `message_moderation_logs` (`id`, `user_id`, `collab_id`, `message`, `moderation_result`, `scores`, `created_at`) VALUES
(1, 1, 19, '🤭🤭🤭', '{\"approved\":true,\"blocked\":false,\"moderated\":false,\"reason\":\"\",\"level\":0,\"scores\":{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}}', '{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}', '2025-12-06 14:55:02'),
(2, 1, 19, 'fuck', '{\"approved\":false,\"blocked\":true,\"moderated\":false,\"reason\":\"Message contient des mots interdits : fuck\",\"level\":1,\"scores\":[]}', '[]', '2025-12-06 16:54:38'),
(3, 1, 11, 'salut', '{\"approved\":true,\"blocked\":false,\"moderated\":false,\"reason\":\"\",\"level\":0,\"scores\":{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}}', '{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}', '2025-12-06 19:48:39'),
(4, 1, 11, 'negga', '{\"approved\":true,\"blocked\":false,\"moderated\":false,\"reason\":\"\",\"level\":0,\"scores\":{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}}', '{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}', '2025-12-06 19:49:48'),
(5, 1, 11, 'fuck', '{\"approved\":false,\"blocked\":true,\"moderated\":false,\"reason\":\"Message contient des mots interdits : fuck\",\"level\":1,\"scores\":[]}', '[]', '2025-12-06 19:50:09'),
(6, 1, 11, 'tu es moche', '{\"approved\":true,\"blocked\":false,\"moderated\":false,\"reason\":\"\",\"level\":0,\"scores\":{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}}', '{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}', '2025-12-06 19:50:38'),
(7, 1, 11, 'son of bitch', '{\"approved\":false,\"blocked\":true,\"moderated\":false,\"reason\":\"Message contient des mots interdits : bitch\",\"level\":1,\"scores\":[]}', '[]', '2025-12-06 19:51:03'),
(8, 1, 11, 'Bonjour ! Je suis votre assistant IA. Comment puis-je vous aider aujourd\'hui ?\r\n19:57', '{\"approved\":true,\"blocked\":false,\"moderated\":false,\"reason\":\"\",\"level\":0,\"scores\":{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}}', '{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}', '2025-12-06 19:57:42'),
(9, 1, 11, ',^pl$^m', '{\"approved\":true,\"blocked\":false,\"moderated\":false,\"reason\":\"\",\"level\":0,\"scores\":{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}}', '{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}', '2025-12-08 17:37:09'),
(10, 1, 19, 'salut monsieur tu es moche et pauvre', '{\"approved\":true,\"blocked\":false,\"moderated\":false,\"reason\":\"\",\"level\":0,\"scores\":{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}}', '{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}', '2025-12-08 18:04:29'),
(11, 1, 19, 'i hate you', '{\"approved\":true,\"blocked\":false,\"moderated\":false,\"reason\":\"\",\"level\":0,\"scores\":{\"hate\":0.52,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}}', '{\"hate\":0.52,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}', '2025-12-08 18:04:53'),
(12, 1, 20, 'zeioez', '{\"approved\":true,\"blocked\":false,\"moderated\":false,\"reason\":\"\",\"level\":0,\"scores\":{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}}', '{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}', '2025-12-08 18:53:33'),
(13, 1, 20, 'no fuck u', '{\"approved\":false,\"blocked\":true,\"moderated\":false,\"reason\":\"Message contient des mots interdits : fuck\",\"level\":1,\"scores\":[]}', '[]', '2025-12-08 19:19:47'),
(14, 1, 20, 'i can talk with stupid people', '{\"approved\":true,\"blocked\":false,\"moderated\":false,\"reason\":\"\",\"level\":0,\"scores\":{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0.5700000000000001,\"dangerous\":0}}', '{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0.5700000000000001,\"dangerous\":0}', '2025-12-08 19:20:10'),
(15, 1, 19, 'fuck', '{\"approved\":false,\"blocked\":true,\"moderated\":false,\"reason\":\"Message contient des mots interdits : fuck\",\"level\":1,\"scores\":[]}', '[]', '2025-12-08 20:07:41'),
(16, 1, 20, 'hi', '{\"approved\":true,\"blocked\":false,\"moderated\":false,\"reason\":\"\",\"level\":0,\"scores\":{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}}', '{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}', '2025-12-08 23:29:01'),
(17, 1, 12, 'hi stupid', '{\"approved\":true,\"blocked\":false,\"moderated\":false,\"reason\":\"\",\"level\":0,\"scores\":{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0.48,\"dangerous\":0}}', '{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0.48,\"dangerous\":0}', '2025-12-09 00:14:05'),
(18, 1, 12, 'hi stupid you look si ugly\r\n😂😂😂', '{\"approved\":true,\"blocked\":false,\"moderated\":false,\"reason\":\"\",\"level\":0,\"scores\":{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0.53,\"dangerous\":0}}', '{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0.53,\"dangerous\":0}', '2025-12-09 00:15:17'),
(19, 1, 12, '📎 Fichiers: c3de0b840761002a6d4db98d415feb6d.jpg', '{\"approved\":true,\"blocked\":false,\"moderated\":false,\"reason\":\"\",\"level\":0,\"scores\":{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}}', '{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}', '2025-12-09 00:22:25'),
(20, 1, 12, '🤭', '{\"approved\":true,\"blocked\":false,\"moderated\":false,\"reason\":\"\",\"level\":0,\"scores\":{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}}', '{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}', '2025-12-09 01:00:23'),
(21, 1, 20, '📎 Fichiers: Rapport_PIC16F877_Seance9.pdf\n📎 Fichiers: Rapport_PIC16F877_Seance9.pdf', '{\"approved\":true,\"blocked\":false,\"moderated\":false,\"reason\":\"\",\"level\":0,\"scores\":{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}}', '{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}', '2025-12-09 01:02:14'),
(22, 1, 12, '📎 Fichiers: Rapport_PIC16F877_Seance9.pdf\n📎 Fichiers: Rapport_PIC16F877_Seance9.pdf', '{\"approved\":true,\"blocked\":false,\"moderated\":false,\"reason\":\"\",\"level\":0,\"scores\":{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}}', '{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}', '2025-12-09 09:58:05'),
(23, 1, 12, '😇😇😇😇😇', '{\"approved\":true,\"blocked\":false,\"moderated\":false,\"reason\":\"\",\"level\":0,\"scores\":{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}}', '{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}', '2025-12-09 10:35:56'),
(24, 1, 13, 'salut ugly prople', '{\"approved\":true,\"blocked\":false,\"moderated\":false,\"reason\":\"\",\"level\":0,\"scores\":{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}}', '{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}', '2025-12-09 10:49:58'),
(25, 1, 13, 'hi stupid', '{\"approved\":true,\"blocked\":false,\"moderated\":false,\"reason\":\"\",\"level\":0,\"scores\":{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0.47,\"dangerous\":0}}', '{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0.47,\"dangerous\":0}', '2025-12-09 10:51:21'),
(26, 1, 12, 'hi stupid ugly ass', '{\"approved\":true,\"blocked\":false,\"moderated\":false,\"reason\":\"\",\"level\":0,\"scores\":{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0.5,\"dangerous\":0}}', '{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0.5,\"dangerous\":0}', '2025-12-09 11:05:08'),
(27, 1, 12, 'dick', '{\"approved\":true,\"blocked\":false,\"moderated\":false,\"reason\":\"\",\"level\":0,\"scores\":{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}}', '{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}', '2025-12-09 11:05:38'),
(28, 1, 20, 'hii', '{\"approved\":true,\"blocked\":false,\"moderated\":false,\"reason\":\"\",\"level\":0,\"scores\":{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}}', '{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}', '2025-12-09 12:03:14'),
(29, 1, 13, 'i hate all of you stupid toxic', '{\"approved\":true,\"blocked\":false,\"moderated\":false,\"reason\":\"\",\"level\":0,\"scores\":{\"hate\":0.44,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0.53,\"dangerous\":0}}', '{\"hate\":0.44,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0.53,\"dangerous\":0}', '2025-12-09 12:10:52'),
(30, 1, 13, 'Générer des avatars ?\r\n\r\nCréer un chatbot ?\r\n\r\nFaire une IA de résumé ou traduction ?\r\n\r\nGénérer des images avec Stable Diffusion ?\r\n\r\nDis-moi ton objectif, et je te donne un exemple de code prêt à l’usage (JS, Python, PHP, etc.', '{\"approved\":true,\"blocked\":false,\"moderated\":false,\"reason\":\"\",\"level\":0,\"scores\":{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}}', '{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}', '2025-12-09 12:11:48'),
(31, 1, 13, 'salut', '{\"approved\":true,\"blocked\":false,\"moderated\":false,\"reason\":\"\",\"level\":0,\"scores\":{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}}', '{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}', '2025-12-09 12:21:08'),
(32, 1, 13, '📎 Fichiers: 8e77709e232a2dd225b952ededbd32e6.jpg\n📎 Fichiers: 8e77709e232a2dd225b952ededbd32e6.jpg', '{\"approved\":true,\"blocked\":false,\"moderated\":false,\"reason\":\"\",\"level\":0,\"scores\":{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}}', '{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}', '2025-12-09 12:21:28'),
(33, 1, 12, 'Personne ne veut bosser avec toi, tu le sais', '{\"approved\":true,\"blocked\":false,\"moderated\":false,\"reason\":\"\",\"level\":0,\"scores\":{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}}', '{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}', '2025-12-09 14:36:01'),
(34, 1, 12, 'hey sexy', '{\"approved\":false,\"blocked\":true,\"moderated\":false,\"reason\":\"Message contient des mots interdits : sexy, sexy (contournement d\\u00e9tect\\u00e9)\",\"level\":1,\"scores\":[]}', '[]', '2025-12-09 14:39:32'),
(35, 1, 12, 'mort', '{\"approved\":true,\"blocked\":false,\"moderated\":false,\"reason\":\"\",\"level\":0,\"scores\":{\"hate\":0.30000000000000004,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}}', '{\"hate\":0.30000000000000004,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}', '2025-12-09 14:41:36'),
(36, 1, 12, 'fuck', '{\"approved\":false,\"blocked\":true,\"moderated\":false,\"reason\":\"Message contient des mots interdits : fuck, fuck (contournement d\\u00e9tect\\u00e9)\",\"level\":1,\"scores\":[]}', '[]', '2025-12-09 16:46:20'),
(37, 1, 12, 'i hate u', '{\"approved\":true,\"blocked\":false,\"moderated\":false,\"reason\":\"\",\"level\":0,\"scores\":{\"hate\":0.30000000000000004,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}}', '{\"hate\":0.30000000000000004,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}', '2025-12-09 16:46:38'),
(38, 51, 21, 'ygudzgoiz', '{\"approved\":true,\"blocked\":false,\"moderated\":false,\"reason\":\"\",\"level\":0,\"scores\":{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}}', '{\"hate\":0,\"violence\":0,\"harassment\":0,\"sexual\":0,\"discrimination\":0,\"toxicity\":0,\"dangerous\":0}', '2025-12-13 20:34:09');

-- --------------------------------------------------------

--
-- Structure de la table `moderated_messages`
--

CREATE TABLE `moderated_messages` (
  `id` int(11) NOT NULL,
  `original_message_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `collab_id` int(11) NOT NULL,
  `original_message` text NOT NULL,
  `moderated_message` text DEFAULT NULL,
  `moderation_reason` text NOT NULL,
  `status` enum('pending','approved','rejected','edited') DEFAULT 'pending',
  `moderator_id` int(11) DEFAULT NULL COMMENT 'ID du modérateur qui a approuvé/rejeté',
  `created_at` datetime DEFAULT current_timestamp(),
  `reviewed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `developpeur` varchar(255) NOT NULL,
  `date_creation` date NOT NULL,
  `categorie` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `trailer` varchar(255) DEFAULT NULL,
  `developpeur_id` int(11) DEFAULT NULL,
  `age_recommande` int(11) DEFAULT NULL,
  `lieu` varchar(255) DEFAULT NULL,
  `lien_telechargement` varchar(255) DEFAULT NULL,
  `plateformes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`plateformes`)),
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `screenshots` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`screenshots`)),
  `statut` varchar(20) DEFAULT 'en_attente',
  `date_soumission` datetime DEFAULT current_timestamp(),
  `date_publication` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `projects`
--

INSERT INTO `projects` (`id`, `nom`, `developpeur`, `date_creation`, `categorie`, `description`, `image`, `trailer`, `developpeur_id`, `age_recommande`, `lieu`, `lien_telechargement`, `plateformes`, `tags`, `screenshots`, `statut`, `date_soumission`, `date_publication`, `created_at`) VALUES
(8, 'ALTO’S ODYSSEY', 'Snowflare Team', '2022-04-18', 'Aventure', 'Alto’s Odyssey is a minimalist, endless sandboarding journey across mystical deserts, ancient ruins, and breathtaking landscapes. The player glides fluidly down slopes, performs tricks, and avoids obstacles while exploring dynamic weather and calm environments. Designed with soothing visuals and music, the game appeals to casual players seeking a relaxing but skillful adventure.', 'https://i.pinimg.com/736x/36/b0/3e/36b03ebd32d0c8cb4605c2aeb6e4ae8d.jpg', 'https://www.youtube.com/watch?v=xxxxxxx', 1, 16, 'canda', 'https://yourgame.com/altosodyssey', '[]', '[\"coop \",\" pixel art\"]', '[\"..\\/backoffice\\/uploads\\/screenshot_691c6273e6db80.99637198.jpg\"]', 'publie', '2025-11-18 13:11:31', '2025-11-21 22:37:08', '2025-11-18 12:11:31'),
(9, 'NEO CITY RUNNER', 'BitRush Collective', '2024-06-08', 'Action', 'Neo City Runner is a fast-paced sci-fi platformer set in a vibrant futuristic metropolis. Players control a small astronaut navigating neon rooftops, hacking terminals, and making daring jumps through vertical levels. The world is packed with secrets, animated billboards, and lively cyberpunk characters. Its bright pixel-art aesthetic and energetic music appeal to fans of retro-modern arcade action.', 'https://i.pinimg.com/1200x/bf/7b/16/bf7b16295bda52b24ca107ae7be4b848.jpg', 'https://vimeo.com/231704625', 1, 16, 'japan', 'https://yourgame.com/neocityrunner', '[]', '[\"Sci-Fi \",\" pixel-Art\"]', '[\"..\\/backoffice\\/uploads\\/screenshot_691c641046b1e9.09159495.jpg\"]', 'publie', '2025-11-18 13:18:24', '2025-11-18 13:19:14', '2025-11-18 12:18:24'),
(12, 'Eternal Quest', 'aryem Aouadi / Atlas Pixel Studio', '2025-06-15', 'RPG', 'Eternal Quest est un jeu de rôle et d’aventure en vue à la troisième personne, se déroulant dans un univers médiéval-fantastique riche et immersif. Le joueur incarne un héros chargé de restaurer l’équilibre d’un monde menacé par une ancienne malédiction. Le gameplay repose sur l’exploration de vastes environnements, des combats dynamiques en temps réel, la gestion de compétences et la résolution d’énigmes. Le système de progression permet de personnaliser le personnage selon différents styles de jeu. Eternal Quest s’adresse aux amateurs de RPG narratifs et d’aventure solo.', '../backoffice/uploads/project_693c4ca142d818.94904754.png', 'https://youtu.be/Wty2OTvudWc', 1, 12, 'tunis,Tunisie', 'https://www.monsitejeux.com/download/eternal-quest', '[\"iooi\"]', '[\"adventure \"]', '[\"..\\/backoffice\\/uploads\\/screenshot_693c4ca143e7d5.72736563.png\"]', 'publie', '2025-12-12 18:10:57', '2025-12-12 18:12:04', '2025-12-12 17:10:57');

-- --------------------------------------------------------

--
-- Structure de la table `user`
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
  `verified` tinyint(1) DEFAULT 0,
  `verification_requested` tinyint(1) DEFAULT 0,
  `totp_secret` varchar(32) DEFAULT NULL,
  `failed_attempts` int(11) DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `passkey_credential` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id_user`, `name`, `lastname`, `email`, `password`, `cin`, `tel`, `gender`, `role`, `verified`, `verification_requested`, `totp_secret`, `failed_attempts`, `locked_until`, `last_login`, `created_at`, `passkey_credential`) VALUES
(2, 'Nour', 'Kahlaoui', 'nourkahlaoui@gmail.com', 'Nawara05', 14540597, 25183228, 'female', 'admin', 0, 0, NULL, 0, NULL, NULL, '2025-12-02 16:25:44', NULL),
(3, 'Taha', 'Chroud', 'tahachroud06@gmail.com', 'Nawara@05', 14540594, 21296203, 'M', 'client', 1, 0, NULL, 0, NULL, NULL, '2025-12-02 16:25:44', NULL),
(44, 'Farah', 'Benasker', 'Farah123@gmail.com', 'Nawara05', 12345678, 55426802, 'male', 'client', 0, 0, NULL, 0, NULL, NULL, '2025-12-02 16:25:44', NULL),
(46, 'Kais', 'Guesmi', 'kais@gmail.com', 'Nawara@05', 12345678, 21296203, 'M', 'client', 1, 1, NULL, 0, NULL, NULL, '2025-12-02 16:25:44', NULL),
(47, 'Ala', 'Gouider', 'ala@gmail.com', 'Nawara@05', 12345678, 21296203, 'F', 'client', 1, 1, NULL, 0, NULL, NULL, '2025-12-02 16:25:44', NULL),
(48, 'Wiem', 'Aouididi', 'wiem123@gmail.com', 'Nawara0505', 12345678, 21296203, 'F', 'client', 1, 1, NULL, 0, NULL, NULL, '2025-12-02 16:25:44', NULL),
(49, 'Taha', 'Chroud', 'taha123@gmail.com', 'Taha@123', 12345678, 21296203, 'M', 'client', 1, 1, '7P2RFB4RZX6FIERA', 0, NULL, NULL, '2025-12-02 16:41:32', NULL),
(50, 'Asma', 'Ouelhezi', 'asma123@gmail.com', 'Nawara05', 14540597, 21296203, 'F', 'client', 1, 1, 'TG4USIO5KNZE266B', 3, '2025-12-06 18:10:39', '2025-12-06 17:54:16', '2025-12-06 15:55:36', NULL),
(51, 'ariem aouadi', '0000', 'awediariem03@gmail.com', 'tPsMd5X:V_C7_6p', 241555878, 53098772, 'F', 'client', 0, 1, NULL, 0, NULL, NULL, '2025-12-13 20:02:02', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `user_avatars`
--

CREATE TABLE `user_avatars` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `avatar_name` varchar(255) NOT NULL DEFAULT 'Mon Avatar',
  `avatar_data` text NOT NULL COMMENT 'JSON data containing avatar configuration',
  `avatar_image` varchar(500) DEFAULT NULL COMMENT 'Path to generated avatar image',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user_avatars`
--

INSERT INTO `user_avatars` (`id`, `user_id`, `avatar_name`, `avatar_data`, `avatar_image`, `created_at`, `updated_at`) VALUES
(1, 1, 'Mon Qbit', '{\"base\":{\"skin_tone\":\"tan\",\"skin_color\":\"#74472d\"},\"hair\":{\"style\":\"short-rounded\",\"color\":\"#654321\"},\"face\":{\"expression\":\"surprised\",\"eyes\":{\"style\":\"surprised\",\"color\":\"#000000\"},\"mouth\":{\"style\":\"surprised\",\"color\":\"#ff6b6b\"},\"eyebrows\":{\"style\":\"soft\",\"color\":\"#2c2c2c\"},\"cheeks\":true},\"body\":{\"torso\":{\"color\":\"#4a90e2\",\"shape\":\"rounded\",\"width\":90,\"height\":100},\"legs\":{\"color\":\"#2c5aa0\",\"width\":35,\"height\":80,\"spacing\":20,\"shoes\":{\"color\":\"#1a1a1a\",\"style\":\"default\"}},\"arms\":{\"position\":\"rest\",\"left_color\":\"#74472d\",\"right_color\":\"#74472d\",\"sleeve_color\":\"#4a90e2\"}},\"accessories\":{\"head\":[\"hat\"],\"face\":[\"glasses\"],\"body\":[]},\"animation\":{\"idle\":true,\"type\":\"breathe\",\"speed\":\"normal\"},\"shirt\":\"shirt1\",\"pants\":\"pants1\",\"metadata\":{\"generated_from_selfie\":true,\"analysis_confidence\":0.8,\"detected_colors\":[\"#001400\",\"#646450\",\"#000000\",\"#646464\",\"#505050\"]}}', NULL, '2025-12-06 13:56:50', '2025-12-09 20:41:03'),
(2, 51, 'Mon Qbit', '{\"base\":{\"skin_tone\":\"light\",\"skin_color\":\"#ffdbac\"},\"hair\":{\"style\":\"short-rounded\",\"color\":\"#333333\"},\"face\":{\"expression\":\"cool\",\"eyes\":{\"style\":\"happy\",\"color\":\"#000000\"},\"mouth\":{\"style\":\"smile\",\"color\":\"#ff6b6b\"},\"eyebrows\":{\"style\":\"soft\",\"color\":\"#2c2c2c\"},\"cheeks\":true},\"body\":{\"torso\":{\"color\":\"#4a90e2\",\"shape\":\"rounded\",\"width\":90,\"height\":100},\"legs\":{\"color\":\"#2c5aa0\",\"width\":35,\"height\":80,\"spacing\":20,\"shoes\":{\"color\":\"#1a1a1a\",\"style\":\"default\"}},\"arms\":{\"position\":\"rest\",\"left_color\":\"#ffdbac\",\"right_color\":\"#ffdbac\",\"sleeve_color\":\"#4a90e2\"}},\"accessories\":{\"head\":[],\"face\":[\"glasses\"],\"body\":[]},\"animation\":{\"idle\":true,\"type\":\"breathe\",\"speed\":\"normal\"},\"shirt\":\"shirt1\",\"pants\":\"pants1\"}', NULL, '2025-12-13 20:23:01', '2025-12-13 20:55:51');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `collab_members`
--
ALTER TABLE `collab_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `collab_id` (`collab_id`);

--
-- Index pour la table `collab_messages`
--
ALTER TABLE `collab_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `collab_id` (`collab_id`),
  ADD KEY `idx_audio` (`audio_path`);

--
-- Index pour la table `collab_project`
--
ALTER TABLE `collab_project`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `collab_skills_required`
--
ALTER TABLE `collab_skills_required`
  ADD PRIMARY KEY (`id`),
  ADD KEY `collab_id` (`collab_id`);

--
-- Index pour la table `collab_tasks`
--
ALTER TABLE `collab_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `collab_id` (`collab_id`);

--
-- Index pour la table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `login_log`
--
ALTER TABLE `login_log`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `message_moderation_logs`
--
ALTER TABLE `message_moderation_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_collab` (`collab_id`),
  ADD KEY `idx_created` (`created_at`);

--
-- Index pour la table `moderated_messages`
--
ALTER TABLE `moderated_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_collab` (`collab_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created` (`created_at`);

--
-- Index pour la table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- Index pour la table `user_avatars`
--
ALTER TABLE `user_avatars`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_created` (`created_at`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `collab_members`
--
ALTER TABLE `collab_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT pour la table `collab_messages`
--
ALTER TABLE `collab_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT pour la table `collab_project`
--
ALTER TABLE `collab_project`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT pour la table `collab_skills_required`
--
ALTER TABLE `collab_skills_required`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `collab_tasks`
--
ALTER TABLE `collab_tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `login_log`
--
ALTER TABLE `login_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `message_moderation_logs`
--
ALTER TABLE `message_moderation_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT pour la table `moderated_messages`
--
ALTER TABLE `moderated_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT pour la table `user_avatars`
--
ALTER TABLE `user_avatars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `collab_members`
--
ALTER TABLE `collab_members`
  ADD CONSTRAINT `collab_members_ibfk_1` FOREIGN KEY (`collab_id`) REFERENCES `collab_project` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `collab_messages`
--
ALTER TABLE `collab_messages`
  ADD CONSTRAINT `collab_messages_ibfk_1` FOREIGN KEY (`collab_id`) REFERENCES `collab_project` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `collab_skills_required`
--
ALTER TABLE `collab_skills_required`
  ADD CONSTRAINT `collab_skills_required_ibfk_1` FOREIGN KEY (`collab_id`) REFERENCES `collab_project` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `collab_tasks`
--
ALTER TABLE `collab_tasks`
  ADD CONSTRAINT `collab_tasks_ibfk_1` FOREIGN KEY (`collab_id`) REFERENCES `collab_project` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
