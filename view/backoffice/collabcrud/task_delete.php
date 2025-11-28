<?php
session_start();

// Mode développeur : permettre l'accès même sans connexion
$isLoggedIn = isset($_SESSION['user_id']);

require_once __DIR__ . "/../../../controller/controllercollab/CollabTaskController.php";
require_once __DIR__ . "/../../../controller/controllercollab/CollabProjectController.php";
require_once __DIR__ . "/../../../controller/controllercollab/CollabMemberController.php";

if (!isset($_GET['id']) || !isset($_GET['collab_id'])) {
    die("Paramètres manquants.");
}

$task_id = intval($_GET['id']);
$collab_id = intval($_GET['collab_id']);

// Vérifier que l'utilisateur est le propriétaire (sauf en mode développeur)
if ($isLoggedIn) {
    $projectController = new CollabProjectController();
    $collab = $projectController->getById($collab_id);
    
    if (!$collab) {
        die("Projet introuvable.");
    }
    
    if ($collab['owner_id'] != $_SESSION['user_id']) {
        die("Erreur : seul le propriétaire peut supprimer des tâches.");
    }
}

$controller = new CollabTaskController();
$controller->delete($task_id);

header("Location: view_collab.php?id=" . $collab_id . "&task_deleted=1");
exit;
?>
