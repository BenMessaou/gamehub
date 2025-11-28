<?php
session_start();

// Mode développeur : permettre l'accès même sans connexion
$isLoggedIn = isset($_SESSION['user_id']);
$defaultUserId = 1;

require_once __DIR__ . "/../../../controller/controllercollab/CollabMessageController.php";
require_once __DIR__ . "/../../../controller/controllercollab/CollabProjectController.php";

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $newMessage = trim($_POST['message'] ?? '');
    $collab_id = isset($_POST['collab_id']) ? intval($_POST['collab_id']) : 0;

    if ($id <= 0 || empty($newMessage) || $collab_id <= 0) {
        header("Location: view_collab.php?id=" . $collab_id . "&error=message_update_invalid");
        exit;
    }

    $controller = new CollabMessageController();
    $message = $controller->getMessageById($id);

    if (!$message) {
        die("Message introuvable.");
    }

    // Vérifier les permissions : seul l'auteur du message ou le propriétaire peut modifier
    $user_id = $isLoggedIn ? $_SESSION['user_id'] : $defaultUserId;
    
    if ($isLoggedIn) {
        $projectController = new CollabProjectController();
        $collab = $projectController->getById($collab_id);
        
        if (!$collab) {
            die("Projet introuvable.");
        }
        
        $isOwner = ($collab['owner_id'] == $user_id);
        $isAuthor = ($message['user_id'] == $user_id);
        
        if (!$isOwner && !$isAuthor) {
            die("Erreur : vous n'avez pas la permission de modifier ce message.");
        }
    }

    $controller->updateMessage($id, $newMessage);
    header("Location: view_collab.php?id=" . $collab_id . "&message_updated=1");
    exit;
}
?>
