-- ============================================================
-- Script SQL pour créer les tables de collaboration
-- Base de données: bdgamehub
-- ============================================================

USE bdgamehub;

-- Table des projets collaboratifs
CREATE TABLE IF NOT EXISTS `collab_project` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `owner_id` INT NOT NULL,
    `titre` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `date_creation` DATE NOT NULL,
    `statut` ENUM('ouvert', 'en_cours', 'ferme') DEFAULT 'ouvert',
    `max_membres` INT NOT NULL DEFAULT 10,
    `image` VARCHAR(500) DEFAULT NULL,
    INDEX `idx_owner` (`owner_id`),
    INDEX `idx_statut` (`statut`),
    INDEX `idx_date` (`date_creation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table des membres des collaborations
CREATE TABLE IF NOT EXISTS `collab_members` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `collab_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `role` ENUM('owner', 'moderateur', 'membre') DEFAULT 'membre',
    `date_ajout` DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_member` (`collab_id`, `user_id`),
    INDEX `idx_collab` (`collab_id`),
    INDEX `idx_user` (`user_id`),
    INDEX `idx_role` (`role`),
    FOREIGN KEY (`collab_id`) REFERENCES `collab_project`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table des messages (optionnel - pour fonctionnalité future)
CREATE TABLE IF NOT EXISTS `collab_message` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `collab_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `message` TEXT NOT NULL,
    `date_message` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_collab` (`collab_id`),
    INDEX `idx_user` (`user_id`),
    INDEX `idx_date` (`date_message`),
    FOREIGN KEY (`collab_id`) REFERENCES `collab_project`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table des tâches (optionnel - pour fonctionnalité future)
CREATE TABLE IF NOT EXISTS `collab_task` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `collab_id` INT NOT NULL,
    `task` VARCHAR(500) NOT NULL,
    `done` BOOLEAN DEFAULT FALSE,
    `date_creation` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_collab` (`collab_id`),
    INDEX `idx_done` (`done`),
    FOREIGN KEY (`collab_id`) REFERENCES `collab_project`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table des compétences requises (optionnel - pour fonctionnalité future)
CREATE TABLE IF NOT EXISTS `collab_skill_required` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `collab_id` INT NOT NULL,
    `skill` VARCHAR(100) NOT NULL,
    INDEX `idx_collab` (`collab_id`),
    FOREIGN KEY (`collab_id`) REFERENCES `collab_project`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================
-- NOTES:
-- 1. Assurez-vous que la table 'users' existe avant d'exécuter ce script
-- 2. Les clés étrangères nécessitent que la table users ait une colonne id
-- 3. Si vous n'avez pas de table users, supprimez les contraintes FOREIGN KEY
-- ============================================================

