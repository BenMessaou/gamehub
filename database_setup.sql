-- Script to update the games table structure

USE gamehub_db;

-- 1. Add missing columns if they don't exist
-- We use a stored procedure to check existence to avoid errors, or just simple ALTER statements that might fail if exists (but standard SQL doesn't have IF NOT EXISTS for columns easily in all versions).
-- For simplicity in XAMPP/MariaDB, we'll try to add them. If they exist, it might error, but that's okay for a setup script.
-- Better approach: Just run these. If they fail, it means they might already exist.

ALTER TABLE games ADD COLUMN image VARCHAR(255) DEFAULT 'https://via.placeholder.com/300';
ALTER TABLE games ADD COLUMN category VARCHAR(50) DEFAULT 'action';
ALTER TABLE games ADD COLUMN rating DECIMAL(3,1) DEFAULT 0.0;

-- 2. Update existing games with some sample data so they look good
UPDATE games SET 
    image = 'https://imgs.hipertextual.com/wp-content/uploads/2023/07/fc-24-scaled.jpg', 
    category = 'sport',
    rating = 4.7
WHERE name LIKE '%FIFA%';

UPDATE games SET 
    image = 'https://tse4.mm.bing.net/th/id/OIP.NZ53080KQK6_O7wTDRTFxwHaEK?pid=Api&P=0&h=180', 
    category = 'rpg',
    rating = 3.8
WHERE name LIKE '%Cyberpunk%';

UPDATE games SET 
    image = 'https://assets.nintendo.com/image/upload/v1681238674/Microsites/zelda-tears-of-the-kingdom/videos/posters/totk_microsite_officialtrailer3_1304xj47am', 
    category = 'aventure',
    rating = 5.0
WHERE name LIKE '%Zelda%';

-- Update the new game "Valorant" if it exists
UPDATE games SET
    image = 'https://cdn.arstechnica.net/wp-content/uploads/2020/04/valorant-listing-800x450.jpg',
    category = 'action',
    rating = 4.5
WHERE name LIKE '%Valorant%';
