-- ============================================================
-- Script SQL pour créer les tables de modération des messages
-- Base de données: bdgamehub
-- ============================================================

USE bdgamehub;

-- Table des logs de modération
CREATE TABLE IF NOT EXISTS `message_moderation_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `collab_id` INT NOT NULL,
    `message` TEXT NOT NULL,
    `moderation_result` TEXT NOT NULL COMMENT 'JSON avec le résultat de la modération',
    `scores` TEXT DEFAULT NULL COMMENT 'JSON avec les scores IA',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_user` (`user_id`),
    INDEX `idx_collab` (`collab_id`),
    INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table des messages modérés (pour révision manuelle)
CREATE TABLE IF NOT EXISTS `moderated_messages` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `original_message_id` INT DEFAULT NULL,
    `user_id` INT NOT NULL,
    `collab_id` INT NOT NULL,
    `original_message` TEXT NOT NULL,
    `moderated_message` TEXT DEFAULT NULL,
    `moderation_reason` TEXT NOT NULL,
    `status` ENUM('pending', 'approved', 'rejected', 'edited') DEFAULT 'pending',
    `moderator_id` INT DEFAULT NULL COMMENT 'ID du modérateur qui a approuvé/rejeté',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `reviewed_at` DATETIME DEFAULT NULL,
    INDEX `idx_user` (`user_id`),
    INDEX `idx_collab` (`collab_id`),
    INDEX `idx_status` (`status`),
    INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

