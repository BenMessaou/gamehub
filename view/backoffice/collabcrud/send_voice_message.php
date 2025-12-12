<?php
session_start();

// Mode développeur : permettre l'accès même sans connexion
$isLoggedIn = isset($_SESSION['user_id']);
$userId = $isLoggedIn ? $_SESSION['user_id'] : 1; // ID par défaut pour le développeur

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Vérifier qu'un fichier audio a été envoyé
if (!isset($_FILES['audio']) || $_FILES['audio']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Aucun fichier audio reçu']);
    exit;
}

// Vérifier les paramètres requis
if (!isset($_POST['collab_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'collab_id is required']);
    exit;
}

$collab_id = intval($_POST['collab_id']);
$duration = isset($_POST['duration']) ? intval($_POST['duration']) : null;

// Charger controllers
require_once __DIR__ . "/../../../controller/controllercollab/CollabMessageController.php";
require_once __DIR__ . "/../../../controller/controllercollab/CollabMemberController.php";
require_once __DIR__ . "/../../../model/collab/CollabMessage.php";

$messageController = new CollabMessageController();
$memberController = new CollabMemberController();

// Vérifier que l'utilisateur est membre (sauf en mode développeur)
if ($isLoggedIn) {
    if (!$memberController->isMember($collab_id, $userId)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'You must be a member to send voice messages']);
        exit;
    }
}

// Créer le dossier d'upload s'il n'existe pas
$uploadDir = __DIR__ . '/../../../uploads/voices/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Générer un nom de fichier unique
$fileExtension = pathinfo($_FILES['audio']['name'], PATHINFO_EXTENSION);
if (empty($fileExtension)) {
    // Déterminer l'extension depuis le type MIME
    $mimeType = $_FILES['audio']['type'];
    if (strpos($mimeType, 'webm') !== false) {
        $fileExtension = 'webm';
    } elseif (strpos($mimeType, 'mp4') !== false) {
        $fileExtension = 'mp4';
    } elseif (strpos($mimeType, 'ogg') !== false) {
        $fileExtension = 'ogg';
    } else {
        $fileExtension = 'webm'; // Par défaut
    }
}

$fileName = 'voice_' . $collab_id . '_' . $userId . '_' . time() . '_' . uniqid() . '.' . $fileExtension;
$filePath = $uploadDir . $fileName;

// Déplacer le fichier uploadé
if (!move_uploaded_file($_FILES['audio']['tmp_name'], $filePath)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'upload du fichier']);
    exit;
}

// Chemin relatif pour la base de données
$relativePath = 'uploads/voices/' . $fileName;

// Créer le message avec le chemin audio
$message = new CollabMessage(null, $collab_id, $userId, '', null);
$message->setAudioPath($relativePath);
if ($duration !== null) {
    $message->setAudioDuration($duration);
}

// Insérer le message dans la base de données
try {
    // Utiliser une méthode spéciale pour insérer un message vocal
    // Ou modifier la méthode send() pour accepter audio_path et audio_duration
    
    // Pour l'instant, on va utiliser une requête SQL directe
    require_once __DIR__ . "/../../../config/config.php";
    $db = config::getConnexion();
    
    $sql = "INSERT INTO collab_messages (collab_id, user_id, message, audio_path, audio_duration, date_message) 
            VALUES (?, ?, ?, ?, ?, NOW())";
    
    $stmt = $db->prepare($sql);
    $success = $stmt->execute([
        $collab_id,
        $userId,
        '', // Message vide pour les messages vocaux
        $relativePath,
        $duration
    ]);
    
    if ($success) {
        echo json_encode([
            'success' => true,
            'message' => 'Message vocal envoyé avec succès',
            'audio_path' => $relativePath,
            'duration' => $duration
        ]);
    } else {
        // Supprimer le fichier si l'insertion a échoué
        @unlink($filePath);
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'enregistrement du message']);
    }
} catch (PDOException $e) {
    // Supprimer le fichier en cas d'erreur
    @unlink($filePath);
    error_log("Erreur PDO lors de l'envoi du message vocal: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur lors de l\'envoi du message vocal']);
}
?>

