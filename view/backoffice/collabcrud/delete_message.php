<?php
session_start();

// Mode développeur : permettre l'accès même sans connexion
$isLoggedIn = isset($_SESSION['user_id']);
$defaultUserId = 1;

require_once __DIR__ . "/../../../controller/controllercollab/CollabMessageController.php";
require_once __DIR__ . "/../../../controller/controllercollab/CollabProjectController.php";

if (!isset($_GET['id']) || !isset($_GET['collab_id'])) {
    die("Paramètres manquants.");
}

$id = intval($_GET['id']);
$collab_id = intval($_GET['collab_id']);

$controller = new CollabMessageController();
$message = $controller->getMessageById($id);

if (!$message) {
    die("Message introuvable.");
}

// Vérifier les permissions : seul l'auteur du message ou le propriétaire peut supprimer
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
        die("Erreur : vous n'avez pas la permission de supprimer ce message.");
    }
}

$controller->delete($id);
header("Location: view_collab.php?id=" . $collab_id . "&message_deleted=1");
exit;
?>
