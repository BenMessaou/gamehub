<?php
session_start();

// Mode développeur : permettre l'accès même sans connexion
$isLoggedIn = isset($_SESSION['user_id']);

require_once __DIR__ . "/../../../controller/controllercollab/CollabMessageController.php";
require_once __DIR__ . "/../../../controller/controllercollab/CollabMemberController.php";

header('Content-Type: application/json');

if (!isset($_GET['collab_id'])) {
    echo json_encode(['success' => false, 'error' => 'Missing collab_id']);
    exit;
}

$collab_id = intval($_GET['collab_id']);

// Vérifier que l'utilisateur est membre (sauf en mode développeur)
$memberController = new CollabMemberController();
$currentUserId = $isLoggedIn ? $_SESSION['user_id'] : 1;

if ($isLoggedIn) {
    if (!$memberController->isMember($collab_id, $currentUserId)) {
        echo json_encode(['success' => false, 'error' => 'Not a member']);
        exit;
    }
}

// Récupérer les messages
$messageController = new CollabMessageController();
$messages = $messageController->getMessages($collab_id);

// Formater les messages pour JSON
$formattedMessages = [];
foreach ($messages as $msg) {
    $formattedMessages[] = [
        'id' => $msg['id'],
        'user_id' => $msg['user_id'],
        'message' => $msg['message'] ?? '',
        'date_message' => $msg['date_message'],
        'audio_path' => $msg['audio_path'] ?? null,
        'audio_duration' => isset($msg['audio_duration']) ? intval($msg['audio_duration']) : null
    ];
}

echo json_encode([
    'success' => true,
    'messages' => $formattedMessages
]);
?>

