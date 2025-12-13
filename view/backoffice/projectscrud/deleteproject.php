<?php
include '../../../controller/ProjectController.php';

$projectC = new ProjectController();

// Vérifier si l'ID existe
if (isset($_GET["id"]) && !empty($_GET["id"])) {

    $projectC->deleteProject($_GET["id"]);

    // Redirection vers la liste
    header('Location: projectlist.php');
    exit;

} else {
    echo "Error: missing project ID.";
}
?>
