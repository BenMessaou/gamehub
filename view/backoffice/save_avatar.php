<?php
session_start();

// Mode développeur : permettre l'accès même sans connexion
$isLoggedIn = isset($_SESSION['user_id']);
$userId = $isLoggedIn ? $_SESSION['user_id'] : 1; // ID par défaut pour le développeur

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get JSON data
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
    exit;
}

// Validate required fields
if (!isset($data['avatar_data'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Avatar data is required']);
    exit;
}

// Here you would save to database
// Example structure:
/*
CREATE TABLE IF NOT EXISTS `user_avatars` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `avatar_data` TEXT NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
*/

// For now, we'll just return success
// In production, you would:
// 1. Connect to database
// 2. Save avatar_data as JSON
// 3. Optionally save avatar image if generated

$avatarData = $data['avatar_data'];
$avatarName = $data['avatar_name'] ?? 'Mon Avatar';

// Simulate save operation
$response = [
    'success' => true,
    'message' => 'Avatar sauvegardé avec succès !',
    'avatar_id' => rand(1000, 9999), // In production, use actual ID from database
    'avatar_name' => $avatarName,
    'saved_at' => date('Y-m-d H:i:s')
];

echo json_encode($response);

