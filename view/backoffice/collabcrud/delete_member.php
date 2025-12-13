<?php
session_start();

// Mode développeur : permettre l'accès même sans connexion
$isLoggedIn = isset($_SESSION['user_id']);
$devMode = isset($_POST['dev_mode']) && $_POST['dev_mode'] == '1';

if (!isset($_POST['member_id'], $_POST['collab_id'])) {
    die("Données manquantes.");
}

$member_id = $_POST['member_id'];
$collab_id = $_POST['collab_id'];

require_once __DIR__ . "/../../../controller/controllercollab/CollabMemberController.php";
require_once __DIR__ . "/../../../controller/controllercollab/CollabProjectController.php";

$memberController = new CollabMemberController();
$projectController = new CollabProjectController();

// Récupérer le projet pour vérifier que l'utilisateur est le propriétaire
$collab = $projectController->getById($collab_id);
if (!$collab) {
    die("Projet introuvable.");
}

// Récupérer le membre pour vérifier son rôle
$members = $memberController->getMembers($collab_id);
$memberToDelete = null;
foreach ($members as $m) {
    if ($m['id'] == $member_id) {
        $memberToDelete = $m;
        break;
    }
}

if (!$memberToDelete) {
    die("Membre introuvable.");
}

// Vérifier que ce n'est pas le propriétaire (ne pas permettre de supprimer le propriétaire)
if ($memberToDelete['role'] == 'owner') {
    header("Location: view_collab.php?id=" . $collab_id . "&error=cannot_delete_owner");
    exit;
}

// Vérifier les permissions : seul le propriétaire peut supprimer des membres
if ($devMode && !$isLoggedIn) {
    // Mode développeur : suppression autorisée
    $memberController->remove($member_id);
    header("Location: view_collab.php?id=" . $collab_id . "&member_deleted=1");
    exit;
} else {
    // Mode normal : vérifier que l'utilisateur est le propriétaire
    if (!$isLoggedIn) {
        die("Vous devez être connecté pour supprimer un membre.");
    }
    
    if ($collab['owner_id'] != $_SESSION['user_id']) {
        die("Erreur : vous n'êtes pas le propriétaire de ce projet. Seul le propriétaire peut supprimer des membres.");
    }
    
    // Ne pas permettre de se supprimer soi-même
    if ($memberToDelete['user_id'] == $_SESSION['user_id']) {
        header("Location: view_collab.php?id=" . $collab_id . "&error=cannot_delete_self");
        exit;
    }
    
    $memberController->remove($member_id);
    header("Location: view_collab.php?id=" . $collab_id . "&member_deleted=1");
    exit;
}
?>
