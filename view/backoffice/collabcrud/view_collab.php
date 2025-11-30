<?php
session_start();

// Mode développeur : permettre l'accès même sans connexion
$isLoggedIn = isset($_SESSION['user_id']);
$userId = $isLoggedIn ? $_SESSION['user_id'] : null;

// Charger controllers
require_once __DIR__ . "/../../../controller/controllercollab/CollabProjectController.php";
require_once __DIR__ . "/../../../controller/controllercollab/CollabMemberController.php";
require_once __DIR__ . "/../../../controller/controllercollab/CollabTaskController.php";
require_once __DIR__ . "/../../../controller/controllercollab/CollabMessageController.php";

$projectController = new CollabProjectController();
$memberController = new CollabMemberController();
$taskController = new CollabTaskController();
$messageController = new CollabMessageController();

// Vérifier si un message de succès/erreur doit être affiché
$showSuccessMessage = isset($_GET['updated']) && $_GET['updated'] == '1';
$showMemberDeletedMessage = isset($_GET['member_deleted']) && $_GET['member_deleted'] == '1';
$showMemberAddedMessage = isset($_GET['member_added']) && $_GET['member_added'] == '1';
$showTaskAddedMessage = isset($_GET['task_added']) && $_GET['task_added'] == '1';
$showTaskUpdatedMessage = isset($_GET['task_updated']) && $_GET['task_updated'] == '1';
$showTaskDeletedMessage = isset($_GET['task_deleted']) && $_GET['task_deleted'] == '1';
$showMessageSent = isset($_GET['message_sent']) && $_GET['message_sent'] == '1';
$showMessageUpdated = isset($_GET['message_updated']) && $_GET['message_updated'] == '1';
$showMessageDeleted = isset($_GET['message_deleted']) && $_GET['message_deleted'] == '1';
$errorMessage = '';
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'cannot_delete_owner':
            $errorMessage = 'Erreur : vous ne pouvez pas supprimer le propriétaire de la collaboration.';
            break;
        case 'cannot_delete_self':
            $errorMessage = 'Erreur : vous ne pouvez pas vous supprimer vous-même.';
            break;
        case 'task_invalid':
            $errorMessage = 'Erreur : la tâche est invalide.';
            break;
        case 'message_invalid':
            $errorMessage = 'Erreur : le message est invalide.';
            break;
        case 'message_update_invalid':
            $errorMessage = 'Erreur : impossible de mettre à jour le message.';
            break;
        default:
            $errorMessage = 'Une erreur est survenue.';
    }
}

// Vérifier si ID est passé
if (!isset($_GET['id'])) {
    die("ID du projet manquant.");
}

$collab_id = $_GET['id'];

// Récupérer info du projet
$collab = $projectController->getById($collab_id);

if (!$collab) {
    die("Projet collaboratif introuvable.");
}

// Récupérer les membres
$members = $memberController->getMembers($collab_id);

// Vérifier si l'utilisateur est membre (seulement s'il est connecté)
$isMember = false;
$isOwner = false;
if ($isLoggedIn) {
foreach ($members as $m) {
        if ($m['user_id'] == $userId) {
        $isMember = true;
        break;
    }
}
    $isOwner = ($collab['owner_id'] == $userId);
}

// Récupérer les tâches (seulement si l'utilisateur est membre ou en mode développeur)
$tasks = [];
$canViewTasks = false;
if ($isLoggedIn) {
    $canViewTasks = $isMember || $isOwner;
} else {
    // Mode développeur : permettre de voir les tâches
    $canViewTasks = true;
}

if ($canViewTasks) {
    $tasks = $taskController->getTasks($collab_id);
}

// Récupérer les messages (seulement si l'utilisateur est membre ou en mode développeur)
$messages = [];
$canViewChat = false;
if ($isLoggedIn) {
    $canViewChat = $isMember || $isOwner;
} else {
    // Mode développeur : permettre de voir le chat
    $canViewChat = true;
}

if ($canViewChat) {
    $messages = $messageController->getMessages($collab_id);
}

// En mode développeur, permettre la suppression si l'utilisateur n'est pas connecté
// (on considère que le développeur peut supprimer n'importe quelle collaboration)
$canDelete = $isOwner || !$isLoggedIn;

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($collab['titre']); ?> - GameHub Pro</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #1a1a1a;
            color: #fff;
        }
        h2 {
            color: #00ff88;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #00ff88;
            text-decoration: none;
            padding: 10px 20px;
            border: 2px solid rgba(0, 255, 136, 0.5);
            border-radius: 10px;
        }
        .back-link:hover {
            background: rgba(0, 255, 136, 0.2);
        }
        .collab-image {
            max-width: 100%;
            border-radius: 10px;
            margin: 20px 0;
        }
        .info-box {
            background: rgba(0, 255, 136, 0.1);
            border: 1px solid rgba(0, 255, 136, 0.3);
            padding: 15px;
            border-radius: 10px;
            margin: 15px 0;
        }
        .members-list {
            list-style: none;
            padding: 0;
        }
        .members-list li {
            padding: 12px;
            background: rgba(255, 255, 255, 0.1);
            margin: 5px 0;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        .members-list li:hover {
            background: rgba(255, 255, 255, 0.15);
        }
        .member-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }
        .btn-delete-member {
            padding: 6px 12px;
            background: rgba(255, 51, 92, 0.2);
            color: #ff335c;
            border: 1px solid rgba(255, 51, 92, 0.5);
            border-radius: 5px;
            font-size: 0.85rem;
            cursor: pointer;
            font-family: inherit;
            transition: all 0.3s ease;
        }
        .btn-delete-member:hover {
            background: rgba(255, 51, 92, 0.3);
            box-shadow: 0 0 10px rgba(255, 51, 92, 0.5);
        }
        .owner-badge {
            color: #ffd700;
            font-size: 0.85rem;
            margin-left: 10px;
        }
        a[href*="edit_collab"]:hover {
            background: rgba(255, 215, 0, 0.3) !important;
            box-shadow: 0 0 15px rgba(255, 215, 0, 0.5);
            transform: translateY(-2px);
        }
    </style>
    <style>
        /* Chat Styles */
        .chat-button {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: linear-gradient(135deg, #0084FF, #00C6FF);
            color: white;
            border: none;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            font-size: 24px;
            cursor: pointer;
            box-shadow: 0 4px 20px rgba(0, 132, 255, 0.4);
            transition: all 0.3s ease;
            z-index: 1000;
        }
        .chat-button:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 30px rgba(0, 132, 255, 0.6);
        }
        .chat-box {
            display: none;
            position: fixed;
            bottom: 100px;
            right: 30px;
            width: 380px;
            height: 500px;
            background: rgba(0, 0, 0, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(0, 255, 136, 0.3);
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
            z-index: 999;
            flex-direction: column;
            overflow: hidden;
        }
        .chat-box.show {
            display: flex;
        }
        .chat-header {
            background: linear-gradient(135deg, #0084FF, #00C6FF);
            color: white;
            padding: 15px;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .chat-close {
            cursor: pointer;
            font-size: 24px;
            font-weight: bold;
            transition: transform 0.3s ease;
        }
        .chat-close:hover {
            transform: rotate(90deg);
        }
        .messages-container {
            flex: 1;
            overflow-y: auto;
            padding: 15px;
            background: rgba(255, 255, 255, 0.02);
        }
        .message-item {
            margin-bottom: 15px;
            padding: 10px;
            background: rgba(0, 255, 136, 0.1);
            border-left: 3px solid #00ff88;
            border-radius: 8px;
            position: relative;
        }
        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 5px;
        }
        .message-menu {
            position: relative;
        }
        .message-menu-btn {
            background: transparent;
            border: none;
            color: #aaa;
            cursor: pointer;
            font-size: 18px;
            padding: 5px 10px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        .message-menu-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }
        .message-dropdown {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background: rgba(0, 0, 0, 0.95);
            border: 1px solid rgba(0, 255, 136, 0.3);
            border-radius: 8px;
            min-width: 150px;
            z-index: 1000;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);
        }
        .message-dropdown.show {
            display: block;
        }
        .message-dropdown-item {
            padding: 10px 15px;
            color: #fff;
            cursor: pointer;
            transition: background 0.3s ease;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .message-dropdown-item:last-child {
            border-bottom: none;
        }
        .message-dropdown-item:hover {
            background: rgba(0, 255, 136, 0.2);
        }
        .message-dropdown-item.edit {
            color: #00ffea;
        }
        .message-dropdown-item.delete {
            color: #ff335c;
        }
        /* Modal pour modifier un message */
        .edit-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 2000;
            justify-content: center;
            align-items: center;
        }
        .edit-modal.show {
            display: flex;
        }
        .edit-modal-content {
            background: rgba(0, 0, 0, 0.95);
            border: 2px solid rgba(0, 255, 136, 0.5);
            border-radius: 15px;
            padding: 20px;
            max-width: 500px;
            width: 90%;
        }
        .edit-modal-header {
            color: #00ff88;
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 15px;
        }
        .edit-modal textarea {
            width: 100%;
            padding: 10px;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(0, 255, 136, 0.3);
            border-radius: 8px;
            color: #fff;
            resize: vertical;
            min-height: 100px;
            font-family: inherit;
        }
        .edit-modal textarea:focus {
            outline: none;
            border-color: #00ff88;
            box-shadow: 0 0 10px rgba(0, 255, 136, 0.3);
        }
        .edit-modal-buttons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        .edit-modal-buttons button {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .edit-modal-buttons .btn-save {
            background: rgba(0, 255, 136, 0.2);
            color: #00ff88;
            border: 2px solid rgba(0, 255, 136, 0.5);
        }
        .edit-modal-buttons .btn-save:hover {
            background: rgba(0, 255, 136, 0.3);
        }
        .edit-modal-buttons .btn-cancel {
            background: rgba(255, 51, 92, 0.2);
            color: #ff335c;
            border: 2px solid rgba(255, 51, 92, 0.5);
        }
        .edit-modal-buttons .btn-cancel:hover {
            background: rgba(255, 51, 92, 0.3);
        }
        .message-item .message-user {
            font-weight: 600;
            color: #00ffea;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }
        .message-item .message-text {
            color: #fff;
            margin-bottom: 5px;
            word-wrap: break-word;
        }
        .message-item .message-date {
            font-size: 0.75rem;
            color: #aaa;
        }
        .chat-form {
            padding: 15px;
            background: rgba(0, 0, 0, 0.8);
            border-top: 1px solid rgba(0, 255, 136, 0.3);
        }
        .chat-form textarea {
            width: 100%;
            padding: 10px;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(0, 255, 136, 0.3);
            border-radius: 8px;
            color: #fff;
            resize: none;
            font-family: inherit;
            margin-bottom: 10px;
        }
        .chat-form textarea:focus {
            outline: none;
            border-color: #00ff88;
            box-shadow: 0 0 10px rgba(0, 255, 136, 0.3);
        }
        #chat-form-buttons {
            display: flex;
            gap: 10px;
        }
        .chat-form button {
            flex: 1;
            padding: 12px;
            background: linear-gradient(135deg, #0084FF, #00C6FF);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .chat-form button.btn-resend {
            background: linear-gradient(135deg, #00ff88, #00C6FF);
        }
        .chat-form button.btn-cancel-edit {
            background: linear-gradient(135deg, #ff335c, #ff6b8a);
            flex: 0 0 auto;
            min-width: 50px;
        }
        .chat-form button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 132, 255, 0.4);
        }
        .chat-form textarea.editing {
            border-color: #00ff88;
            box-shadow: 0 0 10px rgba(0, 255, 136, 0.5);
        }
        .messages-container::-webkit-scrollbar {
            width: 6px;
        }
        .messages-container::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }
        .messages-container::-webkit-scrollbar-thumb {
            background: rgba(0, 255, 136, 0.3);
            border-radius: 3px;
        }
        .messages-container::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 255, 136, 0.5);
        }
        @media (max-width: 768px) {
            .chat-box {
                width: calc(100% - 40px);
                right: 20px;
                left: 20px;
                height: calc(100vh - 120px);
                bottom: 90px;
            }
        }
    </style>
</head>
<body>

<a href="../../frontoffice/collaborations.php" class="back-link">← Retour aux Collaborations</a>

<?php if ($showSuccessMessage): ?>
    <div style="background: rgba(0, 255, 136, 0.2); color: #00ff88; padding: 15px; border-radius: 10px; margin-bottom: 2rem; border: 2px solid rgba(0, 255, 136, 0.5); text-align: center; font-weight: 600;">
        ✅ Collaboration mise à jour avec succès !
    </div>
    <script>
        // Supprimer le paramètre de l'URL après affichage
        setTimeout(function() {
            window.history.replaceState({}, document.title, window.location.pathname + '?id=<?php echo $collab_id; ?>');
        }, 3000);
    </script>
<?php endif; ?>

<?php if ($showMemberDeletedMessage): ?>
    <div style="background: rgba(0, 255, 136, 0.2); color: #00ff88; padding: 15px; border-radius: 10px; margin-bottom: 2rem; border: 2px solid rgba(0, 255, 136, 0.5); text-align: center; font-weight: 600;">
        ✅ Membre supprimé avec succès !
    </div>
    <script>
        // Supprimer le paramètre de l'URL après affichage
        setTimeout(function() {
            window.history.replaceState({}, document.title, window.location.pathname + '?id=<?php echo $collab_id; ?>');
        }, 3000);
    </script>
<?php endif; ?>

<?php if ($showMemberAddedMessage): ?>
    <div style="background: rgba(0, 255, 136, 0.2); color: #00ff88; padding: 15px; border-radius: 10px; margin-bottom: 2rem; border: 2px solid rgba(0, 255, 136, 0.5); text-align: center; font-weight: 600;">
        ✅ Membre ajouté avec succès !
    </div>
    <script>
        // Supprimer le paramètre de l'URL après affichage
        setTimeout(function() {
            window.history.replaceState({}, document.title, window.location.pathname + '?id=<?php echo $collab_id; ?>');
        }, 3000);
    </script>
<?php endif; ?>

<?php if (!empty($errorMessage)): ?>
    <div style="background: rgba(255, 51, 92, 0.2); color: #ff335c; padding: 15px; border-radius: 10px; margin-bottom: 2rem; border: 2px solid rgba(255, 51, 92, 0.5); text-align: center; font-weight: 600;">
        ❌ <?php echo htmlspecialchars($errorMessage); ?>
    </div>
    <script>
        // Supprimer le paramètre de l'URL après affichage
        setTimeout(function() {
            window.history.replaceState({}, document.title, window.location.pathname + '?id=<?php echo $collab_id; ?>');
        }, 3000);
    </script>
<?php endif; ?>

<?php if ($showMessageSent): ?>
    <div style="background: rgba(0, 132, 255, 0.2); color: #0084FF; padding: 15px; border-radius: 10px; margin-bottom: 2rem; border: 2px solid rgba(0, 132, 255, 0.5); text-align: center; font-weight: 600;">
        ✅ Message envoyé avec succès !
    </div>
    <script>
        setTimeout(function() {
            window.history.replaceState({}, document.title, window.location.pathname + '?id=<?php echo $collab_id; ?>');
            // Ouvrir le chat automatiquement après envoi
            if (typeof toggleChat === 'function') {
                setTimeout(toggleChat, 100);
            }
        }, 500);
    </script>
<?php endif; ?>

<?php if ($showMessageUpdated): ?>
    <div style="background: rgba(0, 255, 136, 0.2); color: #00ff88; padding: 15px; border-radius: 10px; margin-bottom: 2rem; border: 2px solid rgba(0, 255, 136, 0.5); text-align: center; font-weight: 600;">
        ✅ Message modifié avec succès !
    </div>
    <script>
        setTimeout(function() {
            window.history.replaceState({}, document.title, window.location.pathname + '?id=<?php echo $collab_id; ?>');
        }, 3000);
    </script>
<?php endif; ?>

<?php if ($showMessageDeleted): ?>
    <div style="background: rgba(255, 51, 92, 0.2); color: #ff335c; padding: 15px; border-radius: 10px; margin-bottom: 2rem; border: 2px solid rgba(255, 51, 92, 0.5); text-align: center; font-weight: 600;">
        ✅ Message supprimé avec succès !
    </div>
    <script>
        setTimeout(function() {
            window.history.replaceState({}, document.title, window.location.pathname + '?id=<?php echo $collab_id; ?>');
        }, 3000);
    </script>
<?php endif; ?>

<h2><?php echo htmlspecialchars($collab['titre']); ?></h2>

<?php if (!empty($collab['image'])) { ?>
    <img src="<?php echo htmlspecialchars($collab['image']); ?>" class="collab-image" alt="<?php echo htmlspecialchars($collab['titre']); ?>"><br><br>
<?php } ?>

<div class="info-box">
<b>Description :</b><br>
    <?php echo nl2br(htmlspecialchars($collab['description'])); ?>
</div>

<div class="info-box">
    <b>Statut :</b> <?php echo htmlspecialchars(ucfirst($collab['statut'])); ?><br>
<b>Nombre maximum de membres :</b> <?php echo $collab['max_membres']; ?><br>
    <b>Membres actuels :</b> <?php echo count($members); ?> / <?php echo $collab['max_membres']; ?>
</div>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
    <h3 style="margin: 0;">Liste des membres :</h3>
    <?php if (($isOwner || !$isLoggedIn) && count($members) < $collab['max_membres']): ?>
        <a href="create_member.php?collab_id=<?php echo $collab_id; ?>" style="padding: 8px 16px; background: rgba(0, 255, 136, 0.2); color: #00ff88; text-decoration: none; border-radius: 8px; border: 2px solid rgba(0, 255, 136, 0.5); font-weight: 600; font-size: 0.9rem; transition: all 0.3s ease;">
            ➕ Ajouter un membre
        </a>
    <?php elseif (count($members) >= $collab['max_membres']): ?>
        <span style="color: #ff335c; font-size: 0.9rem;">✗ Projet complet</span>
    <?php endif; ?>
</div>
<ul class="members-list">
<?php 
if (empty($members)) {
    echo "<li>Aucun membre pour le moment.</li>";
} else {
foreach ($members as $m) {
        $roleLabel = ucfirst($m['role']);
        $isCurrentUser = ($isLoggedIn && $m['user_id'] == $userId);
        $isOwnerMember = ($m['role'] == 'owner');
        
        echo "<li>";
        echo "<div class=\"member-item\">";
        echo "<span><b>User ID : " . htmlspecialchars($m['user_id']) . "</b> - Role : " . htmlspecialchars($roleLabel) . "</span>";
        
        // Bouton supprimer visible seulement pour le propriétaire de la collaboration
        // Ne pas permettre de supprimer le propriétaire lui-même
        if (($isOwner || !$isLoggedIn) && !$isOwnerMember) {
            echo "<form action=\"delete_member.php\" method=\"POST\" style=\"display: inline; margin-left: 10px;\">";
            echo "<input type=\"hidden\" name=\"member_id\" value=\"" . $m['id'] . "\">";
            echo "<input type=\"hidden\" name=\"collab_id\" value=\"" . $collab_id . "\">";
            if (!$isLoggedIn) {
                echo "<input type=\"hidden\" name=\"dev_mode\" value=\"1\">";
            }
            echo "<button type=\"submit\" onclick=\"return confirm('Voulez-vous vraiment supprimer ce membre ?');\" class=\"btn-delete-member\">🗑️ Supprimer</button>";
            echo "</form>";
        } elseif ($isOwnerMember) {
            echo "<span class=\"owner-badge\">👑 Propriétaire</span>";
        }
        
        echo "</div>";
        echo "</li>";
    }
}
?>
</ul>

<?php if ($canViewTasks): ?>
<hr style="margin: 2rem 0; border-color: rgba(0, 255, 136, 0.3);">

<!-- Section To-Do List -->
<div style="margin-top: 2rem;">
    <h3 style="color: #00ff88; margin-bottom: 1.5rem; text-shadow: 0 0 10px rgba(0, 255, 136, 0.5);">
        📋 Tableau des tâches (To-Do List)
    </h3>

    <!-- Formulaire d'ajout -->
    <form action="task_add.php" method="POST" style="margin-bottom: 2rem; display: flex; gap: 1rem; align-items: center;">
        <input type="hidden" name="collab_id" value="<?php echo $collab_id; ?>">
        <input type="text" name="task" placeholder="Nouvelle tâche..." required 
               style="flex: 1; padding: 12px; background: rgba(255, 255, 255, 0.1); border: 2px solid rgba(0, 255, 136, 0.3); border-radius: 10px; color: #fff; font-size: 1rem;">
        <button type="submit" 
                style="padding: 12px 24px; background: rgba(0, 255, 136, 0.2); color: #00ff88; border: 2px solid rgba(0, 255, 136, 0.5); border-radius: 10px; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
            ➕ Ajouter
        </button>
    </form>

    <!-- Tableau des tâches -->
    <?php if (empty($tasks)): ?>
        <div style="background: rgba(255, 255, 255, 0.05); padding: 2rem; border-radius: 10px; text-align: center; color: #aaa;">
            <p>Aucune tâche pour le moment. Ajoutez votre première tâche !</p>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; background: rgba(0, 0, 0, 0.5); border-radius: 10px; overflow: hidden;">
                <thead>
                    <tr style="background: rgba(0, 255, 136, 0.2);">
                        <th style="padding: 15px; text-align: left; color: #00ff88; border-bottom: 2px solid rgba(0, 255, 136, 0.3);">Tâche</th>
                        <th style="padding: 15px; text-align: center; color: #00ff88; border-bottom: 2px solid rgba(0, 255, 136, 0.3);">Status</th>
                        <th style="padding: 15px; text-align: center; color: #00ff88; border-bottom: 2px solid rgba(0, 255, 136, 0.3);">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tasks as $t): ?>
                    <tr style="border-bottom: 1px solid rgba(0, 255, 136, 0.1); transition: background 0.3s ease;" 
                        onmouseover="this.style.background='rgba(0, 255, 136, 0.05)'" 
                        onmouseout="this.style.background='transparent'">
                        <td style="padding: 15px; color: #fff;">
                            <?php $isTaskDone = ($t['done'] == 1 || $t['done'] === true || $t['done'] === '1'); ?>
                            <?php if ($isTaskDone): ?>
                                <span style="text-decoration: line-through; opacity: 0.6;"><?php echo htmlspecialchars($t['task']); ?></span>
                            <?php else: ?>
                                <?php echo htmlspecialchars($t['task']); ?>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 15px; text-align: center;">
                            <?php if ($isTaskDone): ?>
                                <span style="color: #00ff88; font-weight: 600;">✓ Terminée</span>
                            <?php else: ?>
                                <span style="color: #ff335c; font-weight: 600;">⏳ À faire</span>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 15px; text-align: center;">
                            <div style="display: flex; gap: 10px; justify-content: center; align-items: center;">
                                <?php if (!$isTaskDone): ?>
                                    <a href="task_done.php?id=<?php echo $t['id']; ?>&collab_id=<?php echo $collab_id; ?>" 
                                       style="padding: 6px 12px; background: rgba(0, 255, 136, 0.2); color: #00ff88; text-decoration: none; border-radius: 5px; border: 1px solid rgba(0, 255, 136, 0.5); font-size: 0.85rem; transition: all 0.3s ease;"
                                       onmouseover="this.style.background='rgba(0, 255, 136, 0.3)'"
                                       onmouseout="this.style.background='rgba(0, 255, 136, 0.2)'">
                                        ✓ Marquer terminée
                                    </a>
                                <?php endif; ?>
                                <?php if ($isOwner || !$isLoggedIn): ?>
                                    <a href="task_delete.php?id=<?php echo $t['id']; ?>&collab_id=<?php echo $collab_id; ?>" 
                                       onclick="return confirm('Voulez-vous vraiment supprimer cette tâche ?');"
                                       style="padding: 6px 12px; background: rgba(255, 51, 92, 0.2); color: #ff335c; text-decoration: none; border-radius: 5px; border: 1px solid rgba(255, 51, 92, 0.5); font-size: 0.85rem; transition: all 0.3s ease;"
                                       onmouseover="this.style.background='rgba(255, 51, 92, 0.3)'"
                                       onmouseout="this.style.background='rgba(255, 51, 92, 0.2)'">
                                        🗑️ Supprimer
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<hr>

<div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 20px;">
    <!-- Bouton Modifier - Visible par tous -->
    <a href="edit_collab.php?id=<?php echo $collab_id; ?>" style="padding: 12px 24px; background: rgba(255, 215, 0, 0.2); color: #ffd700; text-decoration: none; border-radius: 10px; border: 2px solid rgba(255, 215, 0, 0.5); font-weight: 600; transition: all 0.3s ease; display: inline-block;">
        ✏️ Modifier
    </a>

<?php
    // Actions disponibles seulement si l'utilisateur est connecté
    if ($isLoggedIn) {
// Le user n'est pas membre → bouton "Rejoindre"
        if (!$isMember && !$isOwner && count($members) < $collab['max_membres']) {
?>
            <form action="join_collab.php" method="POST" style="display: inline;">
        <input type="hidden" name="collab_id" value="<?php echo $collab_id; ?>">
                <button type="submit" style="padding: 12px 24px; background: rgba(0, 255, 136, 0.2); color: #00ff88; border: 2px solid rgba(0, 255, 136, 0.5); border-radius: 10px; font-weight: 600; cursor: pointer; font-family: inherit;">
                    ➕ Rejoindre ce projet
                </button>
            </form>
        <?php 
        } elseif ($isMember) {
            echo "<span style=\"padding: 12px 24px; background: rgba(0, 255, 234, 0.2); color: #00ffea; border-radius: 10px; border: 2px solid rgba(0, 255, 234, 0.5); font-weight: 600; display: inline-block;\">✓ Déjà membre</span>";
        } elseif (count($members) >= $collab['max_membres']) {
            echo "<span style=\"padding: 12px 24px; background: rgba(255, 51, 92, 0.2); color: #ff335c; border-radius: 10px; border: 2px solid rgba(255, 51, 92, 0.5); font-weight: 600; display: inline-block;\">✗ Complet</span>";
        }
    }
    
    // Bouton Supprimer - Visible pour le propriétaire connecté ou en mode développeur
    if ($canDelete) {
    ?>
        <form action="delete_collab.php" method="POST" style="display: inline;">
            <input type="hidden" name="id" value="<?php echo $collab_id; ?>">
            <?php if (!$isLoggedIn): ?>
                <input type="hidden" name="dev_mode" value="1">
            <?php endif; ?>
            <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette collaboration ?<?php echo !$isLoggedIn ? ' (Mode développeur)' : ''; ?>');" style="padding: 12px 24px; background: rgba(255, 51, 92, 0.2); color: #ff335c; border: 2px solid rgba(255, 51, 92, 0.5); border-radius: 10px; font-weight: 600; cursor: pointer; font-family: inherit; transition: all 0.3s ease;" title="<?php echo !$isLoggedIn ? 'Mode développeur - Suppression autorisée' : 'Seul le propriétaire peut supprimer'; ?>" onmouseover="this.style.background='rgba(255, 51, 92, 0.3)'; this.style.boxShadow='0 0 15px rgba(255, 51, 92, 0.5)';" onmouseout="this.style.background='rgba(255, 51, 92, 0.2)'; this.style.boxShadow='none';">
                🗑️ Supprimer<?php echo !$isLoggedIn ? ' (Dev)' : ''; ?>
            </button>
    </form>
<?php 
}

    if (!$isLoggedIn) {
        // Mode développeur - permettre de rejoindre même sans connexion
        if (count($members) < $collab['max_membres']) {
        ?>
            <form action="join_collab.php" method="POST" style="display: inline;">
                <input type="hidden" name="collab_id" value="<?php echo $collab_id; ?>">
                <input type="hidden" name="user_id" value="1">
                <button type="submit" onclick="return confirm('Voulez-vous rejoindre cette collaboration ? (Mode développeur - User ID: 1)');" style="padding: 12px 24px; background: rgba(0, 255, 136, 0.2); color: #00ff88; border: 2px solid rgba(0, 255, 136, 0.5); border-radius: 10px; font-weight: 600; cursor: pointer; font-family: inherit;" title="Mode développeur">
                    ➕ Rejoindre (Mode dev)
                </button>
    </form>
<?php
        } else {
            echo "<span style=\"padding: 12px 24px; background: rgba(255, 51, 92, 0.2); color: #ff335c; border-radius: 10px; border: 2px solid rgba(255, 51, 92, 0.5); font-weight: 600; display: inline-block;\">✗ Complet</span>";
        }
    }
    ?>
</div>

<?php if ($canViewChat): ?>
<!-- BOUTON POUR OUVRIR LE CHAT -->
<button onclick="toggleChat()" class="chat-button" title="Ouvrir le chat">
    💬
</button>

<!-- FENÊTRE DE CHAT -->
<div id="chatBox" class="chat-box">
    <!-- HEADER DU CHAT -->
    <div class="chat-header">
        <span><b>💬 Chat du projet</b></span>
        <span onclick="toggleChat()" class="chat-close">×</span>
    </div>
    
    <!-- LISTE DES MESSAGES -->
    <div class="messages-container" id="messagesBox">
        <?php if (empty($messages)): ?>
            <div style="text-align: center; color: #aaa; padding: 2rem;">
                <p>Aucun message pour le moment. Soyez le premier à écrire !</p>
            </div>
        <?php else: ?>
            <?php foreach ($messages as $m): 
                // Vérifier si l'utilisateur peut modifier/supprimer ce message
                $canEditMessage = false;
                $canDeleteMessage = false;
                $currentUserId = $isLoggedIn ? $userId : 1;
                
                if ($isLoggedIn) {
                    $canEditMessage = ($m['user_id'] == $currentUserId || $isOwner);
                    $canDeleteMessage = ($m['user_id'] == $currentUserId || $isOwner);
                } else {
                    // Mode développeur : peut tout modifier/supprimer
                    $canEditMessage = true;
                    $canDeleteMessage = true;
                }
            ?>
                <div class="message-item">
                    <div class="message-header">
                        <div class="message-user">👤 User <?php echo htmlspecialchars($m['user_id']); ?></div>
                        <?php if ($canEditMessage || $canDeleteMessage): ?>
                            <div class="message-menu">
                                <button class="message-menu-btn" onclick="toggleMessageMenu(<?php echo $m['id']; ?>)">⋮</button>
                                <div class="message-dropdown" id="menu-<?php echo $m['id']; ?>">
                                    <?php if ($canEditMessage): ?>
                                        <a href="update_message.php?id=<?php echo $m['id']; ?>&collab_id=<?php echo $collab_id; ?>" class="message-dropdown-item edit" style="text-decoration: none; display: block;" onclick="event.stopPropagation();">
                                            ✏️ Modifier
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($canDeleteMessage): ?>
                                        <div class="message-dropdown-item delete" onclick="event.stopPropagation(); deleteMessage(<?php echo $m['id']; ?>, <?php echo $collab_id; ?>);">
                                            🗑️ Supprimer
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="message-text" id="message-text-<?php echo $m['id']; ?>"><?php echo nl2br(htmlspecialchars($m['message'])); ?></div>
                    <div class="message-date">📅 <?php echo htmlspecialchars($m['date_message'] ?? 'Date inconnue'); ?></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <!-- FORMULAIRE D'ENVOI -->
    <form id="chatMessageForm" action="send_message.php" method="POST" class="chat-form">
        <input type="hidden" name="collab_id" value="<?php echo $collab_id; ?>">
        <input type="hidden" name="message_id" id="editing-message-id" value="">
        <textarea name="message" id="chat-message-input" rows="2" placeholder="Tapez votre message..." required></textarea>
        <div id="chat-form-buttons">
            <button type="submit" id="btn-send-message" class="btn-send">📤 Envoyer</button>
            <button type="button" id="btn-cancel-edit" class="btn-cancel-edit" style="display: none;" onclick="cancelEditMessage()">❌ Annuler</button>
            <button type="button" id="btn-resend-message" class="btn-resend" style="display: none;" onclick="resendMessage()">🔄 Renvoyer</button>
        </div>
        <div id="edit-mode-indicator" style="display: none; text-align: center; color: #00ff88; font-size: 0.85rem; margin-top: 5px;">
            ✏️ Mode édition activé
        </div>
    </form>
</div>

<script>
function toggleChat() {
    const chatBox = document.getElementById('chatBox');
    if (chatBox) {
        chatBox.classList.toggle('show');
        // Faire défiler vers le bas après ouverture
        if (chatBox.classList.contains('show')) {
            setTimeout(() => {
                const messagesBox = document.getElementById('messagesBox');
                if (messagesBox) {
                    messagesBox.scrollTop = messagesBox.scrollHeight;
                }
            }, 100);
        }
    }
}

// Auto-scroll vers le bas des messages
document.addEventListener('DOMContentLoaded', function() {
    const messagesBox = document.getElementById('messagesBox');
    if (messagesBox) {
        messagesBox.scrollTop = messagesBox.scrollHeight;
    }
});

// Fonction pour afficher/masquer le menu déroulant
function toggleMessageMenu(messageId) {
    const menu = document.getElementById('menu-' + messageId);
    if (!menu) return;
    
    // Fermer tous les autres menus
    document.querySelectorAll('.message-dropdown').forEach(m => {
        if (m.id !== 'menu-' + messageId) {
            m.classList.remove('show');
        }
    });
    
    // Toggle le menu actuel
    menu.classList.toggle('show');
}

// Fermer le menu si on clique ailleurs
document.addEventListener('click', function(event) {
    if (!event.target.closest('.message-menu')) {
        document.querySelectorAll('.message-dropdown').forEach(menu => {
            menu.classList.remove('show');
        });
    }
});

// Fonction pour modifier un message - place le message dans la zone de saisie
function editMessage(messageId, currentMessage, collabId) {
    console.log('editMessage appelée:', { messageId, currentMessage, collabId });
    
    // S'assurer que le chat est ouvert
    const chatBox = document.getElementById('chatBox');
    if (chatBox && !chatBox.classList.contains('show')) {
        chatBox.classList.add('show');
    }
    
    // Fermer le menu
    document.querySelectorAll('.message-dropdown').forEach(menu => {
        menu.classList.remove('show');
    });
    
    // Attendre un peu pour que le DOM soit prêt
    setTimeout(() => {
        // Récupérer les éléments du formulaire
        const chatForm = document.getElementById('chatMessageForm');
        const messageInput = document.getElementById('chat-message-input');
        const messageIdInput = document.getElementById('editing-message-id');
        const btnSend = document.getElementById('btn-send-message');
        const btnResend = document.getElementById('btn-resend-message');
        const btnCancel = document.getElementById('btn-cancel-edit');
        
        if (!chatForm) {
            console.error('Formulaire chatMessageForm non trouvé!');
            alert('Erreur: Formulaire de chat non trouvé. Assurez-vous que le chat est ouvert.');
            return;
        }
        
        if (!messageInput) {
            console.error('Textarea chat-message-input non trouvé!');
            alert('Erreur: Zone de saisie non trouvée.');
            return;
        }
        
        if (!messageIdInput) {
            console.error('Input editing-message-id non trouvé!');
            alert('Erreur: Champ ID de message non trouvé.');
            return;
        }
        
        // Stocker l'ID du message en édition
        messageIdInput.value = messageId;
        
        // Remplir le textarea avec le message à modifier
        messageInput.value = currentMessage;
        messageInput.classList.add('editing');
        messageInput.placeholder = 'Modifiez votre message...';
        
        // Changer l'action du formulaire pour la mise à jour
        chatForm.setAttribute('data-original-action', chatForm.action);
        chatForm.setAttribute('data-editing-message-id', messageId);
        
        // Afficher/masquer les boutons
        if (btnSend) {
            btnSend.style.display = 'none';
        }
        if (btnResend) {
            btnResend.style.display = 'inline-block';
        }
        if (btnCancel) {
            btnCancel.style.display = 'inline-block';
        }
        
        // Afficher l'indicateur de mode édition
        const editIndicator = document.getElementById('edit-mode-indicator');
        if (editIndicator) {
            editIndicator.style.display = 'block';
        }
        
        // Focus sur le textarea et sélectionner le texte
        messageInput.focus();
        setTimeout(() => {
            messageInput.select();
        }, 50);
        
        // Faire défiler vers le bas pour voir la zone de saisie
        const messagesBox = document.getElementById('messagesBox');
        if (messagesBox) {
            messagesBox.scrollTop = messagesBox.scrollHeight;
        }
        
        console.log('Message placé dans la zone de saisie:', currentMessage);
    }, 100);
}

// Fonction pour annuler l'édition d'un message
function cancelEditMessage() {
    const messageInput = document.getElementById('chat-message-input');
    const messageIdInput = document.getElementById('editing-message-id');
    const chatForm = document.getElementById('chatMessageForm');
    const btnSend = document.getElementById('btn-send-message');
    const btnResend = document.getElementById('btn-resend-message');
    const btnCancel = document.getElementById('btn-cancel-edit');
    
    // Réinitialiser le formulaire
    if (messageInput) {
        messageInput.value = '';
        messageInput.classList.remove('editing');
        messageInput.placeholder = 'Tapez votre message...';
    }
    
    if (messageIdInput) {
        messageIdInput.value = '';
    }
    
    // Restaurer l'action originale du formulaire
    if (chatForm) {
        const originalAction = chatForm.getAttribute('data-original-action');
        if (originalAction) {
            chatForm.action = originalAction;
        } else {
            chatForm.action = 'send_message.php';
        }
        chatForm.removeAttribute('data-editing-message-id');
    }
    
    // Afficher/masquer les boutons
    if (btnSend) btnSend.style.display = 'inline-block';
    if (btnResend) btnResend.style.display = 'none';
    if (btnCancel) btnCancel.style.display = 'none';
    
    // Masquer l'indicateur de mode édition
    const editIndicator = document.getElementById('edit-mode-indicator');
    if (editIndicator) {
        editIndicator.style.display = 'none';
    }
}

// Fonction pour renvoyer un message modifié
function resendMessage() {
    console.log('resendMessage appelée');
    
    const chatForm = document.getElementById('chatMessageForm');
    const messageInput = document.getElementById('chat-message-input');
    const messageIdInput = document.getElementById('editing-message-id');
    
    if (!chatForm) {
        alert('Erreur: Formulaire non trouvé.');
        console.error('chatForm non trouvé');
        return;
    }
    
    if (!messageInput) {
        alert('Erreur: Zone de saisie non trouvée.');
        console.error('messageInput non trouvé');
        return;
    }
    
    if (!messageIdInput) {
        alert('Erreur: ID de message non trouvé.');
        console.error('messageIdInput non trouvé');
        return;
    }
    
    const messageText = messageInput.value.trim();
    const messageId = messageIdInput.value;
    
    console.log('Données à envoyer:', { messageId, messageText });
    
    if (!messageText) {
        alert('Veuillez entrer un message.');
        messageInput.focus();
        return;
    }
    
    if (!messageId) {
        alert('Erreur: ID de message manquant.');
        return;
    }
    
    // Récupérer le collab_id
    const collabIdInput = chatForm.querySelector('input[name="collab_id"]');
    const collabId = collabIdInput ? collabIdInput.value : '';
    
    if (!collabId) {
        alert('Erreur: ID de collaboration manquant.');
        return;
    }
    
    console.log('Soumission vers update_message.php avec:', { id: messageId, message: messageText, collab_id: collabId });
    
    // Créer un formulaire dynamique pour la mise à jour
    const updateForm = document.createElement('form');
    updateForm.method = 'POST';
    updateForm.action = 'update_message.php';
    updateForm.style.display = 'none';
    
    // Ajouter les champs cachés
    const idField = document.createElement('input');
    idField.type = 'hidden';
    idField.name = 'id';
    idField.value = messageId;
    updateForm.appendChild(idField);
    
    const collabIdField = document.createElement('input');
    collabIdField.type = 'hidden';
    collabIdField.name = 'collab_id';
    collabIdField.value = collabId;
    updateForm.appendChild(collabIdField);
    
    const messageField = document.createElement('input');
    messageField.type = 'hidden';
    messageField.name = 'message';
    messageField.value = messageText;
    updateForm.appendChild(messageField);
    
    // Ajouter le formulaire au DOM et le soumettre
    document.body.appendChild(updateForm);
    console.log('Formulaire créé, soumission...');
    updateForm.submit();
}

// Fonction pour supprimer un message
function deleteMessage(messageId, collabId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce message ?')) {
        window.location.href = 'delete_message.php?id=' + messageId + '&collab_id=' + collabId;
    }
}
</script>
<?php endif; ?>

</body>
</html>
