-- Migration script to add statut, date_soumission, and date_publication columns to projects table
-- Run this script in your MySQL database (bdgamehub)

USE bdgamehub;

-- Add statut column (status: 'en_attente' for pending, 'publie' for published)
ALTER TABLE projects 
ADD COLUMN statut VARCHAR(20) DEFAULT 'en_attente' AFTER screenshots;

-- Add date_soumission column (submission date)
ALTER TABLE projects 
ADD COLUMN date_soumission DATETIME DEFAULT CURRENT_TIMESTAMP AFTER statut;

-- Add date_publication column (publication date - when approved)
ALTER TABLE projects 
ADD COLUMN date_publication DATETIME NULL AFTER date_soumission;

-- Update existing records to have 'publie' status (assuming existing games are already published)
UPDATE projects SET statut = 'publie' WHERE statut IS NULL OR statut = '';

-- Optional: Set date_soumission for existing records if they don't have one
UPDATE projects SET date_soumission = NOW() WHERE date_soumission IS NULL;

-- Optional: Set date_publication for existing published records
UPDATE projects SET date_publication = NOW() WHERE statut = 'publie' AND date_publication IS NULL;

