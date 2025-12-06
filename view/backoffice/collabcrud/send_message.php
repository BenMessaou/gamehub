<?php
session_start();

// Mode développeur : permettre l'accès même sans connexion
$isLoggedIn = isset($_SESSION['user_id']);
$defaultUserId = 1; // ID par défaut pour le développeur

require_once __DIR__ . "/../../../controller/controllercollab/CollabMessageController.php";
require_once __DIR__ . "/../../../controller/controllercollab/CollabMemberController.php";
require_once __DIR__ . "/../../../controller/controllercollab/MessageModerationController.php";
require_once __DIR__ . "/../../../model/collab/CollabMessage.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $collab_id = isset($_POST['collab_id']) ? intval($_POST['collab_id']) : 0;
    $message = trim($_POST['message'] ?? '');

    if ($collab_id <= 0 || empty($message)) {
        $redirectTo = isset($_POST['redirect_to']) ? $_POST['redirect_to'] : 'view_collab';
        $redirectUrl = ($redirectTo === 'room_collab') ? 'room_collab.php' : 'view_collab.php';
        header("Location: " . $redirectUrl . "?id=" . $collab_id . "&error=message_invalid");
        exit;
    }

    // Utiliser l'ID de session s'il existe, sinon utiliser celui par défaut
    $user_id = $isLoggedIn ? $_SESSION['user_id'] : $defaultUserId;

    // Vérifier que l'utilisateur est membre (sauf en mode développeur)
    if ($isLoggedIn) {
        $memberController = new CollabMemberController();
        if (!$memberController->isMember($collab_id, $user_id)) {
            die("Erreur : vous devez être membre de cette collaboration pour envoyer des messages.");
        }
    }

    // ============================================================
    // MODÉRATION DU MESSAGE - 2 NIVEAUX
    // ============================================================
    $moderationController = new MessageModerationController();
    $moderationResult = $moderationController->moderateMessage($message);
    
    // Enregistrer le log de modération
    $moderationController->logModeration($message, $moderationResult, $user_id, $collab_id);
    
    // Vérifier le résultat de la modération
    if ($moderationResult['blocked']) {
        // Message bloqué - rediriger avec erreur
        $redirectTo = isset($_POST['redirect_to']) ? $_POST['redirect_to'] : 'view_collab';
        $redirectUrl = ($redirectTo === 'room_collab') ? 'room_collab.php' : 'view_collab.php';
        
        // Encoder la raison pour l'URL
        $reason = urlencode($moderationResult['reason']);
        header("Location: " . $redirectUrl . "?id=" . $collab_id . "&error=message_blocked&reason=" . $reason . "&level=" . $moderationResult['level']);
        exit;
    }
    
    // Message approuvé - l'envoyer
    $msg = new CollabMessage(null, $collab_id, $user_id, $message, null);
    $controller = new CollabMessageController();
    $controller->send($msg);

    // Vérifier d'où vient la requête (room_collab ou view_collab)
    $redirectTo = isset($_POST['redirect_to']) ? $_POST['redirect_to'] : 'view_collab';
    
    if ($redirectTo === 'room_collab') {
        header("Location: room_collab.php?id=" . $collab_id . "&message_sent=1");
    } else {
        header("Location: view_collab.php?id=" . $collab_id . "&message_sent=1");
    }
    exit;
}
?>
