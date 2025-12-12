<?php
session_start();

// Mode développeur : permettre l'accès même sans connexion
$isLoggedIn = isset($_SESSION['user_id']);
$userId = $isLoggedIn ? $_SESSION['user_id'] : null;

// Charger controllers
require_once __DIR__ . "/../../../controller/controllercollab/CollabProjectController.php";
require_once __DIR__ . "/../../../controller/controllercollab/CollabMemberController.php";
require_once __DIR__ . "/../../../controller/controllercollab/CollabMessageController.php";
require_once __DIR__ . "/../../../config/config.php";

$projectController = new CollabProjectController();
$memberController = new CollabMemberController();
$messageController = new CollabMessageController();
$db = config::getConnexion();

// Fonction pour récupérer l'avatar d'un utilisateur
function getUserAvatar($db, $userId) {
    try {
        $sql = "SELECT avatar_data FROM user_avatars WHERE user_id = ? ORDER BY updated_at DESC LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result && $result['avatar_data']) {
            return json_decode($result['avatar_data'], true);
        }
        return null;
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération de l'avatar: " . $e->getMessage());
        return null;
    }
}

// Fonction pour récupérer l'image de profil d'un utilisateur
function getUserProfileImage($db, $userId) {
    try {
        // Vérifier si la colonne profile_image existe, sinon utiliser avatar_image
        try {
            $stmt = $db->query("SHOW COLUMNS FROM user_avatars LIKE 'profile_image'");
            $hasProfileImageColumn = $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            $hasProfileImageColumn = false;
        }
        
        $imageColumn = $hasProfileImageColumn ? 'profile_image' : 'avatar_image';
        $sql = "SELECT " . $imageColumn . " FROM user_avatars WHERE user_id = ? ORDER BY updated_at DESC LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result && $result[$imageColumn]) {
            return $result[$imageColumn];
        }
        return null;
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération de l'image de profil: " . $e->getMessage());
        return null;
    }
}

// Récupérer l'image de profil de l'utilisateur actuel
$currentUserProfileImage = getUserProfileImage($db, $currentUserId);

// Vérifier si ID est passé
if (!isset($_GET['id'])) {
    die("Missing project ID.");
}

$collab_id = $_GET['id'];

// Vérifier si un message a été envoyé avec succès
$showMessageSent = isset($_GET['message_sent']) && $_GET['message_sent'] == '1';

// Récupérer info du projet
$collab = $projectController->getById($collab_id);

if (!$collab) {
    die("Collaborative project not found.");
}

// Récupérer tous les membres du collab
$members = $memberController->getMembers($collab_id);

// Vérifier si le collab est complet
$isComplete = count($members) >= $collab['max_membres'];

// Récupérer les messages du chat
$messages = $messageController->getMessages($collab_id);

// Vérifier si l'utilisateur est membre (pour le chat)
$isMember = false;
$currentUserId = $isLoggedIn ? $userId : 1; // ID par défaut pour le développeur
foreach ($members as $m) {
    if ($m['user_id'] == $currentUserId) {
        $isMember = true;
        break;
    }
}
// En mode développeur, permettre l'accès au chat même sans être membre
$canViewChat = $isMember || !$isLoggedIn;

// Fonction pour traduire le rôle
function translateRole($role) {
    $translations = [
        'owner' => 'Propriétaire',
        'moderateur' => 'Modérateur',
        'membre' => 'Membre'
    ];
    return isset($translations[$role]) ? $translations[$role] : ucfirst($role);
}

// Récupérer le nom d'utilisateur pour l'assistant IA
$userName = 'Membre #' . $currentUserId;
if (isset($_SESSION['username'])) {
    $userName = $_SESSION['username'];
} elseif (isset($_SESSION['user_name'])) {
    $userName = $_SESSION['user_name'];
} else {
    // Essayer de récupérer depuis la base de données
    try {
        $sql = "SELECT username, nom, prenom FROM users WHERE id = ? LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([$currentUserId]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($userData) {
            if (!empty($userData['username'])) {
                $userName = $userData['username'];
            } elseif (!empty($userData['prenom'])) {
                $userName = $userData['prenom'];
            } elseif (!empty($userData['nom'])) {
                $userName = $userData['nom'];
            }
        }
    } catch (PDOException $e) {
        // Garder le nom par défaut
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Collab - <?php echo htmlspecialchars($collab['titre']); ?> - GameHub Pro</title>
    <!-- Styles du template admin -->
    <link rel="stylesheet" href="admin_style.css">
    <link rel="stylesheet" href="emoji_picker.css">
    <style>
        /* Styles de base sans template */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: #1a1a2e;
            color: #fff;
            padding: 20px;
        }
        
        .room-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .room-header {
            background: rgba(0, 0, 0, 0.5);
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(0, 255, 136, 0.3);
        }
        
        .room-header h1 {
            color: #00ff88;
            margin: 0 0 1rem 0;
            font-size: 2rem;
            font-weight: 700;
        }
        
        .room-header .collab-info {
            color: #ccc;
            margin: 0.5rem 0;
        }
        
        .room-header .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 15px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-top: 1rem;
        }
        
        .status-badge.complete {
            background: rgba(0, 255, 234, 0.2);
            color: #00ffea;
            border: 1px solid rgba(0, 255, 234, 0.5);
        }
        .members-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
        .member-card {
            background: rgba(0, 0, 0, 0.5);
            border-radius: 10px;
            padding: 2rem;
            border: 1px solid rgba(0, 255, 136, 0.3);
        }
        
        .member-card.owner {
            border-color: rgba(255, 215, 0, 0.5);
        }
        .member-avatar {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background: linear-gradient(135deg, #00ff88, #00ffea);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            font-weight: bold;
            color: #000;
            margin: 0 auto 1.5rem;
            border: 4px solid rgba(0, 255, 136, 0.6);
            box-shadow: 
                0 0 20px rgba(0, 255, 136, 0.5),
                inset 0 0 20px rgba(0, 255, 234, 0.3);
            transition: all 0.4s ease;
            position: relative;
            animation: float 3s ease-in-out infinite;
        }
        
        .member-card:hover .member-avatar {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 
                0 0 30px rgba(0, 255, 136, 0.8),
                inset 0 0 30px rgba(0, 255, 234, 0.5);
        }
        .member-card.owner .member-avatar {
            background: linear-gradient(135deg, #ffd700, #ffed4e);
            border-color: rgba(255, 215, 0, 0.5);
        }
        .member-name {
            text-align: center;
            color: #00ff88;
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .member-card.owner .member-name {
            color: #ffd700;
        }
        .member-role {
            text-align: center;
            color: #00ffea;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            padding: 5px 10px;
            background: rgba(0, 255, 234, 0.1);
            border-radius: 10px;
            display: inline-block;
            width: 100%;
        }
        .member-card.owner .member-role {
            color: #ffd700;
            background: rgba(255, 215, 0, 0.1);
        }
        .member-id {
            text-align: center;
            color: #888;
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 2rem;
            color: #00ff88;
            text-decoration: none;
            padding: 10px 20px;
            background: rgba(0, 255, 136, 0.1);
            border: 1px solid rgba(0, 255, 136, 0.3);
            border-radius: 5px;
        }
        
        .members-section {
            background: rgba(0, 0, 0, 0.5);
            border-radius: 10px;
            padding: 2rem;
            border: 1px solid rgba(0, 255, 136, 0.3);
            margin-bottom: 2rem;
        }
        
        .members-section h2 {
            color: #00ff88;
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 2rem;
            font-weight: 700;
        }
        
        .members-count {
            text-align: center;
            color: #00ffea;
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }
        
        .empty-state {
            text-align: center;
            color: #888;
            padding: 3rem;
            font-size: 1.1rem;
        }
        
        /* ============================================================
           CHAT STYLES
           ============================================================ */
        .chat-section {
            position: relative;
            overflow: hidden;
            margin-top: 2rem;
            border-radius: 14px;
            border: 1px solid rgba(0, 255, 136, 0.5);
            background: radial-gradient(circle at 20% 20%, rgba(0, 255, 136, 0.15), transparent 35%),
                        radial-gradient(circle at 80% 10%, rgba(0, 255, 234, 0.12), transparent 30%),
                        linear-gradient(135deg, #050910, #0a1423 45%, #0b1f2f 100%);
            box-shadow:
                0 20px 60px rgba(0, 0, 0, 0.55),
                0 0 30px rgba(0, 255, 136, 0.25),
                inset 0 0 20px rgba(0, 255, 136, 0.12);
            padding: 2rem;
        }
        
        .chat-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(120deg, rgba(0, 255, 136, 0.12), rgba(0, 255, 234, 0.05), rgba(255, 0, 199, 0.05));
            mix-blend-mode: screen;
            pointer-events: none;
        }
        
        .cyber-chat-header {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.25rem;
            border-radius: 12px;
            background: linear-gradient(135deg, rgba(0, 255, 136, 0.18), rgba(0, 255, 234, 0.12));
            border: 1px solid rgba(0, 255, 136, 0.5);
            box-shadow: 0 0 25px rgba(0, 255, 136, 0.25);
            margin-bottom: 1rem;
        }
        
        .cyber-chat-title {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .cyber-chat-title h2 {
            margin: 0;
            color: #7cf7c5;
            font-size: 1.6rem;
            letter-spacing: 0.5px;
        }
        
        .cyber-chat-title p {
            margin: 0;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.95rem;
        }
        
        .chat-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            font-size: 1.4rem;
            background: radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.25), rgba(0, 255, 136, 0.35));
            color: #0b0f1c;
            box-shadow: 0 0 20px rgba(0, 255, 136, 0.5);
        }
        
        .cyber-badge {
            padding: 0.55rem 1.1rem;
            background: rgba(255, 51, 92, 0.15);
            color: #ff7ba5;
            border-radius: 10px;
            border: 1px solid rgba(255, 51, 92, 0.5);
            text-decoration: none;
            font-weight: 700;
            letter-spacing: 0.3px;
            transition: all 0.25s ease;
        }
        
        .cyber-badge:hover {
            background: rgba(255, 51, 92, 0.3);
            box-shadow: 0 0 18px rgba(255, 51, 92, 0.4);
        }
        
        .chat-info {
            position: relative;
            z-index: 1;
            margin-bottom: 1rem;
            padding: 0.8rem 1rem;
            background: rgba(0, 255, 136, 0.12);
            border: 1px solid rgba(0, 255, 136, 0.4);
            border-radius: 10px;
            color: #8fffd0;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 0 15px rgba(0, 255, 136, 0.25);
        }
        
        .chat-container {
            display: flex;
            flex-direction: column;
            height: 500px;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 10px;
            border: 1px solid rgba(0, 255, 136, 0.2);
            overflow: hidden;
        }
        
        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .chat-messages::-webkit-scrollbar {
            width: 8px;
        }
        
        .chat-messages::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }
        
        .chat-messages::-webkit-scrollbar-thumb {
            background: rgba(0, 255, 136, 0.3);
            border-radius: 10px;
        }
        
        .chat-messages::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 255, 136, 0.5);
        }
        
        .message-item {
            display: flex;
            flex-direction: column;
            padding: 0.4rem 0.7rem;
            background: linear-gradient(135deg, rgba(0, 255, 136, 0.15), rgba(0, 255, 136, 0.05));
            border-radius: 10px;
            border: 1.5px solid rgba(0, 255, 136, 0.3);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: visible;
            min-height: auto;
            width: fit-content;
            max-width: 75%;
            min-width: 150px;
            align-self: flex-start;
            animation: slideInUp 0.4s ease-out backwards;
            box-shadow: 
                0 3px 10px rgba(0, 255, 136, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }
        
        .message-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(0, 255, 136, 0.2), transparent);
            transition: left 0.5s ease;
        }
        
        .message-item:hover {
            background: linear-gradient(135deg, rgba(0, 255, 136, 0.25), rgba(0, 255, 136, 0.15));
            border-color: rgba(0, 255, 136, 0.5);
            transform: translateX(5px);
            box-shadow: 
                0 6px 20px rgba(0, 255, 136, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.15);
        }
        
        .message-item:hover::before {
            left: 100%;
        }
        
        .message-item.own-message {
            background: linear-gradient(135deg, rgba(0, 255, 234, 0.2), rgba(0, 255, 234, 0.1));
            border-color: rgba(0, 255, 234, 0.4);
            align-self: flex-end;
            max-width: 75%;
            min-width: 150px;
        }
        
        .message-item.own-message:hover {
            background: linear-gradient(135deg, rgba(0, 255, 234, 0.3), rgba(0, 255, 234, 0.2));
            border-color: rgba(0, 255, 234, 0.6);
            transform: translateX(-5px);
        }
        
        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.25rem;
        }
        
        .message-user {
            font-weight: 600;
            color: #00ff88;
            font-size: 0.9rem;
        }
        
        .message-item.own-message .message-user {
            color: #00ffea;
        }
        
        .message-date {
            font-size: 0.75rem;
            color: #888;
        }
        
        .message-text {
            color: #fff;
            word-wrap: break-word;
            word-break: break-word;
            overflow-wrap: break-word;
            line-height: 1.4;
            white-space: pre-wrap;
            max-width: 100%;
            min-height: auto;
            padding: 0;
            margin: 0;
        }
        
        .message-menu {
            position: relative;
            display: inline-block;
        }
        
        .message-menu-btn {
            background: transparent;
            border: none;
            color: #888;
            cursor: pointer;
            font-size: 1.2rem;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            transition: all 0.2s ease;
            opacity: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .message-item:hover .message-menu-btn,
        .message-item.own-message .message-menu-btn {
            opacity: 1;
        }
        
        .message-menu-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }
        
        .message-menu-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: rgba(0, 0, 0, 0.95);
            border: 1px solid rgba(0, 255, 136, 0.3);
            border-radius: 8px;
            padding: 0.5rem 0;
            min-width: 150px;
            z-index: 1000;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.5);
            display: none;
            margin-top: 0.25rem;
        }
        
        .message-menu-dropdown.show {
            display: block;
        }
        
        .message-menu-item {
            padding: 0.5rem 1rem;
            color: #fff;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
        }
        
        .message-menu-item:hover {
            background: rgba(0, 255, 136, 0.2);
            color: #00ff88;
        }
        
        .message-menu-item.delete {
            color: #ff335c;
        }
        
        .message-menu-item.delete:hover {
            background: rgba(255, 51, 92, 0.2);
            color: #ff335c;
        }
        
        .message-edit-form {
            display: none;
            margin-top: 0.5rem;
        }
        
        .message-edit-form.active {
            display: block;
        }
        
        .message-edit-input {
            width: 100%;
            background: rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(0, 255, 136, 0.3);
            border-radius: 6px;
            padding: 0.5rem;
            color: #fff;
            font-size: 0.9rem;
            resize: vertical;
            min-height: 60px;
        }
        
        .message-edit-input:focus {
            outline: none;
            border-color: #00ff88;
        }
        
        .message-edit-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }
        
        .message-edit-btn {
            padding: 0.4rem 1rem;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        
        .message-edit-save {
            background: rgba(0, 255, 136, 0.2);
            color: #00ff88;
            border: 1px solid rgba(0, 255, 136, 0.5);
        }
        
        .message-edit-save:hover {
            background: rgba(0, 255, 136, 0.4);
        }
        
        .message-edit-cancel {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .message-edit-cancel:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        
        .voice-recorder-container {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
        }
        
        .voice-record-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(255, 51, 92, 0.2), rgba(255, 51, 92, 0.4));
            border: 2px solid rgba(255, 51, 92, 0.5);
            color: #ff335c;
            font-size: 1.5rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            position: relative;
            flex-shrink: 0;
        }
        
        .voice-record-btn:hover {
            background: linear-gradient(135deg, rgba(255, 51, 92, 0.4), rgba(255, 51, 92, 0.6));
            border-color: #ff335c;
            transform: scale(1.05);
        }
        
        .voice-record-btn.recording {
            background: linear-gradient(135deg, rgba(255, 51, 92, 0.6), rgba(255, 51, 92, 0.8));
            border-color: #ff335c;
            animation: pulseRecording 1.5s infinite;
            box-shadow: 0 0 20px rgba(255, 51, 92, 0.6);
        }
        
        @keyframes pulseRecording {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 0 20px rgba(255, 51, 92, 0.6);
            }
            50% {
                transform: scale(1.1);
                box-shadow: 0 0 30px rgba(255, 51, 92, 0.8);
            }
        }
        
        .voice-record-status {
            color: #ff335c;
            font-size: 0.85rem;
            font-weight: 600;
            display: none;
            align-items: center;
            gap: 0.5rem;
        }
        
        .voice-record-status.active {
            display: flex;
        }
        
        .voice-record-timer {
            font-family: 'Courier New', monospace;
            font-weight: bold;
        }
        
        .voice-record-waves {
            display: flex;
            gap: 2px;
            align-items: center;
        }
        
        .voice-wave {
            width: 3px;
            height: 15px;
            background: #ff335c;
            border-radius: 2px;
            animation: waveAnimation 1s infinite ease-in-out;
        }
        
        .voice-wave:nth-child(1) { animation-delay: 0s; }
        .voice-wave:nth-child(2) { animation-delay: 0.1s; }
        .voice-wave:nth-child(3) { animation-delay: 0.2s; }
        .voice-wave:nth-child(4) { animation-delay: 0.3s; }
        
        @keyframes waveAnimation {
            0%, 100% { height: 10px; }
            50% { height: 20px; }
        }
        
        .message-audio {
            margin-top: 0.5rem;
            padding: 0.75rem;
            background: rgba(0, 255, 136, 0.1);
            border: 1px solid rgba(0, 255, 136, 0.3);
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .message-audio-icon {
            font-size: 1.5rem;
            color: #00ff88;
            flex-shrink: 0;
        }
        
        .message-audio-player {
            flex: 1;
        }
        
        .message-audio-player audio {
            width: 100%;
            outline: none;
        }
        
        .message-audio-player audio::-webkit-media-controls-panel {
            background-color: rgba(0, 0, 0, 0.5);
        }
        
        .message-audio-duration {
            color: #888;
            font-size: 0.75rem;
            flex-shrink: 0;
        }
        
        .chat-form-container {
            padding: 1rem;
            background: rgba(0, 0, 0, 0.3);
            border-top: 1px solid rgba(0, 255, 136, 0.2);
        }
        
        .chat-form {
            display: flex;
            gap: 0.5rem;
            align-items: flex-end;
        }
        
        .chat-attach-btn {
            width: 50px;
            height: 50px;
            border-radius: 15px;
            background: linear-gradient(135deg, rgba(0, 255, 136, 0.2), rgba(0, 255, 234, 0.2));
            border: 2px solid rgba(0, 255, 136, 0.4);
            color: #00ff88;
            font-size: 1.5rem;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            flex-shrink: 0;
            position: relative;
            overflow: hidden;
            box-shadow: 
                0 4px 15px rgba(0, 255, 136, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }
        
        .chat-attach-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(0, 255, 136, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.4s, height 0.4s;
        }
        
        .chat-attach-btn:hover {
            background: linear-gradient(135deg, rgba(0, 255, 136, 0.4), rgba(0, 255, 234, 0.4));
            border-color: #00ff88;
            transform: scale(1.1) rotate(90deg);
            box-shadow: 
                0 6px 20px rgba(0, 255, 136, 0.5),
                0 0 30px rgba(0, 255, 136, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }
        
        .chat-attach-btn:hover::before {
            width: 200px;
            height: 200px;
        }
        
        .chat-attach-btn:active {
            transform: scale(1.05) rotate(90deg);
        }
        
        .chat-file-input {
            display: none;
        }
        
        .chat-input {
            flex: 1;
            padding: 1rem 1.25rem;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.6), rgba(15, 30, 50, 0.6));
            border: 2px solid rgba(0, 255, 136, 0.3);
            border-radius: 15px;
            color: #fff;
            font-family: inherit;
            font-size: 0.95rem;
            resize: none;
            min-height: 60px;
            max-height: 200px;
            transition: all 0.3s ease;
            position: relative;
            z-index: 1;
        }
        
        .chat-input:focus {
            outline: none;
            border-color: #00ff88;
            box-shadow: 
                0 0 20px rgba(0, 255, 136, 0.4),
                inset 0 0 20px rgba(0, 255, 136, 0.1);
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.8), rgba(15, 30, 50, 0.8));
        }
        
        .chat-input::placeholder {
            color: #888;
        }
        
        .chat-send-btn {
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #00ff88, #00ffea);
            color: #000;
            border: none;
            border-radius: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            font-size: 1rem;
            white-space: nowrap;
            position: relative;
            overflow: hidden;
            box-shadow: 
                0 4px 15px rgba(0, 255, 136, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }
        
        .chat-send-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .chat-send-btn:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 
                0 8px 25px rgba(0, 255, 136, 0.6),
                0 0 40px rgba(0, 255, 136, 0.4),
                inset 0 1px 0 rgba(255, 255, 255, 0.3);
        }
        
        .chat-send-btn:hover::before {
            width: 300px;
            height: 300px;
        }
        
        .chat-send-btn:active {
            transform: translateY(-1px) scale(1.02);
        }
        
        .no-messages {
            text-align: center;
            color: #888;
            padding: 3rem;
            font-style: italic;
        }
        
        .chat-info {
            text-align: center;
            color: #00ffea;
            font-size: 0.85rem;
            margin-bottom: 1rem;
            padding: 0.5rem;
            background: rgba(0, 255, 234, 0.1);
            border-radius: 8px;
        }
        
        
        .profile-avatar-container {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-radius: 50%;
            position: relative;
            clip-path: circle(50% at 50% 50%);
        }
        
        .profile-avatar-container .avatar-cartoon {
            transform: scale(0.5);
            transform-origin: center top;
            position: absolute;
            top: -20%;
            left: 50%;
            margin-left: -50%;
        }
        
        /* Masquer uniquement les jambes et chaussures dans le bouton */
        .profile-avatar-container .avatar-cartoon .avatar-legs,
        .profile-avatar-container .avatar-cartoon .avatar-shoes {
            display: none !important;
        }
        
        /* Afficher la tête, le torse et les bras */
        .profile-avatar-container .avatar-cartoon .avatar-head,
        .profile-avatar-container .avatar-cartoon .avatar-torso,
        .profile-avatar-container .avatar-cartoon .avatar-arms {
            display: block !important;
        }
        
        .profile-avatar-placeholder {
            font-size: 1.5rem;
            color: #00ff88;
        }
        
        /* ============================================
           ASSISTANT IA - Animated Avatar
           ============================================ */
        
        #assistant-container {
            position: fixed;
            bottom: -300px;
            right: 50px;
            width: 200px;
            height: 260px;
            opacity: 0;
            transition: 0.7s ease;
            z-index: 10000;
            pointer-events: none;
        }
        
        /* Animation d'entrée */
        #assistant-container.show {
            bottom: 30px;
            opacity: 1;
            pointer-events: auto;
        }
        
        /* Avatar */
        #assistant-avatar {
            position: relative;
            width: 140px;
            height: 180px;
            margin: auto;
        }
        
        .assistant-head {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #4a5568, #2d3748);
            border-radius: 20px;
            margin: auto;
            border: 4px solid #1a1a1a;
            position: relative;
            box-shadow: 
                0 5px 20px rgba(0, 0, 0, 0.3),
                inset 0 -10px 30px rgba(0, 0, 0, 0.2),
                inset 0 2px 10px rgba(255, 255, 255, 0.1);
        }
        
        /* Antennes robotiques */
        .assistant-head::before {
            content: '';
            position: absolute;
            top: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 8px;
            height: 20px;
            background: linear-gradient(180deg, #00ffc8, #00a085);
            border-radius: 4px 4px 0 0;
            box-shadow: 0 0 10px rgba(0, 255, 200, 0.5);
        }
        
        /* Écran robotique */
        .assistant-head::after {
            content: '';
            position: absolute;
            top: 15px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 60px;
            background: linear-gradient(135deg, #1a2332, #0f1419);
            border: 3px solid #00ffc8;
            border-radius: 10px;
            box-shadow: 
                inset 0 0 20px rgba(0, 255, 200, 0.3),
                0 0 15px rgba(0, 255, 200, 0.4);
        }
        
        /* Yeux robotiques - LED lumineuses bleues */
        .assistant-eye {
            width: 24px;
            height: 24px;
            background: radial-gradient(circle, #4a9eff 0%, #2563eb 40%, #1e40af 80%, #1e3a8a 100%);
            border-radius: 50%;
            position: absolute;
            top: 32px;
            border: 3px solid #1a1a1a;
            box-shadow: 
                0 0 20px rgba(74, 158, 255, 0.9),
                0 0 40px rgba(74, 158, 255, 0.6),
                0 0 60px rgba(74, 158, 255, 0.3),
                inset 0 0 15px rgba(255, 255, 255, 0.4),
                inset 0 2px 5px rgba(255, 255, 255, 0.6);
            animation: eyePulse 2s ease-in-out infinite;
            z-index: 12;
        }
        
        /* Reflet blanc dans les yeux */
        .assistant-eye::before {
            content: '';
            position: absolute;
            width: 8px;
            height: 8px;
            background: radial-gradient(circle, #ffffff 0%, rgba(255, 255, 255, 0.8) 50%, transparent 100%);
            border-radius: 50%;
            top: 4px;
            left: 5px;
            box-shadow: 0 0 8px rgba(255, 255, 255, 0.9);
        }
        
        /* Pupille centrale */
        .assistant-eye::after {
            content: '';
            position: absolute;
            width: 10px;
            height: 10px;
            background: radial-gradient(circle, #1e3a8a 0%, #000000 100%);
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.8);
        }
        
        @keyframes eyePulse {
            0%, 100% { 
                box-shadow: 
                    0 0 20px rgba(74, 158, 255, 0.9),
                    0 0 40px rgba(74, 158, 255, 0.6),
                    0 0 60px rgba(74, 158, 255, 0.3),
                    inset 0 0 15px rgba(255, 255, 255, 0.4),
                    inset 0 2px 5px rgba(255, 255, 255, 0.6);
                transform: scale(1);
            }
            50% { 
                box-shadow: 
                    0 0 30px rgba(74, 158, 255, 1),
                    0 0 60px rgba(74, 158, 255, 0.8),
                    0 0 90px rgba(74, 158, 255, 0.5),
                    inset 0 0 20px rgba(255, 255, 255, 0.6),
                    inset 0 2px 8px rgba(255, 255, 255, 0.8);
                transform: scale(1.05);
            }
        }
        
        /* Animation de clignement */
        .assistant-eye.blink {
            animation: eyeBlink 0.3s ease-in-out;
        }
        
        @keyframes eyeBlink {
            0%, 100% { 
                height: 24px;
                transform: scaleY(1);
            }
            50% { 
                height: 2px;
                transform: scaleY(0.1);
            }
        }
        
        .eye-left { 
            left: 22px; 
        }
        
        .eye-right { 
            right: 22px; 
        }
        
        /* Bouche robotique - grille de haut-parleur */
        .assistant-mouth {
            width: 60px;
            height: 20px;
            background: linear-gradient(180deg, #1a1a1a 0%, #0a0a0a 100%);
            border: 2px solid #00ffc8;
            border-radius: 5px;
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            box-shadow: 
                inset 0 0 10px rgba(0, 0, 0, 0.5),
                0 0 10px rgba(0, 255, 200, 0.3);
        }
        
        /* Grille de haut-parleur */
        .assistant-mouth::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 50px;
            height: 12px;
            background-image: 
                repeating-linear-gradient(0deg, transparent, transparent 2px, #00ffc8 2px, #00ffc8 3px),
                repeating-linear-gradient(90deg, transparent, transparent 2px, #00ffc8 2px, #00ffc8 3px);
            background-size: 8px 8px;
            opacity: 0.6;
        }
        
        .assistant-mouth::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 40px;
            height: 8px;
            background: radial-gradient(ellipse, rgba(0, 255, 200, 0.3) 0%, transparent 70%);
        }
        
        /* Bras robotique qui fait coucou */
        .assistant-arm {
            width: 35px;
            height: 80px;
            background: linear-gradient(135deg, #4a5568, #2d3748);
            border-radius: 15px;
            border: 3px solid #1a1a1a;
            position: absolute;
            right: -10px;
            top: 80px;
            transform-origin: top center;
            box-shadow: 
                0 3px 10px rgba(0, 0, 0, 0.3),
                inset 0 -3px 8px rgba(0, 0, 0, 0.2),
                inset 0 2px 5px rgba(255, 255, 255, 0.1);
        }
        
        /* Jointure robotique */
        .assistant-arm::before {
            content: '';
            position: absolute;
            top: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 40px;
            height: 12px;
            background: linear-gradient(135deg, #2d3748, #1a1a1a);
            border: 2px solid #00ffc8;
            border-radius: 6px;
            box-shadow: 0 0 8px rgba(0, 255, 200, 0.4);
        }
        
        /* Main robotique */
        .assistant-arm::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 28px;
            height: 20px;
            background: linear-gradient(135deg, #4a5568, #2d3748);
            border: 2px solid #1a1a1a;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        }
        
        /* Animation coucou */
        .wave {
            animation: waving 1s ease-in-out 2;
        }
        
        @keyframes waving {
            0% { transform: rotate(0deg); }
            25% { transform: rotate(25deg); }
            50% { transform: rotate(0deg); }
            75% { transform: rotate(25deg); }
            100% { transform: rotate(0deg); }
        }
        
        /* Bulle de texte */
        #assistant-bubble {
            background: linear-gradient(135deg, #0f1b2e, #1a1a2e);
            color: #00ffc8;
            border: 2px solid #00ffc8;
            padding: 12px 18px;
            border-radius: 15px;
            text-align: center;
            margin-top: 10px;
            opacity: 0;
            transform: translateY(10px);
            transition: 0.6s ease;
            font-size: 0.95rem;
            font-weight: 600;
            box-shadow: 
                0 4px 15px rgba(0, 255, 200, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            position: relative;
        }
        
        #assistant-bubble::before {
            content: '';
            position: absolute;
            top: -8px;
            right: 30px;
            width: 0;
            height: 0;
            border-left: 8px solid transparent;
            border-right: 8px solid transparent;
            border-bottom: 8px solid #00ffc8;
        }
        
        #assistant-container.show #assistant-bubble {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Profile Modal */
        /* Bouton profil dans le header */
        .profile-header-btn {
            background: linear-gradient(145deg, #021015, #041f24);
            border: 2px solid rgba(0, 255, 136, 0.6);
            border-radius: 50%;
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 0 15px rgba(0, 255, 136, 0.25);
            color: #fff;
            padding: 0;
        }
        
        .profile-header-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 0 25px rgba(0, 255, 136, 0.5);
            border-color: rgba(0, 255, 136, 0.9);
            background: linear-gradient(145deg, #041f24, #062f3a);
        }
        
        .profile-header-btn:active {
            transform: scale(0.95);
        }
        
        .profile-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            z-index: 10000;
            display: none;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease-out;
        }
        
        .profile-modal-overlay.show {
            display: flex;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .profile-modal {
            background: linear-gradient(135deg, #1a1a2e, #0f0f1e);
            border-radius: 20px;
            padding: 2rem;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            border: 2px solid rgba(0, 255, 136, 0.5);
            box-shadow: 0 0 40px rgba(0, 255, 136, 0.3);
            position: relative;
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: scale(0.9) translateY(-20px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }
        
        .profile-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid rgba(0, 255, 136, 0.3);
        }
        
        .profile-modal-title {
            color: #00ff88;
            font-size: 1.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .profile-modal-close {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 51, 92, 0.2);
            border: 2px solid rgba(255, 51, 92, 0.5);
            color: #ff335c;
            font-size: 1.5rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .profile-modal-close:hover {
            background: rgba(255, 51, 92, 0.4);
            transform: rotate(90deg) scale(1.1);
        }
        
        .profile-avatar-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .profile-avatar-large {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 3px solid #00ff88;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.6);
            margin-bottom: 1rem;
            box-shadow: 0 0 20px rgba(0, 255, 136, 0.6);
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .profile-avatar-large:hover {
            border-color: #00ff88;
            box-shadow: 0 0 30px rgba(0, 255, 136, 0.8);
            transform: scale(1.05);
        }
        
        .profile-avatar-large label {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            cursor: pointer;
        }
        
        .profile-avatar-large img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
        
        .profile-avatar-large .avatar-cartoon,
        .profile-avatar-large .avatar-cartoon-container {
            transform: scale(0.75);
            transform-origin: center center;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.75);
            transition: transform 0.3s ease;
        }
        
        /* Masquer uniquement les jambes et chaussures dans le modal */
        .profile-avatar-large .avatar-cartoon .avatar-legs,
        .profile-avatar-large .avatar-cartoon .avatar-shoes,
        .profile-avatar-large .avatar-cartoon-container .avatar-legs,
        .profile-avatar-large .avatar-cartoon-container .avatar-shoes {
            display: none !important;
        }
        
        /* Afficher la tête, le torse et les bras */
        .profile-avatar-large .avatar-cartoon .avatar-head,
        .profile-avatar-large .avatar-cartoon .avatar-torso,
        .profile-avatar-large .avatar-cartoon .avatar-arms,
        .profile-avatar-large .avatar-cartoon-container .avatar-head,
        .profile-avatar-large .avatar-cartoon-container .avatar-torso,
        .profile-avatar-large .avatar-cartoon-container .avatar-arms {
            display: block !important;
        }
        
        /* Avatar Crop Editor */
        .avatar-crop-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            backdrop-filter: blur(10px);
            z-index: 10001;
            display: none;
            align-items: center;
            justify-content: center;
        }
        
        .avatar-crop-overlay.show {
            display: flex;
        }
        
        .avatar-crop-container {
            background: linear-gradient(135deg, #1a1a2e, #0f0f1e);
            border-radius: 20px;
            padding: 2rem;
            max-width: 700px;
            width: 90%;
            border: 2px solid rgba(0, 255, 136, 0.5);
            box-shadow: 0 0 40px rgba(0, 255, 136, 0.3);
            position: relative;
        }
        
        .avatar-crop-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid rgba(0, 255, 136, 0.3);
        }
        
        .avatar-crop-title {
            color: #00ff88;
            font-size: 1.5rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        
        .avatar-crop-close {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: rgba(255, 51, 92, 0.2);
            border: 2px solid rgba(255, 51, 92, 0.5);
            color: #ff335c;
            font-size: 1.2rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .avatar-crop-close:hover {
            background: rgba(255, 51, 92, 0.4);
            transform: rotate(90deg);
        }
        
        .avatar-crop-preview {
            width: 300px;
            height: 300px;
            border-radius: 50%;
            border: 4px solid rgba(0, 255, 136, 0.5);
            overflow: hidden;
            margin: 0 auto 2rem;
            position: relative;
            background: rgba(0, 0, 0, 0.3);
            box-shadow: 0 0 30px rgba(0, 255, 136, 0.4);
            clip-path: circle(50% at 50% 50%);
        }
        
        .avatar-crop-preview .avatar-cartoon {
            position: absolute;
            top: -15%;
            left: 50%;
            margin-left: -50%;
            transform-origin: center top;
            transition: transform 0.1s ease-out;
        }
        
        /* Masquer uniquement les jambes et chaussures dans le preview */
        .avatar-crop-preview .avatar-cartoon .avatar-legs,
        .avatar-crop-preview .avatar-cartoon .avatar-shoes {
            display: none !important;
        }
        
        .avatar-crop-controls {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .crop-control-group {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .crop-control-label {
            color: #00ff88;
            font-weight: 600;
            min-width: 100px;
            font-size: 0.9rem;
        }
        
        .crop-control-slider {
            flex: 1;
            height: 8px;
            border-radius: 5px;
            background: rgba(0, 255, 136, 0.2);
            outline: none;
            -webkit-appearance: none;
        }
        
        .crop-control-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #00ff88;
            cursor: pointer;
            box-shadow: 0 0 10px rgba(0, 255, 136, 0.5);
        }
        
        .crop-control-slider::-moz-range-thumb {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #00ff88;
            cursor: pointer;
            border: none;
            box-shadow: 0 0 10px rgba(0, 255, 136, 0.5);
        }
        
        .crop-control-value {
            color: #fff;
            font-weight: 600;
            min-width: 50px;
            text-align: right;
        }
        
        .avatar-crop-actions {
            display: flex;
            gap: 1rem;
        }
        
        .crop-action-btn {
            flex: 1;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .crop-action-btn.save {
            background: linear-gradient(135deg, #00ff88, #00ffea);
            color: #000;
        }
        
        .crop-action-btn.cancel {
            background: rgba(255, 51, 92, 0.2);
            color: #ff335c;
            border: 2px solid rgba(255, 51, 92, 0.5);
        }
        
        .crop-action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 255, 136, 0.5);
        }
        
        .profile-info-section {
            color: #e0e0e0;
        }
        
        .profile-info-item {
            margin-bottom: 1rem;
            padding: 0.75rem;
            background: rgba(0, 255, 136, 0.1);
            border-radius: 10px;
            border-left: 4px solid #00ff88;
        }
        
        .profile-info-label {
            color: #00ff88;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }
        
        .profile-info-value {
            color: #fff;
            font-size: 1rem;
        }
        
        .profile-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .profile-action-btn {
            flex: 1;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #00ff88, #00ffea);
            color: #000;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .profile-action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 255, 136, 0.5);
        }
        
        @media (max-width: 768px) {
            .chat-container {
                height: 400px;
            }
            
            .message-item.own-message {
                max-width: 85%;
                min-width: 120px;
            }
            
            .message-item {
                max-width: 85%;
                width: fit-content;
                min-width: 120px;
                overflow: visible;
                padding: 0.35rem 0.6rem;
            }
            
            .message-text {
                max-width: 100%;
                overflow: visible;
                word-wrap: break-word;
                word-break: break-word;
                white-space: pre-wrap;
            }
            
            .chat-form {
                flex-direction: column;
            }
            
            .chat-send-btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Header du template admin -->
    <header>
        <div class="container">
            <div style="display: flex; align-items: center; gap: 10px;">
                <img src="../../frontoffice/assests/logo.png" alt="Logo GameHub Pro" style="width: 50px; height: 50px;">
                <h1 class="logo">GameHub Pro</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="../../frontoffice/index.php" class="super-button">Home</a></li>
                    <li><a href="../../frontoffice/collaborations.php" class="super-button">Collaborations</a></li>
                    <li><a href="collaboration.php" class="super-button">Gestion Collab</a></li>
                </ul>
            </nav>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <button id="profile-btn" class="profile-header-btn" onclick="openProfileModal()" title="Mon Profil">
                    <span style="font-size: 1.5rem;">👤</span>
                </button>
                <button id="sidebar-toggle" class="sidebar-toggle">☰</button>
            </div>
        </div>
    </header>

    <!-- Sidebar du template admin -->
    <aside id="sidebar" class="sidebar">
        <nav>
            <ul>
                <li><a href="../../frontoffice/index.php">Home</a></li>
                <li><a href="../../frontoffice/collaborations.php">Collaborations</a></li>
                <li><a href="collaboration.php">Gestion Collab</a></li>
            </ul>
        </nav>
    </aside>

    <!-- Contenu principal dans le layout admin -->
    <main id="main-content" class="main-content">
        <div class="room-container">
            <a href="view_collab.php?id=<?php echo $collab_id; ?>" class="back-link">← Retour à la Collaboration</a>
            
            <?php if ($showMessageSent): ?>
                <div style="background: rgba(0, 255, 136, 0.2); color: #00ff88; padding: 15px; border-radius: 10px; margin-bottom: 2rem; border: 2px solid rgba(0, 255, 136, 0.5); text-align: center; font-weight: 600; animation: slideIn 0.3s ease;">
                    ✅ Message envoyé avec succès !
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error']) && $_GET['error'] === 'message_blocked'): 
                $reason = isset($_GET['reason']) ? urldecode($_GET['reason']) : 'Message bloqué par la modération';
                $level = isset($_GET['level']) ? intval($_GET['level']) : 0;
            ?>
                <div style="background: rgba(255, 51, 92, 0.2); color: #ff335c; padding: 15px; border-radius: 10px; margin-bottom: 2rem; border: 2px solid rgba(255, 51, 92, 0.5); text-align: center; font-weight: 600; animation: slideIn 0.3s ease;">
                    🚫 Message bloqué par la modération
                    <br><small style="font-size: 0.85rem; margin-top: 0.5rem; display: block;">
                        Niveau <?php echo $level; ?> : <?php echo htmlspecialchars($reason); ?>
                    </small>
                </div>
                <script>
                    setTimeout(function() {
                        window.history.replaceState({}, document.title, window.location.pathname + '?id=<?php echo $collab_id; ?>');
                    }, 5000);
                </script>
            <?php endif; ?>
            
            <?php if ($showMessageSent): ?>
                <script>
                    // Supprimer le paramètre de l'URL après affichage
                    setTimeout(function() {
                        window.history.replaceState({}, document.title, window.location.pathname + '?id=<?php echo $collab_id; ?>');
                    }, 3000);
                    // Rafraîchir les messages
                    setTimeout(function() {
                        if (typeof refreshMessages === 'function') {
                            refreshMessages();
                        }
                    }, 500);
                </script>
            <?php endif; ?>
            
            <div class="room-header">
                <h1>🏠 Room Collab - <?php echo htmlspecialchars($collab['titre']); ?></h1>
                <div class="collab-info">
                    <p><strong>Description :</strong> <?php echo nl2br(htmlspecialchars($collab['description'])); ?></p>
                    <p><strong>Membres :</strong> <?php echo count($members); ?> / <?php echo $collab['max_membres']; ?></p>
                </div>
                <?php if ($isComplete): ?>
                    <span class="status-badge complete">✅ Collaboration Complète</span>
                <?php endif; ?>
            </div>

            <div class="members-section">
                <h2>👥 Membres de la Collaboration</h2>
                <div class="members-count">
                    Total : <strong><?php echo count($members); ?></strong> membre(s)
                </div>

                <?php if (empty($members)): ?>
                    <div class="empty-state">
                        Aucun membre dans cette collaboration.
                    </div>
                <?php else: ?>
                    <div class="members-grid">
                        <?php 
                        // Séparer le owner des autres membres
                        $ownerMember = null;
                        $otherMembers = [];
                        
                        foreach ($members as $member) {
                            if ($member['user_id'] == $collab['owner_id'] && $member['role'] == 'owner') {
                                $ownerMember = $member;
                            } else {
                                $otherMembers[] = $member;
                            }
                        }
                        
                        // Afficher d'abord le owner
                        if ($ownerMember):
                            $ownerAvatar = getUserAvatar($db, $ownerMember['user_id']);
                        ?>
                            <div class="member-card owner">
                                <div class="member-avatar-container">
                                    <?php if ($ownerAvatar): ?>
                                        <div id="avatar-owner-<?php echo $ownerMember['user_id']; ?>" class="member-avatar-render"></div>
                                    <?php else: ?>
                                        <div class="member-avatar">👑</div>
                                    <?php endif; ?>
                                </div>
                                <div class="member-name">Propriétaire</div>
                                <div class="member-role"><?php echo translateRole($ownerMember['role']); ?></div>
                                <div class="member-id">User ID: <?php echo htmlspecialchars($ownerMember['user_id']); ?></div>
                            </div>
                            <?php if ($ownerAvatar): ?>
                                <script>
                                    // Rendre l'avatar du propriétaire après chargement de la page (portrait mode - tête seulement)
                                    document.addEventListener('DOMContentLoaded', function() {
                                        if (typeof CartoonAvatarRenderer !== 'undefined') {
                                            const ownerAvatarConfig = <?php echo json_encode($ownerAvatar); ?>;
                                            const ownerContainer = document.getElementById('avatar-owner-<?php echo $ownerMember['user_id']; ?>');
                                            if (ownerContainer) {
                                                const ownerRenderer = new CartoonAvatarRenderer('avatar-owner-<?php echo $ownerMember['user_id']; ?>', ownerAvatarConfig);
                                                ownerRenderer.render(true); // Portrait mode - only head
                                                
                                                // Appliquer les paramètres de recadrage si disponibles
                                                <?php if (isset($ownerAvatar['crop'])): ?>
                                                setTimeout(() => {
                                                    applyCropToAvatar('avatar-owner-<?php echo $ownerMember['user_id']; ?>', {
                                                        scale: <?php echo isset($ownerAvatar['crop']['scale']) ? $ownerAvatar['crop']['scale'] : 0.7; ?>,
                                                        x: <?php echo isset($ownerAvatar['crop']['x']) ? $ownerAvatar['crop']['x'] : 0; ?>,
                                                        y: <?php echo isset($ownerAvatar['crop']['y']) ? $ownerAvatar['crop']['y'] : -15; ?>
                                                    }, true);
                                                }, 300);
                                                <?php endif; ?>
                                            }
                                        }
                                    });
                                </script>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php 
                        // Afficher les autres membres
                        foreach ($otherMembers as $member):
                            $memberAvatar = getUserAvatar($db, $member['user_id']);
                        ?>
                            <div class="member-card">
                                <div class="member-avatar-container">
                                    <?php if ($memberAvatar): ?>
                                        <div id="avatar-member-<?php echo $member['user_id']; ?>" class="member-avatar-render"></div>
                                    <?php else: ?>
                                        <div class="member-avatar"><?php echo 'U' . substr($member['user_id'], 0, 1); ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="member-name">Membre #<?php echo htmlspecialchars($member['user_id']); ?></div>
                                <div class="member-role"><?php echo translateRole($member['role']); ?></div>
                                <div class="member-id">User ID: <?php echo htmlspecialchars($member['user_id']); ?></div>
                            </div>
                            <?php if ($memberAvatar): ?>
                                <script>
                                    // Rendre l'avatar du membre après chargement de la page (portrait mode - tête seulement)
                                    document.addEventListener('DOMContentLoaded', function() {
                                        if (typeof CartoonAvatarRenderer !== 'undefined') {
                                            const memberAvatarConfig = <?php echo json_encode($memberAvatar); ?>;
                                            const memberContainer = document.getElementById('avatar-member-<?php echo $member['user_id']; ?>');
                                            if (memberContainer) {
                                                const memberRenderer = new CartoonAvatarRenderer('avatar-member-<?php echo $member['user_id']; ?>', memberAvatarConfig);
                                                memberRenderer.render(true); // Portrait mode - only head
                                                
                                                // Appliquer les paramètres de recadrage si disponibles
                                                <?php if (isset($memberAvatar['crop'])): ?>
                                                setTimeout(() => {
                                                    applyCropToAvatar('avatar-member-<?php echo $member['user_id']; ?>', {
                                                        scale: <?php echo isset($memberAvatar['crop']['scale']) ? $memberAvatar['crop']['scale'] : 0.7; ?>,
                                                        x: <?php echo isset($memberAvatar['crop']['x']) ? $memberAvatar['crop']['x'] : 0; ?>,
                                                        y: <?php echo isset($memberAvatar['crop']['y']) ? $memberAvatar['crop']['y'] : -15; ?>
                                                    }, true);
                                                }, 300);
                                                <?php endif; ?>
                                            }
                                        }
                                    });
                                </script>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Section Chat -->
            <?php if ($canViewChat): ?>
            <div class="chat-section">
                <div class="cyber-chat-header">
                    <div class="cyber-chat-title">
                        <span class="chat-icon">💬</span>
                        <div>
                            <h2>Chat de la Collaboration</h2>
                            <p>Canal temps réel</p>
                        </div>
                    </div>
                    <a href="moderation_dashboard.php?collab_id=<?php echo $collab_id; ?>" class="cyber-badge">
                        🛡️ Modération
                    </a>
                </div>
                <div class="chat-info">
                    <span>👥 <?php echo count($members); ?> membre(s) peuvent participer au chat</span>
                </div>
                
                <div class="chat-container">
                    <div class="chat-messages" id="chatMessages">
                        <?php if (empty($messages)): ?>
                            <div class="no-messages">
                                <p>💬 Aucun message pour le moment. Soyez le premier à écrire !</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($messages as $msg): 
                                $isOwnMessage = ($msg['user_id'] == $currentUserId);
                                $messageDate = date('d/m/Y H:i', strtotime($msg['date_message']));
                                
                                // Trouver le nom du membre
                                $memberName = 'Membre #' . $msg['user_id'];
                                foreach ($members as $m) {
                                    if ($m['user_id'] == $msg['user_id']) {
                                        if ($m['role'] == 'owner') {
                                            $memberName = '👑 Propriétaire';
                                        } else {
                                            $memberName = 'Membre #' . $msg['user_id'];
                                        }
                                        break;
                                    }
                                }
                            ?>
                                <div class="message-item <?php echo $isOwnMessage ? 'own-message' : ''; ?>" data-message-id="<?php echo $msg['id']; ?>">
                                    <div class="message-header">
                                        <span class="message-user"><?php echo htmlspecialchars($memberName); ?></span>
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <span class="message-date"><?php echo $messageDate; ?></span>
                                            <?php if ($isOwnMessage): ?>
                                                <div class="message-menu">
                                                    <button class="message-menu-btn" onclick="toggleMessageMenu(this, event)" title="Options">
                                                        ⋮
                                                    </button>
                                                    <div class="message-menu-dropdown">
                                                        <div class="message-menu-item" onclick="editMessage(<?php echo $msg['id']; ?>, '<?php echo htmlspecialchars(addslashes($msg['message'])); ?>')">
                                                            ✏️ Modifier
                                                        </div>
                                                        <div class="message-menu-item delete" onclick="deleteMessage(<?php echo $msg['id']; ?>)">
                                                            🗑️ Supprimer
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="message-text" id="message-text-<?php echo $msg['id']; ?>">
                                        <?php if (!empty($msg['message'])): 
                                            // Détecter et extraire les fichiers joints
                                            $messageText = $msg['message'];
                                            $filesPattern = '/📎 Fichiers: (.+)/';
                                            $hasFiles = preg_match($filesPattern, $messageText, $filesMatch);
                                            
                                            if ($hasFiles) {
                                                // Extraire les noms de fichiers
                                                $fileNames = explode(', ', $filesMatch[1]);
                                                // Retirer la ligne des fichiers du message
                                                $messageText = preg_replace($filesPattern, '', $messageText);
                                                $messageText = trim($messageText);
                                                
                                                // Afficher le message sans les fichiers
                                                if (!empty($messageText)) {
                                                    echo nl2br(htmlspecialchars($messageText));
                                                }
                                                
                                                // Afficher les fichiers joints
                                                echo '<div class="message-files" style="margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px solid rgba(0, 255, 136, 0.2);">';
                                                echo '<div style="font-size: 0.85rem; color: #00ff88; margin-bottom: 0.4rem; font-weight: 600;">📎 Fichiers joints:</div>';
                                                foreach ($fileNames as $fileName) {
                                                    $fileName = trim($fileName);
                                                    if (!empty($fileName)) {
                                                        // Chercher le fichier dans le dossier uploads/messages
                                                        $filePath = __DIR__ . '/../../../uploads/messages/';
                                                        $foundFile = null;
                                                        if (is_dir($filePath)) {
                                                            $files = scandir($filePath);
                                                            foreach ($files as $file) {
                                                                if ($file !== '.' && $file !== '..' && strpos($file, $fileName) !== false) {
                                                                    $foundFile = $file;
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        
                                                        if ($foundFile) {
                                                            $fileUrl = 'get_file.php?file=' . urlencode($foundFile);
                                                            $extension = strtolower(pathinfo($foundFile, PATHINFO_EXTENSION));
                                                            $icon = '📄';
                                                            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
                                                                $icon = '🖼️';
                                                            } elseif ($extension === 'pdf') {
                                                                $icon = '📕';
                                                            } elseif (in_array($extension, ['doc', 'docx'])) {
                                                                $icon = '📘';
                                                            } elseif (in_array($extension, ['zip', 'rar', '7z'])) {
                                                                $icon = '📦';
                                                            }
                                                            
                                                            echo '<div style="margin: 0.3rem 0;">';
                                                            echo '<a href="' . htmlspecialchars($fileUrl) . '" target="_blank" style="color: #00ffea; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.3rem 0.6rem; background: rgba(0, 255, 234, 0.1); border-radius: 6px; border: 1px solid rgba(0, 255, 234, 0.3); transition: all 0.2s;">';
                                                            echo '<span>' . $icon . '</span>';
                                                            echo '<span>' . htmlspecialchars($fileName) . '</span>';
                                                            echo '</a>';
                                                            echo '</div>';
                                                        } else {
                                                            // Fichier non trouvé, afficher juste le nom
                                                            echo '<div style="margin: 0.3rem 0; color: #888; font-size: 0.85rem;">📄 ' . htmlspecialchars($fileName) . ' (fichier non disponible)</div>';
                                                        }
                                                    }
                                                }
                                                echo '</div>';
                                            } else {
                                                // Pas de fichiers, afficher le message normalement
                                                echo nl2br(htmlspecialchars($messageText));
                                            }
                                        endif; ?>
                                    </div>
                                    <?php if (!empty($msg['audio_path'])): 
                                        // Extraire le nom du fichier depuis le chemin
                                        $audioFileName = basename($msg['audio_path']);
                                        // Utiliser l'endpoint PHP pour servir le fichier
                                        $audioUrl = 'get_audio.php?file=' . urlencode($audioFileName);
                                    ?>
                                        <div class="message-audio">
                                            <div class="message-audio-icon">🎵</div>
                                            <div class="message-audio-player">
                                                <audio controls preload="metadata">
                                                    <source src="<?php echo htmlspecialchars($audioUrl); ?>" type="audio/webm">
                                                    <source src="<?php echo htmlspecialchars($audioUrl); ?>" type="audio/mpeg">
                                                    <source src="<?php echo htmlspecialchars($audioUrl); ?>" type="audio/wav">
                                                    Votre navigateur ne supporte pas la lecture audio.
                                                </audio>
                                            </div>
                                            <?php if (!empty($msg['audio_duration'])): ?>
                                                <div class="message-audio-duration">
                                                    <?php echo gmdate('i:s', $msg['audio_duration']); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="message-edit-form" id="message-edit-<?php echo $msg['id']; ?>">
                                        <textarea class="message-edit-input" id="message-edit-input-<?php echo $msg['id']; ?>" rows="3"></textarea>
                                        <div class="message-edit-actions">
                                            <button class="message-edit-btn message-edit-save" onclick="saveMessageEdit(<?php echo $msg['id']; ?>)">
                                                💾 Enregistrer
                                            </button>
                                            <button class="message-edit-btn message-edit-cancel" onclick="cancelMessageEdit(<?php echo $msg['id']; ?>)">
                                                ❌ Annuler
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <div class="chat-form-container">
                        <!-- Voice Recorder -->
                        <div class="voice-recorder-container">
                            <button 
                                class="voice-record-btn" 
                                id="voiceRecordBtn"
                                title="Maintenir pour enregistrer un message vocal"
                                onmousedown="startVoiceRecording(event)"
                                onmouseup="stopVoiceRecording(event)"
                                onmouseleave="stopVoiceRecording(event)"
                                ontouchstart="startVoiceRecording(event)"
                                ontouchend="stopVoiceRecording(event)"
                                ontouchcancel="stopVoiceRecording(event)">
                                🎤
                            </button>
                            <div class="voice-record-status" id="voiceRecordStatus">
                                <div class="voice-record-waves">
                                    <div class="voice-wave"></div>
                                    <div class="voice-wave"></div>
                                    <div class="voice-wave"></div>
                                    <div class="voice-wave"></div>
                                </div>
                                <span class="voice-record-timer" id="voiceRecordTimer">00:00</span>
                                <span>Enregistrement en cours...</span>
                            </div>
                        </div>
                        
                        <form action="send_message.php" method="POST" class="chat-form" id="chatForm" enctype="multipart/form-data">
                            <input type="hidden" name="collab_id" value="<?php echo $collab_id; ?>">
                            <input type="hidden" name="redirect_to" value="room_collab">
                            <input type="file" id="chatFileInput" name="chatFileInput[]" class="chat-file-input" multiple accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.txt,.zip,.rar,.xls,.xlsx,.json,.csv,.7z">
                            <button type="button" class="chat-attach-btn" id="chatAttachBtn" title="Joindre un fichier">
                                +
                            </button>
                            <div style="display: flex; align-items: flex-end; gap: 0.5rem; flex: 1;">
                                <textarea 
                                    name="message" 
                                    class="chat-input" 
                                    id="chatMessageInput"
                                    rows="3" 
                                    placeholder="Tapez votre message ici..." 
                                    required></textarea>
                                <button type="submit" class="chat-send-btn">📤 Envoyer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="chat-section">
                <div class="no-messages">
                    <p>🔒 Vous devez être membre de cette collaboration pour accéder au chat.</p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>
    
    <footer style="background: rgba(0, 0, 0, 0.8); padding: 2rem; margin-top: 4rem; border-top: 1px solid rgba(0, 255, 136, 0.3); text-align: center; color: #aaa;">
        <p>&copy; 2025 GameHub Pro | All rights reserved | Tunis, Tunisia</p>
    </footer>

    <script>
       
        
        // Scroll initial
        document.addEventListener('DOMContentLoaded', function() {
            scrollToBottom();
        });
        
        // Auto-refresh des messages toutes les 5 secondes
        let refreshInterval;
        
        function startAutoRefresh() {
            refreshInterval = setInterval(function() {
                refreshMessages();
            }, 5000); // Rafraîchir toutes les 5 secondes
        }
        
        function refreshMessages() {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_messages.php?collab_id=<?php echo $collab_id; ?>', true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success && response.messages) {
                            updateMessagesDisplay(response.messages);
                        }
                    } catch (e) {
                        console.error('Erreur lors du rafraîchissement:', e);
                    }
                }
            };
            xhr.send();
        }
        
        function updateMessagesDisplay(messages) {
            const messagesBox = document.getElementById('chatMessages');
            if (!messagesBox) return;
            
            const currentUserId = <?php echo $currentUserId; ?>;
            const members = <?php echo json_encode($members); ?>;
            
            // Sauvegarder la position de scroll
            const wasAtBottom = messagesBox.scrollHeight - messagesBox.scrollTop <= messagesBox.clientHeight + 100;
            
            messagesBox.innerHTML = '';
            
            if (messages.length === 0) {
                messagesBox.innerHTML = '<div class="no-messages"><p>💬 Aucun message pour le moment. Soyez le premier à écrire !</p></div>';
            } else {
                messages.forEach(function(msg) {
                    const isOwnMessage = (msg.user_id == currentUserId);
                    const messageDate = new Date(msg.date_message).toLocaleString('fr-FR', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    
                    // Trouver le nom du membre
                    let memberName = 'Membre #' + msg.user_id;
                    members.forEach(function(m) {
                        if (m.user_id == msg.user_id) {
                            if (m.role == 'owner') {
                                memberName = '👑 Propriétaire';
                            } else {
                                memberName = 'Membre #' + msg.user_id;
                            }
                        }
                    });
                    
                    const messageDiv = document.createElement('div');
                    messageDiv.className = 'message-item' + (isOwnMessage ? ' own-message' : '');
                    messageDiv.setAttribute('data-message-id', msg.id);
                    
                    let menuHtml = '';
                    if (isOwnMessage) {
                        menuHtml = `
                            <div class="message-menu">
                                <button class="message-menu-btn" onclick="toggleMessageMenu(this, event)" title="Options">
                                    ⋮
                                </button>
                                <div class="message-menu-dropdown">
                                    <div class="message-menu-item" onclick="editMessage(${msg.id}, ${JSON.stringify(msg.message)})">
                                        ✏️ Modifier
                                    </div>
                                    <div class="message-menu-item delete" onclick="deleteMessage(${msg.id})">
                                        🗑️ Supprimer
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                    
                    messageDiv.innerHTML = `
                        <div class="message-header">
                            <span class="message-user">${escapeHtml(memberName)}</span>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <span class="message-date">${messageDate}</span>
                                ${menuHtml}
                            </div>
                        </div>
                        <div class="message-text" id="message-text-${msg.id}">
                            ${(() => {
                                let messageHtml = '';
                                if (msg.message) {
                                    // Détecter les fichiers joints
                                    const filesPattern = /📎 Fichiers: (.+)/;
                                    const filesMatch = msg.message.match(filesPattern);
                                    
                                    if (filesMatch) {
                                        // Extraire les noms de fichiers
                                        const fileNames = filesMatch[1].split(', ');
                                        // Retirer la ligne des fichiers du message
                                        let messageText = msg.message.replace(filesPattern, '').trim();
                                        
                                        // Afficher le message sans les fichiers
                                        if (messageText) {
                                            messageHtml += escapeHtml(messageText).replace(/\n/g, '<br>');
                                        }
                                        
                                        // Afficher les fichiers joints
                                        messageHtml += '<div class="message-files" style="margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px solid rgba(0, 255, 136, 0.2);">';
                                        messageHtml += '<div style="font-size: 0.85rem; color: #00ff88; margin-bottom: 0.4rem; font-weight: 600;">📎 Fichiers joints:</div>';
                                        
                                        fileNames.forEach(fileName => {
                                            fileName = fileName.trim();
                                            if (fileName) {
                                                // Pour l'instant, on affiche juste le nom
                                                // Le serveur cherchera le fichier par nom
                                                const extension = fileName.split('.').pop().toLowerCase();
                                                let icon = '📄';
                                                if (['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'].includes(extension)) {
                                                    icon = '🖼️';
                                                } else if (extension === 'pdf') {
                                                    icon = '📕';
                                                } else if (['doc', 'docx'].includes(extension)) {
                                                    icon = '📘';
                                                } else if (['zip', 'rar', '7z'].includes(extension)) {
                                                    icon = '📦';
                                                }
                                                
                                                // Encoder le nom de fichier pour l'URL (on cherchera par nom)
                                                const fileUrl = 'get_file.php?file=' + encodeURIComponent(fileName);
                                                messageHtml += '<div style="margin: 0.3rem 0;">';
                                                messageHtml += '<a href="' + fileUrl + '" target="_blank" style="color: #00ffea; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.3rem 0.6rem; background: rgba(0, 255, 234, 0.1); border-radius: 6px; border: 1px solid rgba(0, 255, 234, 0.3); transition: all 0.2s;">';
                                                messageHtml += '<span>' + icon + '</span>';
                                                messageHtml += '<span>' + escapeHtml(fileName) + '</span>';
                                                messageHtml += '</a>';
                                                messageHtml += '</div>';
                                            }
                                        });
                                        
                                        messageHtml += '</div>';
                                    } else {
                                        // Pas de fichiers, afficher le message normalement
                                        messageHtml = escapeHtml(msg.message).replace(/\n/g, '<br>');
                                    }
                                }
                                return messageHtml;
                            })()}
                        </div>
                        ${msg.audio_path ? (() => {
                            // Extraire le nom du fichier depuis le chemin
                            const audioFileName = msg.audio_path.split('/').pop();
                            const audioUrl = 'get_audio.php?file=' + encodeURIComponent(audioFileName);
                            return `
                            <div class="message-audio">
                                <div class="message-audio-icon">🎵</div>
                                <div class="message-audio-player">
                                    <audio controls preload="metadata">
                                        <source src="${audioUrl}" type="audio/webm">
                                        <source src="${audioUrl}" type="audio/mpeg">
                                        <source src="${audioUrl}" type="audio/wav">
                                        Votre navigateur ne supporte pas la lecture audio.
                                    </audio>
                                </div>
                                ${msg.audio_duration ? `
                                    <div class="message-audio-duration">
                                        ${formatDuration(msg.audio_duration)}
                                    </div>
                                ` : ''}
                            </div>
                        `;
                        })() : ''}
                        <div class="message-edit-form" id="message-edit-${msg.id}">
                            <textarea class="message-edit-input" id="message-edit-input-${msg.id}" rows="3"></textarea>
                            <div class="message-edit-actions">
                                <button class="message-edit-btn message-edit-save" onclick="saveMessageEdit(${msg.id})">
                                    💾 Enregistrer
                                </button>
                                <button class="message-edit-btn message-edit-cancel" onclick="cancelMessageEdit(${msg.id})">
                                    ❌ Annuler
                                </button>
                            </div>
                        </div>
                    `;
                    messagesBox.appendChild(messageDiv);
                });
            }
            
            // Restaurer la position de scroll si on était en bas
            if (wasAtBottom) {
                setTimeout(scrollToBottom, 100);
            }
        }
        
        // Fonction pour scroller vers le bas
        function scrollToBottom() {
            const messagesBox = document.getElementById('chatMessages');
            if (messagesBox) {
                messagesBox.scrollTop = messagesBox.scrollHeight;
            }
        }
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        function formatDuration(seconds) {
            const mins = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
        }
        
        // ============================================
        // ENREGISTREMENT VOCAL
        // ============================================
        let mediaRecorder = null;
        let audioChunks = [];
        let recordingTimer = null;
        let recordingStartTime = null;
        let isRecording = false;
        
        // Fonction pour démarrer l'enregistrement
        function startVoiceRecording(event) {
            event.preventDefault();
            
            if (isRecording) return;
            
            // Demander l'accès au microphone
            navigator.mediaDevices.getUserMedia({ audio: true })
                .then(function(stream) {
                    isRecording = true;
                    audioChunks = [];
                    
                    // Créer le MediaRecorder
                    const options = {
                        mimeType: 'audio/webm;codecs=opus'
                    };
                    
                    // Fallback pour les navigateurs qui ne supportent pas webm
                    if (!MediaRecorder.isTypeSupported(options.mimeType)) {
                        options.mimeType = 'audio/webm';
                        if (!MediaRecorder.isTypeSupported(options.mimeType)) {
                            options.mimeType = 'audio/mp4';
                        }
                    }
                    
                    mediaRecorder = new MediaRecorder(stream, options);
                    
                    // Événements du MediaRecorder
                    mediaRecorder.ondataavailable = function(event) {
                        if (event.data.size > 0) {
                            audioChunks.push(event.data);
                        }
                    };
                    
                    mediaRecorder.onstop = function() {
                        // Arrêter tous les tracks du stream
                        stream.getTracks().forEach(track => track.stop());
                    };
                    
                    // Démarrer l'enregistrement
                    mediaRecorder.start();
                    
                    // Mettre à jour l'UI
                    const recordBtn = document.getElementById('voiceRecordBtn');
                    const recordStatus = document.getElementById('voiceRecordStatus');
                    
                    if (recordBtn) {
                        recordBtn.classList.add('recording');
                    }
                    if (recordStatus) {
                        recordStatus.classList.add('active');
                    }
                    
                    // Démarrer le timer
                    recordingStartTime = Date.now();
                    recordingTimer = setInterval(updateRecordingTimer, 100);
                })
                .catch(function(error) {
                    console.error('Erreur d\'accès au microphone:', error);
                    alert('Impossible d\'accéder au microphone. Veuillez vérifier les permissions de votre navigateur.');
                });
        }
        
        // Fonction pour arrêter l'enregistrement
        function stopVoiceRecording(event) {
            event.preventDefault();
            
            if (!isRecording || !mediaRecorder) return;
            
            // Arrêter l'enregistrement
            if (mediaRecorder.state !== 'inactive') {
                mediaRecorder.stop();
            }
            
            // Arrêter le timer
            if (recordingTimer) {
                clearInterval(recordingTimer);
                recordingTimer = null;
            }
            
            // Mettre à jour l'UI
            const recordBtn = document.getElementById('voiceRecordBtn');
            const recordStatus = document.getElementById('voiceRecordStatus');
            
            if (recordBtn) {
                recordBtn.classList.remove('recording');
            }
            if (recordStatus) {
                recordStatus.classList.remove('active');
            }
            
            // Attendre que l'enregistrement soit terminé
            const originalOnStop = mediaRecorder.onstop;
            mediaRecorder.onstop = function() {
                if (originalOnStop) originalOnStop();
                
                // Créer le blob audio
                const audioBlob = new Blob(audioChunks, { type: mediaRecorder.mimeType });
                
                // Calculer la durée
                const duration = Math.round((Date.now() - recordingStartTime) / 1000);
                
                // Vérifier la durée minimale (1 seconde)
                if (duration < 1) {
                    alert('Enregistrement trop court. Veuillez enregistrer au moins 1 seconde.');
                    isRecording = false;
                    return;
                }
                
                // Envoyer le fichier audio
                uploadVoiceMessage(audioBlob, duration);
                
                isRecording = false;
            };
        }
        
        // Fonction pour mettre à jour le timer
        function updateRecordingTimer() {
            if (!recordingStartTime) return;
            
            const elapsed = Math.floor((Date.now() - recordingStartTime) / 1000);
            const minutes = Math.floor(elapsed / 60);
            const seconds = elapsed % 60;
            
            const timerElement = document.getElementById('voiceRecordTimer');
            if (timerElement) {
                timerElement.textContent = 
                    String(minutes).padStart(2, '0') + ':' + 
                    String(seconds).padStart(2, '0');
            }
        }
        
        // Fonction pour uploader le message vocal
        function uploadVoiceMessage(audioBlob, duration) {
            const formData = new FormData();
            formData.append('audio', audioBlob, 'voice-message.webm');
            formData.append('collab_id', <?php echo $collab_id; ?>);
            formData.append('duration', duration);
            formData.append('redirect_to', 'room_collab');
            
            // Afficher un indicateur de chargement
            const recordStatus = document.getElementById('voiceRecordStatus');
            if (recordStatus) {
                recordStatus.innerHTML = '<span>📤 Envoi du message vocal...</span>';
                recordStatus.classList.add('active');
            }
            
            // Envoyer le fichier
            fetch('send_voice_message.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Masquer le statut
                    if (recordStatus) {
                        recordStatus.classList.remove('active');
                    }
                    
                    // Rafraîchir les messages
                    if (typeof refreshMessages === 'function') {
                        refreshMessages();
                    }
                    
                    // Scroll vers le bas
                    setTimeout(scrollToBottom, 500);
                } else {
                    alert('Erreur : ' + (data.message || 'Impossible d\'envoyer le message vocal'));
                    if (recordStatus) {
                        recordStatus.classList.remove('active');
                    }
                }
            })
            .catch(error => {
                console.error('Erreur lors de l\'envoi:', error);
                alert('Erreur lors de l\'envoi du message vocal');
                if (recordStatus) {
                    recordStatus.classList.remove('active');
                }
            });
        }
        
        // Démarrer l'auto-refresh
        <?php if ($canViewChat): ?>
        startAutoRefresh();
        <?php endif; ?>
        
        // Gestion du bouton d'attachement de fichiers
        document.addEventListener('DOMContentLoaded', function() {
            const attachBtn = document.getElementById('chatAttachBtn');
            const fileInput = document.getElementById('chatFileInput');
            
            if (attachBtn && fileInput) {
                attachBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    fileInput.click();
                });
                
                fileInput.addEventListener('change', function(e) {
                const files = e.target.files;
                if (files && files.length > 0) {
                    // Afficher les fichiers sélectionnés
                    let fileList = '';
                    for (let i = 0; i < files.length; i++) {
                        fileList += files[i].name;
                        if (i < files.length - 1) fileList += ', ';
                    }
                    
                    // Ajouter les noms de fichiers dans le textarea
                    const messageInput = document.getElementById('chatMessageInput');
                    if (messageInput) {
                        const currentText = messageInput.value;
                        const fileNames = Array.from(files).map(f => f.name).join(', ');
                        messageInput.value = currentText + (currentText ? '\n' : '') + '📎 Fichiers: ' + fileNames;
                    }
                    
                    // Afficher une notification visuelle
                    const notification = document.createElement('div');
                    notification.style.cssText = `
                        position: fixed;
                        bottom: 100px;
                        right: 20px;
                        background: rgba(0, 255, 136, 0.2);
                        border: 2px solid rgba(0, 255, 136, 0.5);
                        color: #00ff88;
                        padding: 0.75rem 1.5rem;
                        border-radius: 10px;
                        font-weight: 600;
                        z-index: 10000;
                        animation: slideInRight 0.3s ease-out;
                        box-shadow: 0 5px 20px rgba(0, 255, 136, 0.3);
                    `;
                    notification.textContent = `📎 ${files.length} fichier(s) sélectionné(s)`;
                    document.body.appendChild(notification);
                    
                    setTimeout(() => {
                        notification.style.animation = 'slideOutRight 0.3s ease-out';
                        setTimeout(() => notification.remove(), 300);
                    }, 3000);
                }
                });
            }
        });
        
        // Animation pour les notifications
        if (!document.getElementById('notification-styles')) {
            const style = document.createElement('style');
            style.id = 'notification-styles';
            style.textContent = `
                @keyframes slideInRight {
                    from {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }
                @keyframes slideOutRight {
                    from {
                        transform: translateX(0);
                        opacity: 1;
                    }
                    to {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);
        }
        
        // Réinitialiser le formulaire après envoi
        document.getElementById('chatForm')?.addEventListener('submit', function(e) {
            const input = document.getElementById('chatMessageInput');
            if (input && input.value.trim()) {
                // Le formulaire sera soumis normalement
                // Le rafraîchissement se fera après le rechargement de la page
            }
        });
        
        // Fonction pour afficher/masquer le menu des messages
        function toggleMessageMenu(button, event) {
            event.stopPropagation();
            
            // Fermer tous les autres menus
            document.querySelectorAll('.message-menu-dropdown').forEach(menu => {
                if (menu !== button.nextElementSibling) {
                    menu.classList.remove('show');
                }
            });
            
            // Toggle le menu actuel
            const menu = button.nextElementSibling;
            if (menu) {
                menu.classList.toggle('show');
            }
        }
        
        // Fermer les menus au clic ailleurs
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.message-menu')) {
                document.querySelectorAll('.message-menu-dropdown').forEach(menu => {
                    menu.classList.remove('show');
                });
            }
        });
        
        // Fonction pour éditer un message
        function editMessage(messageId, currentMessage) {
            // Fermer tous les menus
            document.querySelectorAll('.message-menu-dropdown').forEach(menu => {
                menu.classList.remove('show');
            });
            
            const messageText = document.getElementById(`message-text-${messageId}`);
            const editForm = document.getElementById(`message-edit-${messageId}`);
            const editInput = document.getElementById(`message-edit-input-${messageId}`);
            
            if (messageText && editForm && editInput) {
                // Masquer le texte du message
                messageText.style.display = 'none';
                
                // Afficher le formulaire d'édition
                editForm.classList.add('active');
                
                // Remplir le champ avec le message actuel
                editInput.value = currentMessage;
                editInput.focus();
            }
        }
        
        // Fonction pour annuler l'édition
        function cancelMessageEdit(messageId) {
            const messageText = document.getElementById(`message-text-${messageId}`);
            const editForm = document.getElementById(`message-edit-${messageId}`);
            
            if (messageText && editForm) {
                // Afficher le texte du message
                messageText.style.display = 'block';
                
                // Masquer le formulaire d'édition
                editForm.classList.remove('active');
            }
        }
        
        // Fonction pour sauvegarder l'édition d'un message
        function saveMessageEdit(messageId) {
            const editInput = document.getElementById(`message-edit-input-${messageId}`);
            if (!editInput) return;
            
            const newMessage = editInput.value.trim();
            if (!newMessage) {
                alert('Le message ne peut pas être vide');
                return;
            }
            
            // Envoyer la requête de mise à jour
            fetch('update_message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    message_id: messageId,
                    message: newMessage
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mettre à jour le texte du message
                    const messageText = document.getElementById(`message-text-${messageId}`);
                    if (messageText) {
                        messageText.innerHTML = escapeHtml(newMessage).replace(/\n/g, '<br>');
                        messageText.style.display = 'block';
                    }
                    
                    // Masquer le formulaire d'édition
                    const editForm = document.getElementById(`message-edit-${messageId}`);
                    if (editForm) {
                        editForm.classList.remove('active');
                    }
                } else {
                    alert('Erreur : ' + (data.message || 'Impossible de modifier le message'));
                }
            })
            .catch(error => {
                console.error('Erreur lors de la modification:', error);
                alert('Erreur lors de la modification du message');
            });
        }
        
        // Fonction pour supprimer un message
        function deleteMessage(messageId) {
            // Fermer le menu
            document.querySelectorAll('.message-menu-dropdown').forEach(menu => {
                menu.classList.remove('show');
            });
            
            if (!confirm('Êtes-vous sûr de vouloir supprimer ce message ?')) {
                return;
            }
            
            // Envoyer la requête de suppression
            fetch('delete_message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    message_id: messageId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Supprimer l'élément du message du DOM
                    const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
                    if (messageElement) {
                        messageElement.style.opacity = '0';
                        messageElement.style.transform = 'translateX(-20px)';
                        setTimeout(() => {
                            messageElement.remove();
                            
                            // Vérifier s'il reste des messages
                            const messagesBox = document.getElementById('chatMessages');
                            if (messagesBox && messagesBox.children.length === 0) {
                                messagesBox.innerHTML = '<div class="no-messages"><p>💬 Aucun message pour le moment. Soyez le premier à écrire !</p></div>';
                            }
                            
                            // Scroll vers le bas
                            scrollToBottom();
                        }, 300);
                    }
                } else {
                    alert('Erreur : ' + (data.message || 'Impossible de supprimer le message'));
                }
            })
            .catch(error => {
                console.error('Erreur lors de la suppression:', error);
                alert('Erreur lors de la suppression du message');
            });
        }
    </script>
    <script src="emoji_picker.js"></script>
    <!-- Avatar Cartoon Renderer -->
    <link rel="stylesheet" href="../avatar_cartoon.css">
    <script src="../avatar_cartoon_faces.js"></script>
    <script src="../avatar_cartoon_renderer.js"></script>
    
    <script>
        // Fonction pour appliquer le recadrage à un avatar spécifique
        function applyCropToAvatar(containerId, cropParams, isPortrait = false) {
            const container = document.getElementById(containerId);
            if (!container) {
                console.warn('Container not found:', containerId);
                return;
            }
            
            // Chercher l'élément avatar-cartoon-container ou avatar-cartoon
            let avatarElement = container.querySelector('.avatar-cartoon-container');
            if (!avatarElement) {
                avatarElement = container.querySelector('.avatar-cartoon');
            }
            
            // Si toujours pas trouvé, utiliser le premier enfant direct
            if (!avatarElement && container.children.length > 0) {
                avatarElement = container.children[0];
                // Si c'est un avatar-cartoon, chercher le container à l'intérieur
                if (avatarElement && avatarElement.classList.contains('avatar-cartoon')) {
                    avatarElement = avatarElement.querySelector('.avatar-cartoon-container') || avatarElement;
                }
            }
            
            if (avatarElement) {
                const scale = cropParams.scale || 0.7;
                const translateX = cropParams.x || 0;
                const translateY = cropParams.y || -15;
                
                // Appliquer la transformation
                if (isPortrait) {
                    // Mode portrait - ajuster pour les petits avatars
                    const baseScale = 0.7;
                    const finalScale = baseScale * scale;
                    avatarElement.style.setProperty('transform', `scale(${finalScale}) translate(${translateX}%, ${translateY}%)`, 'important');
                } else {
                    // Mode complet
                    avatarElement.style.setProperty('transform', `scale(${scale}) translate(${translateX}%, ${translateY}%)`, 'important');
                }
                avatarElement.style.setProperty('transform-origin', 'center center', 'important');
                avatarElement.style.setProperty('transition', 'transform 0.2s ease-out', 'important');
            } else {
                // Si l'avatar n'est pas encore rendu, réessayer après un court délai
                setTimeout(() => {
                    applyCropToAvatar(containerId, cropParams, isPortrait);
                }, 100);
            }
        }
        
        // Fonction pour appliquer le recadrage à tous les avatars du profil
        function applyCropToAvatars() {
            const profileAvatarContainer = document.querySelector('#profile-avatar-large-render .avatar-cartoon-container');
            const profileAvatar = profileAvatarContainer || document.querySelector('#profile-avatar-large-render .avatar-cartoon');
            
            const buttonAvatarContainer = document.querySelector('#profile-avatar-render .avatar-cartoon-container');
            const buttonAvatar = buttonAvatarContainer || document.querySelector('#profile-avatar-render .avatar-cartoon');
            
            if (profileAvatar && typeof avatarCropData !== 'undefined') {
                const baseScale = 0.75;
                const finalScale = baseScale * avatarCropData.scale;
                const translateX = (avatarCropData.translateX || 0) * 0.01;
                const translateY = (avatarCropData.translateY || 0) * 0.01;
                profileAvatar.style.setProperty('transform', `translate(calc(-50% + ${translateX * 150}px), calc(-50% + ${translateY * 150}px)) scale(${finalScale})`, 'important');
                profileAvatar.style.setProperty('transform-origin', 'center center', 'important');
            }
            
            if (buttonAvatar && typeof avatarCropData !== 'undefined') {
                const baseScale = 0.45;
                const finalScale = baseScale * avatarCropData.scale;
                const translateX = (avatarCropData.translateX || 0) * 0.01;
                const translateY = (avatarCropData.translateY || 0) * 0.01;
                buttonAvatar.style.setProperty('transform', `translate(calc(-50% + ${translateX * 50}px), calc(-50% + ${translateY * 50}px)) scale(${finalScale})`, 'important');
                buttonAvatar.style.setProperty('transform-origin', 'center center', 'important');
            }
        }
    </script>
    
    <!-- Chatbot IA -->
    <link rel="stylesheet" href="chatbot.css">
    <script src="chatbot.js"></script>
    
    <!-- Profile Modal -->
    <div class="profile-modal-overlay" id="profileModal">
        <div class="profile-modal">
            <div class="profile-modal-header">
                <h2 class="profile-modal-title">Mon Profil</h2>
                <button class="profile-modal-close" onclick="closeProfileModal()">✕</button>
            </div>
            <div class="profile-avatar-section">
                <div class="profile-avatar-large" id="profileAvatarLarge">
                    <input type="file" id="profileImageInput" accept="image/*" style="display: none;" onchange="uploadProfileImage(this)">
                    <label for="profileImageInput" style="cursor: pointer; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; border-radius: 50%; position: relative;">
                        <?php if ($currentUserProfileImage): ?>
                            <img src="<?php echo htmlspecialchars($currentUserProfileImage); ?>" alt="Photo de profil" id="profileImageDisplay" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                        <?php else: ?>
                            <div style="font-size: 4rem; color: #00ff88;">👤</div>
                        <?php endif; ?>
                        <div style="position: absolute; bottom: 5px; right: 5px; background: rgba(0, 255, 136, 0.9); border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; font-size: 1rem; opacity: 0.8; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3); z-index: 10;">📷</div>
                    </label>
                </div>
                <p style="color: #888; font-size: 0.85rem; margin-top: 0.5rem; text-align: center;">
                    Cliquez pour ajouter une image de votre galerie
                </p>
            </div>
            <div class="profile-info-section">
                <div class="profile-info-item">
                    <div class="profile-info-label">User ID</div>
                    <div class="profile-info-value"><?php echo htmlspecialchars($currentUserId); ?></div>
                </div>
                <div class="profile-info-item">
                    <div class="profile-info-label">Collaboration Actuelle</div>
                    <div class="profile-info-value"><?php echo htmlspecialchars($collab['titre']); ?></div>
                </div>
                <div class="profile-info-item">
                    <div class="profile-info-label">Rôle</div>
                    <div class="profile-info-value">
                        <?php
                        $userRole = 'Membre';
                        foreach ($members as $m) {
                            if ($m['user_id'] == $currentUserId) {
                                $userRole = translateRole($m['role']);
                                break;
                            }
                        }
                        echo htmlspecialchars($userRole);
                        ?>
                    </div>
                </div>
            </div>
            <div class="profile-actions">
                <button class="profile-action-btn" onclick="window.location.href='../avatar_shop.php'">
                    🎨 Personnaliser Avatar
                </button>
                <button class="profile-action-btn" onclick="closeProfileModal()">
                    Fermer
                </button>
            </div>
        </div>
    </div>
    
    <script>
        // Fonction pour uploader l'image de profil
        function uploadProfileImage(input) {
            if (input.files && input.files[0]) {
                const file = input.files[0];
                
                // Vérifier la taille du fichier (max 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('L\'image est trop volumineuse. Taille maximale: 5MB');
                    return;
                }
                
                // Vérifier le type de fichier
                if (!file.type.match('image.*')) {
                    alert('Veuillez sélectionner une image valide');
                    return;
                }
                
                // Afficher un aperçu immédiat
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Afficher l'aperçu dans le modal
                    const profileImageDisplay = document.getElementById('profileImageDisplay');
                    if (profileImageDisplay) {
                        profileImageDisplay.src = e.target.result;
                    } else {
                        const profileAvatarLarge = document.getElementById('profileAvatarLarge');
                        if (profileAvatarLarge) {
                            profileAvatarLarge.innerHTML = '<input type="file" id="profileImageInput" accept="image/*" style="display: none;" onchange="uploadProfileImage(this)"><label for="profileImageInput" style="cursor: pointer; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; border-radius: 50%; position: relative;"><img src="' + e.target.result + '" alt="Photo de profil" id="profileImageDisplay" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;"><div style="position: absolute; bottom: 5px; right: 5px; background: rgba(0, 255, 136, 0.9); border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; font-size: 1rem; opacity: 0.8; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3); z-index: 10;">📷</div></label>';
                        }
                    }
                    
                };
                reader.readAsDataURL(file);
                
                // Uploader le fichier
                const formData = new FormData();
                formData.append('profile_image', file);
                
                fetch('upload_profile_image.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Image de profil uploadée avec succès');
                        // Mettre à jour les images avec le nouveau chemin
                        if (data.image_path) {
                            const profileImageDisplay = document.getElementById('profileImageDisplay');
                            if (profileImageDisplay) {
                                profileImageDisplay.src = data.image_path;
                            }
                        }
                    } else {
                        alert('Erreur lors de l\'upload: ' + (data.message || 'Erreur inconnue'));
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors de l\'upload de l\'image');
                });
            }
        }
        
        // Ouvrir le modal de profil
        function openProfileModal() {
            const modal = document.getElementById('profileModal');
            if (modal) {
                modal.classList.add('show');
            }
        }
        
        // Fermer le modal de profil
        function closeProfileModal() {
            const modal = document.getElementById('profileModal');
            if (modal) {
                modal.classList.remove('show');
            }
        }
        
        // Fermer au clic sur l'overlay
        document.getElementById('profileModal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closeProfileModal();
            }
        });
        
        // Fermer avec Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const cropOverlay = document.getElementById('avatarCropOverlay');
                if (cropOverlay && cropOverlay.classList.contains('show')) {
                    cancelAvatarCrop();
                } else {
                    closeProfileModal();
                }
            }
        });
        
        // ============================================
        // AVATAR CROP EDITOR
        // ============================================
        let avatarCropData = {
            scale: 0.7,
            translateX: 0,
            translateY: -15
        };
        
        function openAvatarCrop() {
            const cropOverlay = document.getElementById('avatarCropOverlay');
            if (cropOverlay) {
                cropOverlay.classList.add('show');
                // Initialiser les sliders avec les valeurs actuelles
                const scaleSlider = document.getElementById('cropScaleSlider');
                const xSlider = document.getElementById('cropXSlider');
                const ySlider = document.getElementById('cropYSlider');
                
                if (scaleSlider) scaleSlider.value = avatarCropData.scale;
                if (xSlider) xSlider.value = avatarCropData.translateX;
                if (ySlider) ySlider.value = avatarCropData.translateY;
                
                // Attendre que l'avatar soit rendu avant de mettre à jour
                setTimeout(() => {
                    updateCropPreview();
                }, 100);
            }
        }
        
        function closeAvatarCrop() {
            const cropOverlay = document.getElementById('avatarCropOverlay');
            if (cropOverlay) {
                cropOverlay.classList.remove('show');
            }
        }
        
        function updateCropPreview() {
            const container = document.getElementById('cropPreviewAvatar');
            if (!container) return;
            
            // Chercher d'abord avatar-cartoon-container (créé par le renderer)
            let previewAvatar = container.querySelector('.avatar-cartoon-container');
            
            // Si pas trouvé, chercher avatar-cartoon
            if (!previewAvatar) {
                previewAvatar = container.querySelector('.avatar-cartoon');
            }
            
            // Si toujours pas trouvé, utiliser le premier enfant direct
            if (!previewAvatar && container.children.length > 0) {
                previewAvatar = container.children[0];
                // Si c'est un avatar-cartoon, chercher le container à l'intérieur
                if (previewAvatar && previewAvatar.classList.contains('avatar-cartoon')) {
                    previewAvatar = previewAvatar.querySelector('.avatar-cartoon-container') || previewAvatar;
                }
            }
            
            if (previewAvatar) {
                const scale = avatarCropData.scale;
                const translateX = avatarCropData.translateX;
                const translateY = avatarCropData.translateY;
                
                // Appliquer la transformation avec setProperty pour forcer
                previewAvatar.style.setProperty('transform', `scale(${scale}) translate(${translateX}%, ${translateY}%)`, 'important');
                previewAvatar.style.setProperty('transform-origin', 'center top', 'important');
                previewAvatar.style.setProperty('transition', 'transform 0.1s ease-out', 'important');
                
                // Forcer le reflow pour s'assurer que la transformation est appliquée
                void previewAvatar.offsetHeight;
            } else {
                // Si l'avatar n'est pas encore rendu, attendre un peu
                setTimeout(() => {
                    updateCropPreview();
                }, 100);
            }
            
            // Mettre à jour les valeurs affichées
            const scaleValue = document.getElementById('cropScaleValue');
            const xValue = document.getElementById('cropXValue');
            const yValue = document.getElementById('cropYValue');
            
            if (scaleValue) scaleValue.textContent = Math.round(avatarCropData.scale * 100) + '%';
            if (xValue) xValue.textContent = Math.round(avatarCropData.translateX) + '%';
            if (yValue) yValue.textContent = Math.round(avatarCropData.translateY) + '%';
        }
        
        function updateCropScale(value) {
            avatarCropData.scale = parseFloat(value);
            updateCropPreview();
        }
        
        function updateCropX(value) {
            avatarCropData.translateX = parseFloat(value);
            updateCropPreview();
        }
        
        function updateCropY(value) {
            avatarCropData.translateY = parseFloat(value);
            updateCropPreview();
        }
        
        function saveAvatarCrop() {
            // Appliquer les transformations aux avatars dans le profil
            const profileAvatarContainer = document.querySelector('#profile-avatar-large-render .avatar-cartoon-container');
            const profileAvatar = profileAvatarContainer || document.querySelector('#profile-avatar-large-render .avatar-cartoon');
            
            const buttonAvatarContainer = document.querySelector('#profile-avatar-render .avatar-cartoon-container');
            const buttonAvatar = buttonAvatarContainer || document.querySelector('#profile-avatar-render .avatar-cartoon');
            
            if (profileAvatar) {
                const baseScale = 0.75;
                const finalScale = baseScale * avatarCropData.scale;
                const translateX = (avatarCropData.translateX || 0) * 0.01;
                const translateY = (avatarCropData.translateY || 0) * 0.01;
                profileAvatar.style.setProperty('transform', `translate(calc(-50% + ${translateX * 150}px), calc(-50% + ${translateY * 150}px)) scale(${finalScale})`, 'important');
                profileAvatar.style.setProperty('transform-origin', 'center center', 'important');
            }
            
            if (buttonAvatar) {
                const baseScale = 0.45;
                const finalScale = baseScale * avatarCropData.scale;
                const translateX = (avatarCropData.translateX || 0) * 0.01;
                const translateY = (avatarCropData.translateY || 0) * 0.01;
                buttonAvatar.style.setProperty('transform', `translate(calc(-50% + ${translateX * 50}px), calc(-50% + ${translateY * 50}px)) scale(${finalScale})`, 'important');
                buttonAvatar.style.setProperty('transform-origin', 'center center', 'important');
            }
            
            // Sauvegarder dans localStorage pour persister
            localStorage.setItem('avatarCropData', JSON.stringify(avatarCropData));
            
            // Sauvegarder dans la base de données via AJAX
            <?php if (isset($_SESSION['user_id'])): ?>
            fetch('save_avatar_crop.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    crop_scale: avatarCropData.scale,
                    crop_x: avatarCropData.translateX,
                    crop_y: avatarCropData.translateY
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Paramètres de recadrage sauvegardés');
                } else {
                    console.error('Erreur lors de la sauvegarde:', data.message);
                }
            })
            .catch(error => {
                console.error('Erreur lors de la sauvegarde:', error);
            });
            <?php endif; ?>
            
            closeAvatarCrop();
            
            // Afficher une notification
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                bottom: 100px;
                right: 20px;
                background: rgba(0, 255, 136, 0.2);
                border: 2px solid rgba(0, 255, 136, 0.5);
                color: #00ff88;
                padding: 0.75rem 1.5rem;
                border-radius: 10px;
                font-weight: 600;
                z-index: 10002;
                animation: slideInRight 0.3s ease-out;
            `;
            notification.textContent = '✅ Avatar ajusté avec succès';
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.animation = 'slideOutRight 0.3s ease-out';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }
        
        function cancelAvatarCrop() {
            // Restaurer les valeurs par défaut
            avatarCropData = {
                scale: 0.7,
                translateX: 0,
                translateY: -15
            };
            closeAvatarCrop();
        }
        
        // Charger les paramètres sauvegardés au chargement
        document.addEventListener('DOMContentLoaded', function() {
            const savedCropData = localStorage.getItem('avatarCropData');
            if (savedCropData) {
                try {
                    avatarCropData = JSON.parse(savedCropData);
                    // Appliquer aux avatars
                    const profileAvatar = document.querySelector('#profile-avatar-large-render .avatar-cartoon');
                    const buttonAvatar = document.querySelector('#profile-avatar-render .avatar-cartoon');
                    
                    if (profileAvatar) {
                        const baseScale = 0.65;
                        const finalScale = baseScale * avatarCropData.scale;
                        profileAvatar.style.setProperty('transform', `scale(${finalScale}) translate(${avatarCropData.translateX}%, ${avatarCropData.translateY}%)`, 'important');
                        profileAvatar.style.setProperty('transform-origin', 'center center', 'important');
                    }
                    
                    if (buttonAvatar) {
                        const baseScale = 0.4;
                        const finalScale = baseScale * avatarCropData.scale;
                        buttonAvatar.style.setProperty('transform', `scale(${finalScale}) translate(${avatarCropData.translateX}%, ${avatarCropData.translateY}%)`, 'important');
                        buttonAvatar.style.setProperty('transform-origin', 'center center', 'important');
                    }
                } catch (e) {
                    console.error('Erreur lors du chargement des paramètres de recadrage:', e);
                }
            }
        });
    </script>
    
    <!-- Avatar Crop Editor Modal -->
    <div class="avatar-crop-overlay" id="avatarCropOverlay">
        <div class="avatar-crop-container">
            <div class="avatar-crop-header">
                <h2 class="avatar-crop-title">Ajuster l'Avatar</h2>
                <button class="avatar-crop-close" onclick="closeAvatarCrop()">✕</button>
            </div>
            
            <div class="avatar-crop-preview" id="cropPreviewContainer">
                <?php if ($currentUserAvatar): ?>
                    <div id="cropPreviewAvatar"></div>
                <?php else: ?>
                    <div style="font-size: 4rem; color: #00ff88; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">👤</div>
                <?php endif; ?>
            </div>
            
            <div class="avatar-crop-controls">
                <div class="crop-control-group">
                    <label class="crop-control-label">Zoom</label>
                    <input type="range" class="crop-control-slider" id="cropScaleSlider" 
                           min="0.3" max="1.5" step="0.01" value="0.7"
                           oninput="updateCropScale(this.value)">
                    <span class="crop-control-value" id="cropScaleValue">70%</span>
                </div>
                
                <div class="crop-control-group">
                    <label class="crop-control-label">Position X</label>
                    <input type="range" class="crop-control-slider" id="cropXSlider" 
                           min="-50" max="50" step="1" value="0"
                           oninput="updateCropX(this.value)">
                    <span class="crop-control-value" id="cropXValue">0%</span>
                </div>
                
                <div class="crop-control-group">
                    <label class="crop-control-label">Position Y</label>
                    <input type="range" class="crop-control-slider" id="cropYSlider" 
                           min="-50" max="50" step="1" value="-15"
                           oninput="updateCropY(this.value)">
                    <span class="crop-control-value" id="cropYValue">-15%</span>
                </div>
            </div>
            
            <div class="avatar-crop-actions">
                <button class="crop-action-btn save" onclick="saveAvatarCrop()">
                    💾 Enregistrer
                </button>
                <button class="crop-action-btn cancel" onclick="cancelAvatarCrop()">
                    ❌ Annuler
                </button>
            </div>
        </div>
    </div>
    
    <script>
        // Rendre l'avatar dans le crop editor
        <?php if ($currentUserAvatar): ?>
        let cropAvatarRendered = false;
        
        function renderCropAvatar() {
            const cropPreviewContainer = document.getElementById('cropPreviewAvatar');
            if (!cropPreviewContainer || typeof CartoonAvatarRenderer === 'undefined') {
                return;
            }
            
            // Vider le conteneur avant de rendre
            cropPreviewContainer.innerHTML = '';
            cropAvatarRendered = false;
            
            const profileAvatarConfig = <?php echo json_encode($currentUserAvatar); ?>;
            const cropRenderer = new CartoonAvatarRenderer('cropPreviewAvatar', profileAvatarConfig);
            cropRenderer.render(true); // Portrait mode
            
            cropAvatarRendered = true;
            
            // Appliquer les paramètres de recadrage après le rendu (plusieurs tentatives)
            setTimeout(() => {
                updateCropPreview();
            }, 300);
            
            setTimeout(() => {
                updateCropPreview();
            }, 600);
        }
        
        // Rendre l'avatar quand le modal s'ouvre
        document.addEventListener('DOMContentLoaded', function() {
            const originalOpenAvatarCrop = window.openAvatarCrop;
            window.openAvatarCrop = function() {
                if (originalOpenAvatarCrop) originalOpenAvatarCrop();
                // Forcer le re-rendu à chaque ouverture
                renderCropAvatar();
            };
        });
        <?php endif; ?>
        
        // Stocker le nom d'utilisateur globalement pour le chatbot
        window.userNameForAssistant = <?php echo json_encode($userName); ?>;
        
        // Assistant IA - Animation automatique au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const container = document.getElementById("assistant-container");
                const arm = document.querySelector(".assistant-arm");
                const bubble = document.getElementById("assistant-bubble");
                const eyes = document.querySelectorAll(".assistant-eye");
                const chatbotContainer = document.getElementById("chatbot-container");
                
                if (!container || !arm || !bubble) return;
                
                // Montrer l'assistant avec animation
                container.classList.add("show");
                
                // Animation coucou
                arm.classList.add("wave");
                
                // Animation de clignement des yeux
                if (eyes.length > 0) {
                    setTimeout(function() {
                        eyes.forEach(function(eye) {
                            eye.classList.add("blink");
                            setTimeout(function() {
                                eye.classList.remove("blink");
                            }, 300);
                        });
                    }, 500);
                }
                
                // Message personnalisé
                const userName = <?php echo json_encode($userName); ?>;
                bubble.innerText = "Bonjour " + userName + " ! 👋 Comment puis-je t'aider ? 🤖";
                
                // Voix (optionnel)
                if ('speechSynthesis' in window) {
                    const speak = new SpeechSynthesisUtterance("Bonjour " + userName + " !");
                    speak.lang = "fr-FR";
                    speak.rate = 1.0;
                    speak.pitch = 1.0;
                    speechSynthesis.speak(speak);
                }
                
                // Après l'animation (2 secondes), ouvrir le chatbot et faire disparaître l'assistant
                setTimeout(function() {
                    // Retirer la classe wave
                    arm.classList.remove("wave");
                    
                    // Ouvrir le chatbot avec la même animation
                    if (chatbotContainer) {
                        chatbotContainer.classList.remove("chatbot-hidden");
                        
                        // Focus sur l'input du chatbot
                        setTimeout(function() {
                            const chatbotInput = document.getElementById("chatbot-input");
                            if (chatbotInput) {
                                chatbotInput.focus();
                            }
                        }, 300);
                    }
                    
                    // Faire disparaître l'assistant après un court délai
                    setTimeout(function() {
                        container.classList.remove("show");
                        // Réinitialiser la bulle
                        bubble.innerText = "Bonjour ! 👋";
                    }, 500);
                }, 2000);
            }, 1000); // Démarrer l'animation 1 seconde après le chargement de la page
        });
    </script>
    
    <!-- Assistant IA Container -->
    <div id="assistant-container">
        <div id="assistant-avatar">
            <div class="assistant-head">
                <div class="assistant-eye eye-left"></div>
                <div class="assistant-eye eye-right"></div>
                <div class="assistant-mouth"></div>
            </div>
            <div class="assistant-arm"></div>
        </div>
        <div id="assistant-bubble">Bonjour ! 👋</div>
    </div>
    
    <!-- Chatbot HTML -->
    <?php include 'chatbot.html'; ?>
    
    <!-- Script du template admin -->
    <script src="admin_script.js"></script>
</body>
</html>

