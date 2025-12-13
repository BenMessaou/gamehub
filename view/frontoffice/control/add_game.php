<?php
session_start();
include '../../../controller/ProjectController.php';
require_once __DIR__ . '/../../../model/Project.php';

$error = "";
$projectC = new ProjectController();

$uploadDir = __DIR__ . '/../../backoffice/uploads/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if (
    isset($_POST["nom"]) && isset($_POST["developpeur"]) && isset($_POST["date_creation"]) &&
    isset($_POST["categorie"]) && isset($_POST["description"]) && isset($_POST["trailer"])
) {
    if (
        !empty($_POST["nom"]) && !empty($_POST["developpeur"]) && !empty($_POST["date_creation"]) &&
        !empty($_POST["categorie"]) && !empty($_POST["description"]) && !empty($_POST["trailer"])
    ) {
        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['image'];
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            $maxSize = 5 * 1024 * 1024; // 5MB
            
            if (in_array($file['type'], $allowedTypes) && $file['size'] <= $maxSize) {
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $fileName = uniqid('project_', true) . '.' . $extension;
                $targetPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                    $imagePath = '../backoffice/uploads/' . $fileName;
                } else {
                    $error = "Error uploading image.";
                }
            } else {
                $error = "Unsupported image format or file too large (max 5MB).";
            }
        } else {
            $error = "Cover image is required.";
        }

        $screenshotsPaths = [];
        if (isset($_FILES['screenshots']) && is_array($_FILES['screenshots']['name'])) {
            foreach ($_FILES['screenshots']['name'] as $key => $name) {
                if ($_FILES['screenshots']['error'][$key] === UPLOAD_ERR_OK) {
                    $file = [
                        'name' => $name,
                        'type' => $_FILES['screenshots']['type'][$key],
                        'tmp_name' => $_FILES['screenshots']['tmp_name'][$key],
                        'error' => $_FILES['screenshots']['error'][$key],
                        'size' => $_FILES['screenshots']['size'][$key]
                    ];
                    $allowedTypes = ['image/jpeg', 'image/png'];
                    $maxSize = 3 * 1024 * 1024; // 3MB
                    
                    if (in_array($file['type'], $allowedTypes) && $file['size'] <= $maxSize) {
                        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                        $fileName = uniqid('screenshot_', true) . '.' . $extension;
                        $targetPath = $uploadDir . $fileName;
                        
                        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                            $screenshotsPaths[] = '../backoffice/uploads/' . $fileName;
                        } else {
                            $error = "Error uploading screenshot.";
                        }
                    } else {
                        $error = "Unsupported screenshot format or file too large (max 3MB).";
                    }
                }
            }
        }

        $plateformes = !empty($_POST['plateformes']) ? explode(",", $_POST['plateformes']) : [];
        $tags        = !empty($_POST['tags']) ? explode(",", $_POST['tags']) : [];

        // Convert age_recommande from string (e.g., '16+') to integer
        $ageRecommande = null;
        if (!empty($_POST['age_recommande'])) {
            // Extract numeric value from strings like '16+', '7+', etc.
            $ageValue = preg_replace('/[^0-9]/', '', $_POST['age_recommande']);
            if (!empty($ageValue) && is_numeric($ageValue)) {
                $ageRecommande = (int)$ageValue;
            }
        }

        if (empty($error)) {
            $project = new Project(
                null,
                $_POST['nom'],
                $_POST['developpeur'],
                $_POST['date_creation'],
                $_POST['categorie'],
                $_POST['description'],
                $imagePath,
                $_POST['trailer'],
                1, // developpeur_id (default for user submissions)
                $ageRecommande,
                $_POST['lieu'] ?? null,
                $_POST['lien_telechargement'] ?? null,
                $plateformes,
                $tags,
                $screenshotsPaths
            );

            // Add project with status 'en_attente' (pending) for user submissions
            $projectC->addProject($project, 'en_attente');
            $success = true;
        }
    } else {
        $error = "Please fill in all required fields.";
    }
} else {
    $error = "Missing form data.";
}

// Redirect with message
if (isset($success) && $success) {
    header('Location: ../addgame.html?success=1');
    exit;
} else {
    header('Location: ../addgame.html?error=' . urlencode($error));
    exit;
}
?>

