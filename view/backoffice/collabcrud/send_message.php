<?php
session_start();

// Mode développeur : permettre l'accès même sans connexion
$isLoggedIn = isset($_SESSION['user_id']);
$defaultUserId = 1; // ID par défaut pour le développeur

require_once __DIR__ . "/../../../controller/controllercollab/CollabMessageController.php";
require_once __DIR__ . "/../../../controller/controllercollab/CollabMemberController.php";
require_once __DIR__ . "/../../../controller/controllercollab/MessageModerationController.php";
require_once __DIR__ . "/../../../model/collab/CollabMessage.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $collab_id = isset($_POST['collab_id']) ? intval($_POST['collab_id']) : 0;
    $message = trim($_POST['message'] ?? '');

    if ($collab_id <= 0 || empty($message)) {
        $redirectTo = isset($_POST['redirect_to']) ? $_POST['redirect_to'] : 'view_collab';
        $redirectUrl = ($redirectTo === 'room_collab') ? 'room_collab.php' : 'view_collab.php';
        header("Location: " . $redirectUrl . "?id=" . $collab_id . "&error=message_invalid");
        exit;
    }

    // Utiliser l'ID de session s'il existe, sinon utiliser celui par défaut
    $user_id = $isLoggedIn ? $_SESSION['user_id'] : $defaultUserId;

    // Vérifier que l'utilisateur est membre (sauf en mode développeur)
    if ($isLoggedIn) {
        $memberController = new CollabMemberController();
        if (!$memberController->isMember($collab_id, $user_id)) {
            die("Erreur : vous devez être membre de cette collaboration pour envoyer des messages.");
        }
    }

    // ============================================================
    // GESTION DE L'UPLOAD DES FICHIERS
    // ============================================================
    $uploadedFiles = [];
    $filePaths = [];
    
    if (isset($_FILES['chatFileInput']) && !empty($_FILES['chatFileInput']['name'][0])) {
        // Créer le dossier d'upload s'il n'existe pas
        $uploadDir = __DIR__ . '/../../../uploads/messages/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Types de fichiers autorisés
        $allowedTypes = [
            // Images
            'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml',
            // Documents
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
            'text/plain', // .txt
            // Archives
            'application/zip',
            'application/x-rar-compressed',
            'application/x-7z-compressed',
            // Autres
            'application/json',
            'text/csv'
        ];
        
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'pdf', 'doc', 'docx', 
                             'xls', 'xlsx', 'txt', 'zip', 'rar', '7z', 'json', 'csv'];
        
        $maxFileSize = 10 * 1024 * 1024; // 10MB par fichier
        
        // Traiter chaque fichier
        $fileCount = count($_FILES['chatFileInput']['name']);
        for ($i = 0; $i < $fileCount; $i++) {
            if ($_FILES['chatFileInput']['error'][$i] === UPLOAD_ERR_OK) {
                $file = [
                    'name' => $_FILES['chatFileInput']['name'][$i],
                    'type' => $_FILES['chatFileInput']['type'][$i],
                    'tmp_name' => $_FILES['chatFileInput']['tmp_name'][$i],
                    'size' => $_FILES['chatFileInput']['size'][$i]
                ];
                
                // Vérifier la taille
                if ($file['size'] > $maxFileSize) {
                    continue; // Ignorer les fichiers trop volumineux
                }
                
                // Vérifier l'extension
                $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if (!in_array($extension, $allowedExtensions)) {
                    continue; // Ignorer les extensions non autorisées
                }
                
                // Vérifier le type MIME (optionnel, car peut être falsifié)
                if (!in_array($file['type'], $allowedTypes) && !empty($file['type'])) {
                    // Accepter quand même si l'extension est valide
                    // (certains navigateurs peuvent avoir des types MIME différents)
                }
                
                // Générer un nom de fichier unique
                $fileName = 'msg_' . $collab_id . '_' . $user_id . '_' . time() . '_' . uniqid() . '.' . $extension;
                $filePath = $uploadDir . $fileName;
                
                // Déplacer le fichier
                if (move_uploaded_file($file['tmp_name'], $filePath)) {
                    $relativePath = 'uploads/messages/' . $fileName;
                    $filePaths[] = $relativePath;
                    $uploadedFiles[] = $file['name'];
                }
            }
        }
        
        // Ajouter les informations des fichiers au message
        if (!empty($uploadedFiles)) {
            $fileInfo = "\n📎 Fichiers: " . implode(', ', $uploadedFiles);
            $message .= $fileInfo;
        }
    }

    // ============================================================
    // MODÉRATION DU MESSAGE - 2 NIVEAUX
    // ============================================================
    $moderationController = new MessageModerationController();
    $moderationResult = $moderationController->moderateMessage($message);
    
    // Enregistrer le log de modération
    $moderationController->logModeration($message, $moderationResult, $user_id, $collab_id);
    
    // Vérifier le résultat de la modération
    if ($moderationResult['blocked']) {
        // Supprimer les fichiers uploadés si le message est bloqué
        foreach ($filePaths as $filePath) {
            $fullPath = __DIR__ . '/../../../' . $filePath;
            if (file_exists($fullPath)) {
                @unlink($fullPath);
            }
        }
        
        // Message bloqué - rediriger avec erreur
        $redirectTo = isset($_POST['redirect_to']) ? $_POST['redirect_to'] : 'view_collab';
        $redirectUrl = ($redirectTo === 'room_collab') ? 'room_collab.php' : 'view_collab.php';
        
        // Encoder la raison pour l'URL
        $reason = urlencode($moderationResult['reason']);
        header("Location: " . $redirectUrl . "?id=" . $collab_id . "&error=message_blocked&reason=" . $reason . "&level=" . $moderationResult['level']);
        exit;
    }
    
    // Message approuvé - l'envoyer
    $msg = new CollabMessage(null, $collab_id, $user_id, $message, null);
    $controller = new CollabMessageController();
    $controller->send($msg);

    // Vérifier d'où vient la requête (room_collab ou view_collab)
    $redirectTo = isset($_POST['redirect_to']) ? $_POST['redirect_to'] : 'view_collab';
    
    if ($redirectTo === 'room_collab') {
        header("Location: room_collab.php?id=" . $collab_id . "&message_sent=1");
    } else {
        header("Location: view_collab.php?id=" . $collab_id . "&message_sent=1");
    }
    exit;
}
?>
