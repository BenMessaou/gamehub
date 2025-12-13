<?php
session_start();

// Mode développeur : permettre l'accès même sans connexion
$isLoggedIn = isset($_SESSION['user_id']);
$devMode = isset($_POST['dev_mode']) && $_POST['dev_mode'] == '1';

if (!isset($_POST['id'])) {
    die("ID manquant.");
}

$id = $_POST['id'];

require_once __DIR__ . "/../../../controller/controllercollab/CollabProjectController.php";

$controller = new CollabProjectController();

// Récupérer le projet pour vérifier l'owner
$collab = $controller->getById($id);
if (!$collab) {
    die("Projet introuvable.");
}

// En mode développeur, permettre la suppression sans vérification du propriétaire
if ($devMode && !$isLoggedIn) {
    // Mode développeur : suppression directe
    $deleted = $controller->delete($id, null);
} else {
    // Mode normal : vérifier que l'utilisateur est le propriétaire
    $ownerId = $isLoggedIn ? $_SESSION['user_id'] : null;
    if (!$ownerId) {
        die("Vous devez être connecté pour supprimer ce projet.");
    }
    
    // Vérifier que l'utilisateur est le propriétaire
    if ($collab['owner_id'] != $ownerId) {
        die("Erreur : vous n'êtes pas le propriétaire de ce projet.");
    }
    
    $deleted = $controller->delete($id, $ownerId);
}

if ($deleted) {
    header("Location: ../../frontoffice/collaborations.php?deleted=1");
    exit;
} else {
    header("Location: view_collab.php?id=" . $id . "&error=delete_failed");
    exit;
}
?>
