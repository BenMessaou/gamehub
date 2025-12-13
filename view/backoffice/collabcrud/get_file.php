<?php
session_start();

// Mode développeur : permettre l'accès même sans connexion
$isLoggedIn = isset($_SESSION['user_id']);

// Vérifier qu'un fichier est demandé
if (!isset($_GET['file']) || empty($_GET['file'])) {
    http_response_code(400);
    die('Fichier non spécifié');
}

$fileName = basename($_GET['file']); // Sécurité : éviter les chemins relatifs

// Chemin du fichier
$filePath = __DIR__ . '/../../../uploads/messages/' . $fileName;

// Vérifier que le fichier existe
if (!file_exists($filePath)) {
    http_response_code(404);
    die('Fichier non trouvé');
}

// Vérifier que c'est bien un fichier (pas un dossier)
if (!is_file($filePath)) {
    http_response_code(403);
    die('Accès refusé');
}

// Déterminer le type MIME
$extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
$mimeTypes = [
    'pdf' => 'application/pdf',
    'doc' => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'xls' => 'application/vnd.ms-excel',
    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'txt' => 'text/plain',
    'zip' => 'application/zip',
    'rar' => 'application/x-rar-compressed',
    '7z' => 'application/x-7z-compressed',
    'json' => 'application/json',
    'csv' => 'text/csv',
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'gif' => 'image/gif',
    'webp' => 'image/webp',
    'svg' => 'image/svg+xml'
];

$mimeType = $mimeTypes[$extension] ?? 'application/octet-stream';

// Déterminer si le fichier doit être téléchargé ou affiché
$shouldDownload = in_array($extension, ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'zip', 'rar', '7z']);

// Envoyer les headers appropriés
header('Content-Type: ' . $mimeType);
header('Content-Length: ' . filesize($filePath));

if ($shouldDownload) {
    header('Content-Disposition: attachment; filename="' . basename($fileName) . '"');
} else {
    header('Content-Disposition: inline; filename="' . basename($fileName) . '"');
}

// Sécurité : désactiver la mise en cache pour les fichiers sensibles
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

// Lire et envoyer le fichier
readfile($filePath);
exit;
?>

