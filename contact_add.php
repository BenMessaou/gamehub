<?php
// contact_add.php
// Traitement simple et sécurisé du formulaire Contact
ini_set('display_errors', 1);
error_reporting(E_ALL);

require 'config.php'; // utilise la connexion $conn comme dans add.php

// vérifier méthode
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: avis.php');
    exit;
}

// récupérer et nettoyer
$name    = isset($_POST['name']) ? trim($_POST['name']) : '';
$email   = isset($_POST['email']) ? trim($_POST['email']) : '';
$message = isset($_POST['message-contact']) ? trim($_POST['message-contact']) : '';

// validations simples
$errors = [];
if ($name === '') $errors[] = 'Nom requis.';
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email invalide.';
if ($message === '') $errors[] = 'Message requis.';

if (!empty($errors)) {
    // on retourne à la page avec erreur (affichage basique)
    $err = urlencode(implode(' | ', $errors));
    header("Location: avis.php?contact_err={$err}");
    exit;
}

// insertion en base
$sql = "INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    // debug
    $err = urlencode("Erreur prepare: " . $conn->error);
    header("Location: avis.php?contact_err={$err}");
    exit;
}
$stmt->bind_param("sss", $name, $email, $message);
if (!$stmt->execute()) {
    $err = urlencode("Erreur execute: " . $stmt->error);
    $stmt->close();
    header("Location: avis.php?contact_err={$err}");
    exit;
}
$stmt->close();

// succès : redirection (option : message de succès)
header("Location: avis.php?contact_ok=1");
exit;
