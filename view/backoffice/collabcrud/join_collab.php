<?php
session_start();

// Mode développeur : permettre l'accès même sans connexion
$isLoggedIn = isset($_SESSION['user_id']);
$defaultUserId = 1; // ID par défaut pour le développeur

require_once __DIR__ . "/../../../controller/controllercollab/CollabMemberController.php";
require_once __DIR__ . "/../../../controller/controllercollab/CollabProjectController.php";
require_once __DIR__ . "/../../../model/collab/CollabMember.php";

$memberController = new CollabMemberController();
$projectController = new CollabProjectController();

// Vérifier si on a reçu un collab_id
if (!isset($_POST['collab_id'])) {
    die("ID du projet introuvable.");
}

$collab_id = $_POST['collab_id'];

// Utiliser l'ID de session s'il existe, sinon utiliser celui du formulaire ou l'ID par défaut
$user_id = $isLoggedIn ? $_SESSION['user_id'] : (isset($_POST['user_id']) ? intval($_POST['user_id']) : $defaultUserId);

// Récupérer le projet collaboratif
$collab = $projectController->getById($collab_id);
if (!$collab) {
    die("Projet collaboratif introuvable.");
}

// Récupérer les membres actuels
$members = $memberController->getMembers($collab_id);

// Vérifier si l'user est déjà membre
foreach ($members as $m) {
    if ($m['user_id'] == $user_id) {
        header("Location: ../../frontoffice/collaborations.php?error=already_member");
        exit;
    }
}

// Vérifier si le groupe est plein
if (count($members) >= $collab['max_membres']) {
    header("Location: ../../frontoffice/collaborations.php?error=full");
    exit;
}

// Ajouter l'utilisateur comme membre
$newMember = new CollabMember(
    null,
    $collab_id,
    $user_id,
    "membre"
);

$memberController->add($newMember);

// Redirection vers la page des collaborations avec message de succès
header("Location: ../../frontoffice/collaborations.php?joined=1&collab_id=" . $collab_id);
exit;

?>
