<?php
require_once __DIR__ . '/../model/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: avis.php');
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message-contact'] ?? '');

// Validation serveur
$errors = [];
$name = substr($name, 0, 100);
$message = substr($message, 0, 2000);

if ($name === '') $errors[] = 'Nom requis.';
elseif (mb_strlen($name) < 2) $errors[] = 'Nom trop court.';

if ($email === '') $errors[] = 'Email requis.';
else {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email invalide.';
    $email = substr($email, 0, 200);
}

if ($message === '') $errors[] = 'Message requis.';
elseif (mb_strlen($message) < 5) $errors[] = 'Message trop court.';

if (!empty($errors)) {
    header('Location: avis.php?contact=err');
    exit;
}

$sql = "INSERT INTO contact (name, email, message) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    header('Location: avis.php?contact=err');
    exit;
}
$stmt->bind_param("sss", $name, $email, $message);

if ($stmt->execute()) {
    header('Location: avis.php?contact=ok');
} else {
    header('Location: avis.php?contact=err');
}
exit;
