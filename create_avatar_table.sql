-- ============================================================
-- Script SQL pour créer la table des avatars utilisateurs
-- Base de données: bdgamehub
-- ============================================================

USE bdgamehub;

-- Table des avatars utilisateurs
CREATE TABLE IF NOT EXISTS `user_avatars` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `avatar_name` VARCHAR(255) NOT NULL DEFAULT 'Mon Avatar',
    `avatar_data` TEXT NOT NULL COMMENT 'JSON data containing avatar configuration',
    `avatar_image` VARCHAR(500) DEFAULT NULL COMMENT 'Path to generated avatar image',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_user` (`user_id`),
    INDEX `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Exemple de structure JSON pour avatar_data:
-- {
--     "hair": "hair1",
--     "face": "face1",
--     "helmet": "helmet1",
--     "shirt": "shirt1",
--     "pants": "pants1",
--     "shoes": "shoes1",
--     "accessories": ["acc1", "acc2"]
-- }

