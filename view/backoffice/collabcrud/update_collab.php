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
    
    // Gestion de l'upload d'image
    $imagePath = $existingCollab['image'] ?? null; // Conserver l'image existante par défaut
    
    // Vérifier si un nouveau fichier image a été uploadé
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        if (in_array($file['type'], $allowedTypes) && $file['size'] <= $maxSize) {
            // Créer le dossier uploads s'il n'existe pas
            // Chemin depuis view/backoffice/collabcrud/ vers view/frontoffice/backoffice/uploads/
            $uploadDir = __DIR__ . '/../../frontoffice/backoffice/uploads/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $fileName = uniqid('collab_', true) . '.' . $extension;
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                // Chemin absolu depuis la racine du serveur web pour l'affichage
                // Format: /gamehubprjt/view/frontoffice/backoffice/uploads/filename.jpg
                $imagePath = '/gamehubprjt/view/frontoffice/backoffice/uploads/' . $fileName;
            } else {
                // Erreur lors de l'upload, garder l'image existante
                $imagePath = $existingCollab['image'] ?? null;
            }
        } else {
            // Format non supporté ou fichier trop gros, garder l'image existante
            $imagePath = $existingCollab['image'] ?? null;
        }
    }

    $collab = new CollabProject(
        $id,
        $owner_id,
        $titre,
        $description,
        $existingCollab['date_creation'],  // Conserver la date de création originale
        $statut,
        $max_membres,
        $imagePath
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
