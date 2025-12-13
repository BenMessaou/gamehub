<?php
include '../../../controller/ProjectController.php';

$projectC = new ProjectController();

if (isset($_POST["id"]) && !empty($_POST["id"])) {
    $projectC->approveProject($_POST["id"]);
    header('Location: projectlist.php');
    exit;
} else {
    echo "Error: missing project ID.";
}
?>

