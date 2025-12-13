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

// Database connection
require_once __DIR__ . "/../../config/config.php";
$db = config::getConnexion();

$avatarData = json_encode($data['avatar_data']);
$avatarName = $data['avatar_name'] ?? 'Mon Qbit';

try {
    // Check if an avatar already exists for this user
    $sqlCheck = "SELECT id FROM user_avatars WHERE user_id = ?";
    $stmtCheck = $db->prepare($sqlCheck);
    $stmtCheck->execute([$userId]);
    $existingAvatar = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if ($existingAvatar) {
        // Update existing avatar
        $sql = "UPDATE user_avatars SET avatar_name = ?, avatar_data = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $stmt = $db->prepare($sql);
        $success = $stmt->execute([$avatarName, $avatarData, $existingAvatar['id']]);
        $avatarId = $existingAvatar['id'];
    } else {
        // Insert new avatar
        $sql = "INSERT INTO user_avatars (user_id, avatar_name, avatar_data) VALUES (?, ?, ?)";
        $stmt = $db->prepare($sql);
        $success = $stmt->execute([$userId, $avatarName, $avatarData]);
        $avatarId = $db->lastInsertId();
    }

    if ($success) {
        $response = [
            'success' => true,
            'message' => 'Avatar sauvegardé avec succès !',
            'avatar_id' => $avatarId,
            'avatar_name' => $avatarName,
            'saved_at' => date('Y-m-d H:i:s')
        ];
    } else {
        $response = ['success' => false, 'message' => 'Erreur lors de la sauvegarde de l\'avatar.'];
    }

} catch (PDOException $e) {
    error_log("Erreur PDO lors de la sauvegarde de l'avatar: " . $e->getMessage());
    $response = ['success' => false, 'message' => 'Erreur serveur lors de la sauvegarde de l\'avatar.'];
}

echo json_encode($response);

