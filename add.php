<?php
// add.php - reçoit POST du formulaire et ajoute en BDD
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: avis.php');
    exit;
}

// Récupère et nettoie les champs
$pseudo  = trim($_POST['pseudo'] ?? '');
$game    = trim($_POST['game'] ?? '');
$rating  = (int)($_POST['rating'] ?? 0);
$message = trim($_POST['message'] ?? '');

// Validation basique
$errors = [];
if ($pseudo === '') $errors[] = "Pseudo requis.";
if ($game === '')   $errors[] = "Nom du jeu requis.";
if ($rating < 1 || $rating > 5) $errors[] = "Note invalide.";
if ($message === '') $errors[] = "Message requis.";

if (!empty($errors)) {
    // option simple : on redirige vers avis.php (tu peux aussi stocker en session)
    // pour debug, tu peux afficher les erreurs (décommente si besoin)
    // foreach ($errors as $err) echo htmlspecialchars($err) . "<br>";
    header('Location: avis.php');
    exit;
}

// Prépare et exécute l'insertion
$sql = "INSERT INTO feedback (pseudo, game, rating, message) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("ssis", $pseudo, $game, $rating, $message);
    $stmt->execute();
    $stmt->close();
}

// Retour à la page des avis
header('Location: avis.php');
exit;
