-- Ajouter le champ audio_path à la table collab_messages
ALTER TABLE `collab_messages` 
ADD COLUMN `audio_path` VARCHAR(500) NULL DEFAULT NULL COMMENT 'Chemin vers le fichier audio du message vocal' AFTER `message`,
ADD COLUMN `audio_duration` INT NULL DEFAULT NULL COMMENT 'Durée du message vocal en secondes' AFTER `audio_path`,
ADD INDEX `idx_audio` (`audio_path`);

