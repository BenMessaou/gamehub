-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : dim. 07 déc. 2025 à 01:16
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
  `image_path` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `articles`
--

INSERT INTO `articles` (`id`, `title`, `content`, `image_path`, `image`, `created_at`, `updated_at`, `user_id`) VALUES
(25, 'super mario', 'Pour rappel, Replaced nous plonge dans une r&eacute;alit&eacute; altern&eacute;e, au sein des 80 dans une Am&eacute;rique marqu&eacute;e par une catastrophe nucl&eacute;aire. Vous incarnez ici non pas un humain, mais une IA coinc&eacute;e &agrave; l&rsquo;int&eacute;rieur d&rsquo;un corps, qui doit d&eacute;couvrir les secrets de la soci&eacute;t&eacute; Phoenix, qui est au c&oelig;ur de plusieurs magouilles et injustices.\r\n\r\n', 'public/uploads/6934378d1338b.jpg', NULL, '2025-12-06 15:02:53', NULL, 1),
(26, 'Un séisme dans le monde du divertissement', 'Un s&eacute;isme dans le monde du divertissement\r\nNetflix et Warner Bros sont d&rsquo;abord parvenus &agrave; un accord pour entrer dans une phase de n&eacute;gociation exclusive, qui excluait les autres participants. Selon The Wrap, Netflix aurait fait la plus grosse offre avec 30 dollars par action, tout en s&rsquo;alignant sur Paramount avec une indemnit&eacute; de rupture de 5 milliards de dollars en cas d&rsquo;&eacute;chec. Cela s&rsquo;est confirm&eacute; quelques heures plus tard avec l&rsquo;officialisation du rachat, &agrave; hauteur de 87,2 milliards de dollars.\r\n\r\nApr&egrave;s plusieurs ench&egrave;res, Netflix a donc eu les faveurs de David Zaslav (PDG de Warner Bros) et compagnie, et on attend maintenant de savoir si les organismes de r&eacute;gulation vont approuver ce rachat, puisqu&rsquo;il menace Hollywood dans son ensemble, milieu de plus en plus consolid&eacute;. Le New York Post r&eacute;v&egrave;le d&rsquo;ailleurs que plusieurs responsables de la Maison Blanche commencent &agrave; se r&eacute;unir pour contester cette acquisition. L&agrave; o&ugrave; le rachat par Paramount serait sans doute pass&eacute; comme une lettre &agrave; la Poste, &eacute;tant donn&eacute; que la famille Ellison est une grande alli&eacute;e de Donald Trump.\r\n\r\n', 'public/uploads/69344f9751653.jpg', NULL, '2025-12-06 16:45:27', NULL, 1),
(27, 'Replaced s’est fait attendre.', 'Replaced s&rsquo;est fait attendre. Le jeu d&rsquo;action cyberpunk en 2.5D a sans cesse &eacute;t&eacute; repouss&eacute; et n&rsquo;a fait qu&rsquo;&ecirc;tre report&eacute; d&rsquo;une ann&eacute;e &agrave; l&rsquo;autre depuis son annonce. Il faut dire que le projet est ambitieux, notamment via sa direction artistique qui flatte la r&eacute;tine. Il fallait donc du temps pour arriver au bout de cette vision, et ce temps est justement venu. Sad Cat Studios nous avait promis que son jeu arriverait au printemps 2026, mais il a menti. Un joli mensonge, puisque Replaced arrivera finalement quelques jours plus t&ocirc;t, le 12 mars 2026.', 'public/uploads/693450e7df7b7.jpg', NULL, '2025-12-06 16:51:03', NULL, 1),
(30, 'my time at ', 'erri&egrave;re les jeux My Time at, comme My Time at Sandrock, Pathea Games a d&rsquo;autres ambitions. Depuis des ann&eacute;es, il travaille sur un projet qui lui demande beaucoup de ressources, c&rsquo;est pourquoi il &eacute;tait rattach&eacute; dans un premier temps au PlayStation China Hero Project. Mais avec le temps, le studio a su trouver son ind&eacute;pendance, et par les temps qui courent, mieux veut ne plus se limiter &agrave; une seule plateforme. C&rsquo;est pourquoi The God Slayer prend son envol aujourd&rsquo;hui, en confirmant sa sortie sur PC et Xbox Series en plus de la PS5.', 'public/uploads/69346547d26d1.jpeg', NULL, '2025-12-06 18:17:59', NULL, 1),
(34, 'jeux vidéo notables', 'Cette riche ann&eacute;e 2025 touche maintenant &agrave; sa fin, mais malgr&eacute; cela, quelques sorties jeux vid&eacute;o notables vous attendent. On fait ici le point sur tous les jeux &agrave; ne pas manquer en ce mois de d&eacute;cembre 2025, plus riche que ce qu&rsquo;on ne pourrait l&rsquo;imaginer.es studios et &eacute;diteurs ont encore quelques jours pour sortir des jeux qui atterriront au pied de votre sapin. Comme d&rsquo;habitude, d&eacute;cembre est un mois plus calme que les autres, mais vous verrez ici quelques sorties jeux vid&eacute;o &agrave; ne pas manquer durant ces prochaines semaines.\r\n\r\nSommaire', 'public/uploads/6934ad0b31e5a.jpg', NULL, '2025-12-06 23:24:11', NULL, 1),
(35, 'nouha bebn mesaoud ', 'Les studios et &eacute;diteurs ont encore quelques jours pour sortir des jeux qui atterriront au pied de votre sapin. Comme d&rsquo;habitude, d&eacute;cembre est un mois plus calme que les autres, mais vous verrez ici quelques sorties jeux vid&eacute;o &agrave; ne pas manquer durant ces prochaines semaines.\r\n\r\n', 'public/uploads/6934c600658ac.png', NULL, '2025-12-07 01:10:40', NULL, 1);

-- --------------------------------------------------------

--
-- Structure de la table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `content` text NOT NULL,
  `article_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `comments`
--

INSERT INTO `comments` (`id`, `content`, `article_id`, `user_id`, `created_at`, `updated_at`) VALUES
(18, 'merci beaucoup ', 27, 1, '2025-12-06 18:04:36', NULL),
(20, 'super bien ', 34, 1, '2025-12-06 23:24:38', NULL),
(21, 'c rien ca ', 35, 1, '2025-12-07 01:10:58', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `comment_share`
--

CREATE TABLE `comment_share` (
  `id_share` int(11) NOT NULL,
  `id_comment` int(11) NOT NULL,
  `id_user_emetteur` int(11) NOT NULL,
  `id_recipient` int(11) NOT NULL,
  `shared_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `comment_share`
--

INSERT INTO `comment_share` (`id_share`, `id_comment`, `id_user_emetteur`, `id_recipient`, `shared_at`) VALUES
(4, 18, 1, 2, '2025-12-06 23:07:37'),
(5, 20, 1, 3, '2025-12-06 23:24:50'),
(6, 20, 1, 2, '2025-12-06 23:36:52'),
(7, 21, 1, 2, '0000-00-00 00:00:00'),
(8, 21, 1, 8, '0000-00-00 00:00:00');

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
(2, 'Super Gamer', 'gamer@mail.com', 'gamer_hash', 'client'),
(3, '', '', '', 'client'),
(4, 'Utilisateur 3', 'user3@test.com', 'test1234', 'client'),
(8, 'Utilisateur 3', 'utilisateur_trois_unique@test.com', 'test1234', 'client');

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
-- Index pour la table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_comment_article` (`article_id`),
  ADD KEY `fk_comment_user` (`user_id`);

--
-- Index pour la table `comment_share`
--
ALTER TABLE `comment_share`
  ADD PRIMARY KEY (`id_share`),
  ADD UNIQUE KEY `unique_share` (`id_comment`,`id_recipient`),
  ADD KEY `id_user_emetteur` (`id_user_emetteur`),
  ADD KEY `id_user_destinataire` (`id_recipient`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT pour la table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT pour la table `comment_share`
--
ALTER TABLE `comment_share`
  MODIFY `id_share` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `articles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Contraintes pour la table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `fk_comment_article` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_comment_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_user`);

--
-- Contraintes pour la table `comment_share`
--
ALTER TABLE `comment_share`
  ADD CONSTRAINT `comment_share_ibfk_1` FOREIGN KEY (`id_comment`) REFERENCES `comments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comment_share_ibfk_2` FOREIGN KEY (`id_user_emetteur`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `comment_share_ibfk_3` FOREIGN KEY (`id_recipient`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
