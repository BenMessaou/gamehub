<?php
session_start();

// Mode développeur : permettre l'accès même sans connexion
$isLoggedIn = isset($_SESSION['user_id']);

require_once __DIR__ . "/../../../controller/controllercollab/CollabTaskController.php";
require_once __DIR__ . "/../../../controller/controllercollab/CollabMemberController.php";

if (!isset($_GET['id']) || !isset($_GET['collab_id'])) {
    die("Paramètres manquants.");
}

$task_id = intval($_GET['id']);
$collab_id = intval($_GET['collab_id']);

// Vérifier que l'utilisateur est membre (sauf en mode développeur)
if ($isLoggedIn) {
    $memberController = new CollabMemberController();
    if (!$memberController->isMember($collab_id, $_SESSION['user_id'])) {
        die("Erreur : vous devez être membre de cette collaboration pour modifier des tâches.");
    }
}

$controller = new CollabTaskController();
$controller->markDone($task_id);

header("Location: view_collab.php?id=" . $collab_id . "&task_updated=1");
exit;
?>
