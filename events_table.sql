-- SQL for 'events' table for import into XAMPP
CREATE TABLE `events` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT DEFAULT NULL, -- the user who suggested the event, NULL if admin
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `eventType` VARCHAR(50) NOT NULL,
    `platform` VARCHAR(50),
    `location` VARCHAR(255),
    `startDate` DATETIME NOT NULL,
    `endDate` DATETIME NOT NULL,
    `ticketPrice` DECIMAL(10,2) DEFAULT 0.00,
    `availability` INT DEFAULT NULL,
    `prizePool` DECIMAL(10,2) DEFAULT 0.00,
    `imageURL` VARCHAR(255),
    `status` ENUM('pending','accepted','rejected') DEFAULT 'pending', -- admin approval
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
