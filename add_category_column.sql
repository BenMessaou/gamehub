USE bdgamehub;

ALTER TABLE `games`
ADD COLUMN `category` VARCHAR(50) NOT NULL DEFAULT 'Autre' AFTER `image`;
