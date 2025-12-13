<?php
session_start();

// Mode développeur : permettre l'accès même sans connexion
$isLoggedIn = isset($_SESSION['user_id']);
$userId = $isLoggedIn ? $_SESSION['user_id'] : null;

// Charger controllers
require_once __DIR__ . "/../../../controller/controllercollab/CollabProjectController.php";
require_once __DIR__ . "/../../../controller/controllercollab/CollabMemberController.php";

$projectController = new CollabProjectController();
$memberController = new CollabMemberController();

// Vérifier que la requête est POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: view_collab.php?id=" . ($_GET['id'] ?? ''));
    exit;
}

// Récupérer les données du formulaire
$collab_id = isset($_POST['collab_id']) ? intval($_POST['collab_id']) : 0;
$new_status = isset($_POST['statut']) ? trim($_POST['statut']) : '';

// Valider les données
if ($collab_id <= 0 || empty($new_status)) {
    header("Location: view_collab.php?id=" . $collab_id . "&status_error=invalid");
    exit;
}

// Valider le statut (doit être l'un des statuts autorisés)
$allowed_statuses = ['ouvert', 'en_cours', 'ferme'];
if (!in_array($new_status, $allowed_statuses)) {
    header("Location: view_collab.php?id=" . $collab_id . "&status_error=invalid");
    exit;
}

// Récupérer les informations de la collaboration
$collab = $projectController->getById($collab_id);

if (!$collab) {
    header("Location: view_collab.php?id=" . $collab_id . "&status_error=not_found");
    exit;
}

// Vérifier les permissions
// Mode développeur : permettre le changement si l'utilisateur n'est pas connecté
$canUpdate = false;
if (!$isLoggedIn) {
    // Mode développeur : autoriser
    $canUpdate = true;
    $ownerId = null; // Passer null pour le mode développeur
} else {
    // Vérifier que l'utilisateur est le propriétaire
    $canUpdate = ($collab['owner_id'] == $userId);
    $ownerId = $userId;
}

if (!$canUpdate) {
    header("Location: view_collab.php?id=" . $collab_id . "&status_error=unauthorized");
    exit;
}

// Mettre à jour le statut
$success = $projectController->updateStatus($collab_id, $new_status, $ownerId);

if ($success) {
    header("Location: view_collab.php?id=" . $collab_id . "&status_updated=1");
} else {
    header("Location: view_collab.php?id=" . $collab_id . "&status_error=update_failed");
}

exit;
?>

