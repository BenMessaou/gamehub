<?php
// Fichier pour servir les fichiers audio de manière sécurisée

session_start();

// Récupérer le nom du fichier depuis l'URL
$audioFile = isset($_GET['file']) ? basename($_GET['file']) : '';

if (empty($audioFile)) {
    http_response_code(400);
    die('Fichier audio non spécifié');
}

// Chemin vers le fichier audio
$audioPath = __DIR__ . '/../../../uploads/voices/' . $audioFile;

// Vérifier que le fichier existe
if (!file_exists($audioPath)) {
    http_response_code(404);
    die('Fichier audio introuvable');
}

// Vérifier que c'est bien un fichier audio (sécurité)
$allowedExtensions = ['webm', 'mp3', 'wav', 'ogg', 'm4a', 'mp4'];
$fileExtension = strtolower(pathinfo($audioFile, PATHINFO_EXTENSION));

if (!in_array($fileExtension, $allowedExtensions)) {
    http_response_code(403);
    die('Type de fichier non autorisé');
}

// Déterminer le type MIME
$mimeTypes = [
    'webm' => 'audio/webm',
    'mp3' => 'audio/mpeg',
    'wav' => 'audio/wav',
    'ogg' => 'audio/ogg',
    'm4a' => 'audio/mp4',
    'mp4' => 'audio/mp4'
];

$mimeType = $mimeTypes[$fileExtension] ?? 'application/octet-stream';

// Envoyer les headers appropriés
header('Content-Type: ' . $mimeType);
header('Content-Length: ' . filesize($audioPath));
header('Accept-Ranges: bytes');
header('Cache-Control: public, max-age=3600');

// Lire et envoyer le fichier
readfile($audioPath);
exit;
?>

