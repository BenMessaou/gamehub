-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 24 nov. 2025 à 23:12
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gamehub`
--

-- --------------------------------------------------------

--
-- Structure de la table `articles`
--

CREATE TABLE `articles` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `articles`
--

INSERT INTO `articles` (`id`, `title`, `content`, `image`, `created_at`, `updated_at`, `user_id`) VALUES
(1, 'Le Futur de GameHub', 'Nous lançons bientôt de nouvelles fonctionnalités...', NULL, '2025-11-16 03:11:58', NULL, 1),
(4, 'Gaming 12', 'Key Takeaways\r\nThe gaming world is more diverse than ever: Players of all ages, backgrounds, and skill levels contribute to a rich and evolving gaming culture. This diversity enhances the gaming experience for everyone.\r\nGaming offers a range of emotional and social benefits: From stress relief and improved mood to stronger social connections and personal growth, gaming can positively impact well-being.\r\nA balanced perspective on gaming is essential: Understanding both the challenges and the opportunities within the gaming world allows for more informed discussions and a healthier relationship with gaming.\r\nWhat is the Gaming Landscape?\r\nGaming has evolved. It’s no longer a niche hobby, but a mainstream form of entertainment and connection. This evolution has drastically changed who plays and how we interact within the gaming world. Let’s explore the current state of play.\r\n\r\n', NULL, '2025-11-17 21:28:14', NULL, 2),
(7, 'Ubisoft game 7', 'Depuis plusieurs années, Ubisoft ne cache plus ses ambitions en matière d’intelligence artificielle. Bien avant que l’IA générative ne devienne un mot-clé incontournable, l’éditeur français explorait déjà des technologies visant à rendre ses mondes plus crédibles, plus dynamiques, plus vivants, notamment dans Assassin’s Creed où l’immersion est primordiale. De la génération procédurale de décors à l’intelligence tactique des ennemis, Ubisoft a toujours voulu dépasser le simple script pour créer des univers réactifs. Force est de constater que l’entreprise française n’a de cesse de vouloir surfer sur les tendances de la tech. Parfois ça passe, parfois ça casse (coucou les NFT).', NULL, '2025-11-24 15:47:32', NULL, 1),
(8, 'gaming', 'Depuis plusieurs années, Ubisoft ne cache plus ses ambitions en matière d’intelligence artificielle. Bien avant que l’IA générative ne devienne un mot-clé incontournable, l’éditeur français explorait déjà des technologies visant à rendre ses mondes plus crédibles, plus dynamiques, plus vivants, notamment dans Assassin’s Creed où l’immersion est primordiale. De la génération procédurale de décors à l’intelligence tactique des ennemis, Ubisoft a toujours voulu dépasser le simple script pour créer des univers réactifs. Force est de constater que l’entreprise française n’a de cesse de vouloir surfer sur les tendances de la tech. Parfois ça passe, parfois ça casse (coucou les NFT).', NULL, '2025-11-24 22:51:46', NULL, 1);

-- --------------------------------------------------------

--
-- Structure de la table `commentaires`
--

CREATE TABLE `commentaires` (
  `id` int(11) NOT NULL,
  `content` text NOT NULL,
  `article_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `commentaires`
--

INSERT INTO `commentaires` (`id`, `content`, `article_id`, `user_id`, `created_at`) VALUES
(2, 'magnifique', 4, 1, '2025-11-24 22:24:57'),
(3, 'merci beacoup', 7, 1, '2025-11-24 22:28:17'),
(4, 'f', 1, 1, '2025-11-24 23:11:03');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` enum('admin','client') NOT NULL DEFAULT 'client'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id_user`, `nom`, `email`, `mot_de_passe`, `role`) VALUES
(1, 'Admin GameHub', 'admin@gamehub.com', 'admin_hash', 'admin'),
(2, 'Super Gamer', 'gamer@mail.com', 'gamer_hash', 'client');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `commentaires`
--
ALTER TABLE `commentaires`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_comment_article` (`article_id`),
  ADD KEY `fk_comment_user` (`user_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `commentaires`
--
ALTER TABLE `commentaires`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `articles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Contraintes pour la table `commentaires`
--
ALTER TABLE `commentaires`
  ADD CONSTRAINT `fk_comment_article` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_comment_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_user`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
