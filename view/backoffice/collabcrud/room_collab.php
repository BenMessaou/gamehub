<?php
session_start();

// Mode développeur : permettre l'accès même sans connexion
$isLoggedIn = isset($_SESSION['user_id']);
$userId = $isLoggedIn ? $_SESSION['user_id'] : null;

// Charger controllers
require_once __DIR__ . "/../../../controller/controllercollab/CollabProjectController.php";
require_once __DIR__ . "/../../../controller/controllercollab/CollabMemberController.php";
require_once __DIR__ . "/../../../controller/controllercollab/CollabMessageController.php";

$projectController = new CollabProjectController();
$memberController = new CollabMemberController();
$messageController = new CollabMessageController();

// Vérifier si ID est passé
if (!isset($_GET['id'])) {
    die("Missing project ID.");
}

$collab_id = $_GET['id'];

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

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Collab - <?php echo htmlspecialchars($collab['titre']); ?> - GameHub Pro</title>
    <link rel="stylesheet" href="../../frontoffice/collaborations.css">
    <link rel="stylesheet" href="emoji_picker.css">
    <style>
        body {
            padding-top: 100px;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 50%, #16213e 100%);
            min-height: 100vh;
        }
        .room-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        .room-header {
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 2px solid rgba(0, 255, 136, 0.3);
            box-shadow: 0 10px 30px rgba(0, 255, 136, 0.2);
        }
        .room-header h1 {
            color: #00ff88;
            margin: 0 0 1rem 0;
            text-shadow: 0 0 20px rgba(0, 255, 136, 0.5);
            font-size: 2rem;
        }
        .room-header .collab-info {
            color: #00ffea;
            margin: 0.5rem 0;
        }
        .room-header .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            margin-top: 1rem;
        }
        .status-badge.complete {
            background: rgba(0, 255, 136, 0.2);
            color: #00ff88;
            border: 2px solid rgba(0, 255, 136, 0.5);
        }
        .members-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
        .member-card {
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 1.5rem;
            border: 2px solid rgba(0, 255, 136, 0.3);
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 255, 136, 0.1);
        }
        .member-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 255, 136, 0.3);
            border-color: #00ff88;
        }
        .member-card.owner {
            border-color: rgba(255, 215, 0, 0.5);
            background: rgba(255, 215, 0, 0.05);
        }
        .member-card.owner:hover {
            border-color: #ffd700;
            box-shadow: 0 10px 30px rgba(255, 215, 0, 0.3);
        }
        .member-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #00ff88, #00ffea);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: bold;
            color: #000;
            margin: 0 auto 1rem;
            border: 3px solid rgba(0, 255, 136, 0.5);
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
            border: 2px solid rgba(0, 255, 136, 0.5);
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .back-link:hover {
            background: rgba(0, 255, 136, 0.2);
        }
        .members-section {
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 2rem;
            border: 2px solid rgba(0, 255, 136, 0.3);
            box-shadow: 0 10px 30px rgba(0, 255, 136, 0.2);
        }
        .members-section h2 {
            color: #00ff88;
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 1.8rem;
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
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 2rem;
            margin-top: 2rem;
            border: 2px solid rgba(0, 255, 136, 0.3);
            box-shadow: 0 10px 30px rgba(0, 255, 136, 0.2);
        }
        
        .chat-section h2 {
            color: #00ff88;
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 1.8rem;
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
            gap: 1rem;
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
            padding: 0.75rem 1rem;
            background: rgba(0, 255, 136, 0.1);
            border-radius: 10px;
            border: 1px solid rgba(0, 255, 136, 0.2);
            transition: all 0.3s ease;
        }
        
        .message-item:hover {
            background: rgba(0, 255, 136, 0.15);
            border-color: rgba(0, 255, 136, 0.4);
        }
        
        .message-item.own-message {
            background: rgba(0, 255, 234, 0.15);
            border-color: rgba(0, 255, 234, 0.3);
            align-self: flex-end;
            max-width: 70%;
        }
        
        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
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
            line-height: 1.5;
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
        
        .chat-input {
            flex: 1;
            padding: 0.75rem;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(0, 255, 136, 0.3);
            border-radius: 10px;
            color: #fff;
            font-family: inherit;
            font-size: 0.95rem;
            resize: none;
            min-height: 50px;
            max-height: 120px;
        }
        
        .chat-input:focus {
            outline: none;
            border-color: #00ff88;
            box-shadow: 0 0 10px rgba(0, 255, 136, 0.3);
        }
        
        .chat-input::placeholder {
            color: #888;
        }
        
        .chat-send-btn {
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #00ff88, #00ffea);
            color: #000;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1rem;
            white-space: nowrap;
        }
        
        .chat-send-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(0, 255, 136, 0.5);
        }
        
        .chat-send-btn:active {
            transform: translateY(0);
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
        
        @media (max-width: 768px) {
            .chat-container {
                height: 400px;
            }
            
            .message-item.own-message {
                max-width: 85%;
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
        </div>
    </header>

    <main class="main-content">
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
                            $initials = 'O'; // Initiales pour Owner
                        ?>
                            <div class="member-card owner">
                                <div class="member-avatar">👑</div>
                                <div class="member-name">Propriétaire</div>
                                <div class="member-role"><?php echo translateRole($ownerMember['role']); ?></div>
                                <div class="member-id">User ID: <?php echo htmlspecialchars($ownerMember['user_id']); ?></div>
                            </div>
                        <?php endif; ?>
                        
                        <?php 
                        // Afficher les autres membres
                        foreach ($otherMembers as $member):
                            $initials = 'U' . substr($member['user_id'], 0, 1); // Initiales basées sur user_id
                        ?>
                            <div class="member-card">
                                <div class="member-avatar"><?php echo $initials; ?></div>
                                <div class="member-name">Membre #<?php echo htmlspecialchars($member['user_id']); ?></div>
                                <div class="member-role"><?php echo translateRole($member['role']); ?></div>
                                <div class="member-id">User ID: <?php echo htmlspecialchars($member['user_id']); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Section Chat -->
            <?php if ($canViewChat): ?>
            <div class="chat-section">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h2 style="margin: 0;">💬 Chat de la Collaboration</h2>
                    <a href="moderation_dashboard.php?collab_id=<?php echo $collab_id; ?>" style="padding: 0.5rem 1rem; background: rgba(255, 51, 92, 0.2); color: #ff335c; text-decoration: none; border-radius: 8px; border: 1px solid rgba(255, 51, 92, 0.5); font-size: 0.85rem; font-weight: 600;">
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
                                <div class="message-item <?php echo $isOwnMessage ? 'own-message' : ''; ?>">
                                    <div class="message-header">
                                        <span class="message-user"><?php echo htmlspecialchars($memberName); ?></span>
                                        <span class="message-date"><?php echo $messageDate; ?></span>
                                    </div>
                                    <div class="message-text">
                                        <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <div class="chat-form-container">
                        <form action="send_message.php" method="POST" class="chat-form" id="chatForm">
                            <input type="hidden" name="collab_id" value="<?php echo $collab_id; ?>">
                            <input type="hidden" name="redirect_to" value="room_collab">
                            <div style="display: flex; align-items: flex-end; gap: 0.5rem;">
                                <textarea 
                                    name="message" 
                                    class="chat-input" 
                                    id="chatMessageInput"
                                    rows="2" 
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

    <script>
        // Auto-scroll vers le bas des messages
        function scrollToBottom() {
            const messagesBox = document.getElementById('chatMessages');
            if (messagesBox) {
                messagesBox.scrollTop = messagesBox.scrollHeight;
            }
        }
        
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
                    messageDiv.innerHTML = `
                        <div class="message-header">
                            <span class="message-user">${escapeHtml(memberName)}</span>
                            <span class="message-date">${messageDate}</span>
                        </div>
                        <div class="message-text">${escapeHtml(msg.message).replace(/\n/g, '<br>')}</div>
                    `;
                    messagesBox.appendChild(messageDiv);
                });
            }
            
            // Restaurer la position de scroll si on était en bas
            if (wasAtBottom) {
                setTimeout(scrollToBottom, 100);
            }
        }
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Démarrer l'auto-refresh
        <?php if ($canViewChat): ?>
        startAutoRefresh();
        <?php endif; ?>
        
        // Réinitialiser le formulaire après envoi
        document.getElementById('chatForm')?.addEventListener('submit', function(e) {
            const input = document.getElementById('chatMessageInput');
            if (input && input.value.trim()) {
                // Le formulaire sera soumis normalement
                // Le rafraîchissement se fera après le rechargement de la page
            }
        });
    </script>
    <script src="emoji_picker.js"></script>
</body>
</html>

