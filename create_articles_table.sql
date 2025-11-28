-- Script SQL pour créer la table articles dans la base de données
-- Structure similaire à la table projects mais adaptée pour les articles

-- Créer la table articles
CREATE TABLE IF NOT EXISTS `articles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `titre` VARCHAR(255) NOT NULL,
    `auteur` VARCHAR(255) NOT NULL,
    `date_creation` DATE NOT NULL,
    `categorie` VARCHAR(100) NOT NULL,
    `contenu` TEXT NOT NULL,
    `image` VARCHAR(255) DEFAULT NULL,
    `auteur_id` INT NOT NULL,
    `lieu` VARCHAR(255) DEFAULT NULL,
    `tags` JSON DEFAULT NULL,
    `statut` ENUM('en_attente', 'publie') DEFAULT 'en_attente',
    `date_soumission` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `date_publication` DATETIME DEFAULT NULL,
    INDEX `idx_article_auteur` (`auteur_id`),
    INDEX `idx_article_statut` (`statut`),
    INDEX `idx_article_categorie` (`categorie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

