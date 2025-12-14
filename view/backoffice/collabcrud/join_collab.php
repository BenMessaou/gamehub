<?php
session_start();

// Exiger que l'utilisateur soit connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../frontoffice/login_client.php?redirect=" . urlencode($_SERVER['HTTP_REFERER'] ?? ''));
    exit;
}

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

// Utiliser l'ID de session utilisateur
$user_id = $_SESSION['user_id'];

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

// Ajouter l'utilisateur comme membre
$newMember = new CollabMember(
    null,
    $collab_id,
    $user_id,
    "membre"
);

$memberController->add($newMember);

// Vérifier si le groupe est maintenant plein après l'ajout
$updatedMembers = $memberController->getMembers($collab_id);
if (count($updatedMembers) >= $collab['max_membres']) {
    // Le collab est maintenant complet, rediriger vers la room
    header("Location: room_collab.php?id=" . $collab_id);
    exit;
}

// Redirection vers la page des collaborations avec message de succès
header("Location: ../../frontoffice/collaborations.php?joined=1&collab_id=" . $collab_id);
exit;

?>
