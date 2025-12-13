<?php
session_start();

// Mode développeur : permettre l'accès même sans connexion
$isLoggedIn = isset($_SESSION['user_id']);

require_once __DIR__ . "/../../../controller/controllercollab/CollabTaskController.php";
require_once __DIR__ . "/../../../controller/controllercollab/CollabProjectController.php";
require_once __DIR__ . "/../../../controller/controllercollab/CollabMemberController.php";
require_once __DIR__ . "/../../../model/collab/CollabTask.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $collab_id = isset($_POST['collab_id']) ? intval($_POST['collab_id']) : 0;
    $task = trim($_POST['task'] ?? '');

    if ($collab_id <= 0 || empty($task)) {
        header("Location: view_collab.php?id=" . $collab_id . "&error=task_invalid");
        exit;
    }

    // Vérifier que l'utilisateur est membre (sauf en mode développeur)
    if ($isLoggedIn) {
        $memberController = new CollabMemberController();
        if (!$memberController->isMember($collab_id, $_SESSION['user_id'])) {
            die("Erreur : vous devez être membre de cette collaboration pour ajouter des tâches.");
        }
    }

    $newTask = new CollabTask(null, $collab_id, $task, false);
    $controller = new CollabTaskController();
    $controller->add($newTask);

    header("Location: view_collab.php?id=" . $collab_id . "&task_added=1");
    exit;
}
?>
