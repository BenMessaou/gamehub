<?php
session_start();

// Mode développeur : permettre l'accès même sans connexion
require_once __DIR__ . "/../../../controller/controllercollab/CollabProjectController.php";
require_once __DIR__ . "/../../../model/collab/CollabProject.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $controller = new CollabProjectController();
    $id = $_POST['id'];
    
    // Récupérer le projet existant pour conserver l'owner_id
    $existingCollab = $controller->getById($id);
    if (!$existingCollab) {
        die("Projet introuvable.");
    }
    
    // Utiliser l'owner_id du projet existant (mode développeur)
    $owner_id = $existingCollab['owner_id'];
    $titre = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $statut = $_POST['statut'] ?? 'ouvert';
    $max_membres = isset($_POST['max_membres']) ? intval($_POST['max_membres']) : 5;
    $image = isset($_POST['image']) ? trim($_POST['image']) : '';

    $collab = new CollabProject(
        $id,
        $owner_id,
        $titre,
        $description,
        $existingCollab['date_creation'],  // Conserver la date de création originale
        $statut,
        $max_membres,
        $image ?: null
    );

    $ok = $controller->update($collab);

    if ($ok) {
        header("Location: view_collab.php?id=" . $id . "&updated=1");
        exit;
    } else {
        header("Location: edit_collab.php?id=" . $id . "&error=1");
        exit;
    }
}
?>
