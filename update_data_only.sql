-- Script to update data ONLY (Safe to run if columns already exist)

USE gamehub_db;

-- Update existing games with sample data
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
