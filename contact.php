<?php
// contact.php
// traite le formulaire Contact et redirige vers avis.php avec ?contact=ok ou ?contact=err

ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'config.php'; // doit définir $conn (mysqli)

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: avis.php');
    exit;
}

// Récupère et nettoie
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$message = isset($_POST['message-contact']) ? trim($_POST['message-contact']) : '';

// Validation simple
$errors = [];
if ($name === '') $errors[] = 'Nom requis';
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email invalide';
if ($message === '') $errors[] = 'Message requis';

if (!empty($errors)) {
    // En cas d'erreur, on peut rediriger avec erreur simple (ou afficher)
    $msg = urlencode(implode(', ', $errors));
    header("Location: avis.php?contact=err&msg={$msg}");
    exit;
}

// Prépare l'insertion
$sql = "INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    header('Location: avis.php?contact=err&msg=' . urlencode('Erreur sql prepare'));
    exit;
}

$stmt->bind_param("sss", $name, $email, $message);
$ok = $stmt->execute();
$stmt->close();

if ($ok) {
    header('Location: avis.php?contact=ok');
    exit;
} else {
    header('Location: avis.php?contact=err&msg=' . urlencode('Impossible d\'enregistrer'));
    exit;
}
