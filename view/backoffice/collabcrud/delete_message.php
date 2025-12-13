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

if (!$data || !isset($data['message_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Message ID is required']);
    exit;
}

$messageId = intval($data['message_id']);

// Charger controllers
require_once __DIR__ . "/../../../controller/controllercollab/CollabMessageController.php";

$messageController = new CollabMessageController();

// Vérifier que le message existe et que l'utilisateur est le propriétaire
$message = $messageController->getMessageById($messageId);

if (!$message) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Message not found']);
    exit;
}

// Vérifier que l'utilisateur est le propriétaire du message
if ($message['user_id'] != $userId) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'You can only delete your own messages']);
    exit;
}

// Supprimer le message
$success = $messageController->delete($messageId);

if ($success) {
    echo json_encode([
        'success' => true,
        'message' => 'Message supprimé avec succès'
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la suppression du message'
    ]);
}
?>
