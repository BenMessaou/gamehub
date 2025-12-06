<?php
// delete.php - suppression d'un avis par id
require_once __DIR__ . '/../models/config.php';


// récupération et validation de l'id
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: avis.php');
    exit;
}

// Suppression
$sql = "DELETE FROM feedback WHERE id = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

header('Location: avis.php');
exit;
