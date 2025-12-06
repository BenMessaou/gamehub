<?php
// add.php - reçoit POST du formulaire et ajoute en BDD
require_once __DIR__ . '/../models/config.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: avis.php');
    exit;
}

// Récupère et nettoie les champs
$pseudo  = trim($_POST['pseudo'] ?? '');
$email   = trim($_POST['email'] ?? '');
$game    = trim($_POST['game'] ?? '');
$rating  = (int)($_POST['rating'] ?? 0);
$message = trim($_POST['message'] ?? '');

// Validation serveur renforcée
$errors = [];
$pseudo = substr($pseudo, 0, 50);
$email  = substr($email, 0, 255);
$game   = substr($game, 0, 150);
$message = substr($message, 0, 1000);

if ($pseudo === '') $errors[] = "Pseudo requis.";
elseif (mb_strlen($pseudo) < 2) $errors[] = "Pseudo trop court.";

if ($email === '') $errors[] = "Email requis.";
elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide.";

if ($game === '') $errors[] = "Nom du jeu requis.";

if ($rating < 1 || $rating > 5) $errors[] = "Note invalide.";

if ($message === '') $errors[] = "Message requis.";
elseif (mb_strlen($message) < 5) $errors[] = "Message trop court.";

if (!empty($errors)) {
    // redirige avec code d'erreur simple (on peut afficher plus tard depuis la session)
    header('Location: avis.php?add=err');
    exit;
}

// Prépare et exécute l'insertion avec status 'pending'
$sql = "INSERT INTO feedback (pseudo, email, game, rating, message, status) VALUES (?, ?, ?, ?, ?, 'pending')";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("sssis", $pseudo, $email, $game, $rating, $message);
    $stmt->execute();
    $stmt->close();
}

// Retour à la page des avis
header('Location: avis.php');
exit;
