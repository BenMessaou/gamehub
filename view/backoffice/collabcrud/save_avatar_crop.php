<?php
session_start();
require_once '../../config/config.php';
$db = config::getConnexion();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Non authentifié']);
    exit;
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Données invalides']);
    exit;
}

$crop_scale = isset($data['crop_scale']) ? floatval($data['crop_scale']) : 0.7;
$crop_x = isset($data['crop_x']) ? floatval($data['crop_x']) : 0;
$crop_y = isset($data['crop_y']) ? floatval($data['crop_y']) : -15;

try {
    // Vérifier si l'utilisateur a déjà un avatar
    $stmt = $db->prepare("SELECT avatar_data FROM user_avatars WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existing) {
        // Mettre à jour l'avatar existant avec les paramètres de recadrage
        $avatarData = json_decode($existing['avatar_data'], true);
        if (!$avatarData) {
            $avatarData = [];
        }
        
        // Ajouter les paramètres de recadrage
        $avatarData['crop'] = [
            'scale' => $crop_scale,
            'x' => $crop_x,
            'y' => $crop_y
        ];
        
        $stmt = $db->prepare("UPDATE user_avatars SET avatar_data = ?, updated_at = NOW() WHERE user_id = ?");
        $stmt->execute([json_encode($avatarData), $user_id]);
    } else {
        // Créer un nouvel enregistrement avec les paramètres de recadrage
        $avatarData = [
            'crop' => [
                'scale' => $crop_scale,
                'x' => $crop_x,
                'y' => $crop_y
            ]
        ];
        
        $stmt = $db->prepare("INSERT INTO user_avatars (user_id, avatar_data, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
        $stmt->execute([$user_id, json_encode($avatarData)]);
    }
    
    echo json_encode(['success' => true, 'message' => 'Paramètres de recadrage sauvegardés']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur base de données: ' . $e->getMessage()]);
}
