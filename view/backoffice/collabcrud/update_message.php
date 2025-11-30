<?php
session_start();

// Mode développeur : permettre l'accès même sans connexion
$isLoggedIn = isset($_SESSION['user_id']);
$defaultUserId = 1;

require_once __DIR__ . "/../../../controller/controllercollab/CollabMessageController.php";
require_once __DIR__ . "/../../../controller/controllercollab/CollabProjectController.php";

// Si c'est une requête POST, traiter la mise à jour
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $newMessage = trim($_POST['message'] ?? '');
    $collab_id = isset($_POST['collab_id']) ? intval($_POST['collab_id']) : 0;

    if ($id <= 0 || empty($newMessage) || $collab_id <= 0) {
        header("Location: view_collab.php?id=" . $collab_id . "&error=message_update_invalid");
        exit;
    }

    $controller = new CollabMessageController();
    $message = $controller->getMessageById($id);

    if (!$message) {
        die("Message introuvable.");
    }

    // Vérifier les permissions : seul l'auteur du message ou le propriétaire peut modifier
    $user_id = $isLoggedIn ? $_SESSION['user_id'] : $defaultUserId;
    
    if ($isLoggedIn) {
        $projectController = new CollabProjectController();
        $collab = $projectController->getById($collab_id);
        
        if (!$collab) {
            die("Projet introuvable.");
        }
        
        $isOwner = ($collab['owner_id'] == $user_id);
        $isAuthor = ($message['user_id'] == $user_id);
        
        if (!$isOwner && !$isAuthor) {
            die("Erreur : vous n'avez pas la permission de modifier ce message.");
        }
    }

    $controller->updateMessage($id, $newMessage);
    header("Location: view_collab.php?id=" . $collab_id . "&message_updated=1");
    exit;
}

// Si c'est une requête GET, afficher le formulaire d'édition
if ($_SERVER['REQUEST_METHOD'] === "GET" && isset($_GET['id']) && isset($_GET['collab_id'])) {
    $message_id = intval($_GET['id']);
    $collab_id = intval($_GET['collab_id']);

    $messageController = new CollabMessageController();
    $message = $messageController->getMessageById($message_id);

    if (!$message) {
        die("Message introuvable.");
    }

    // Vérifier que le message appartient à cette collaboration
    if ($message['collab_id'] != $collab_id) {
        die("Erreur : ce message n'appartient pas à cette collaboration.");
    }

    // Vérifier les permissions
    $projectController = new CollabProjectController();
    $collab = $projectController->getById($collab_id);

    if (!$collab) {
        die("Projet introuvable.");
    }

    $user_id = $isLoggedIn ? $_SESSION['user_id'] : $defaultUserId;

    if ($isLoggedIn) {
        $isOwner = ($collab['owner_id'] == $user_id);
        $isAuthor = ($message['user_id'] == $user_id);
        
        if (!$isOwner && !$isAuthor) {
            die("Erreur : vous n'avez pas la permission de modifier ce message.");
        }
    }
    // En mode développeur (non connecté), on permet la modification de tous les messages
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Modifier le message - GameHub Pro</title>
        <link rel="stylesheet" href="../../frontoffice/collaborations.css">
        <style>
            body {
                padding-top: 100px;
                background: #1a1a1a;
                color: #fff;
            }
            .edit-form-container {
                max-width: 700px;
                margin: 0 auto;
                padding: 2rem;
                background: rgba(0, 0, 0, 0.8);
                backdrop-filter: blur(10px);
                border-radius: 15px;
                box-shadow: 0 10px 30px rgba(0, 255, 136, 0.2);
                border: 1px solid rgba(0, 255, 136, 0.3);
            }
            .edit-form-container h2 {
                color: #00ff88;
                margin-bottom: 2rem;
                text-shadow: 0 0 20px rgba(0, 255, 136, 0.5);
            }
            .form-group {
                margin-bottom: 1.5rem;
            }
            .form-group label {
                display: block;
                margin-bottom: 0.5rem;
                color: #00ffea;
                font-weight: 600;
            }
            .form-group textarea {
                width: 100%;
                padding: 12px;
                background: rgba(255, 255, 255, 0.1);
                border: 2px solid rgba(0, 255, 136, 0.3);
                border-radius: 10px;
                color: #fff;
                font-size: 1rem;
                transition: all 0.3s ease;
                min-height: 150px;
                resize: vertical;
                font-family: inherit;
            }
            .form-group textarea:focus {
                outline: none;
                border-color: #00ff88;
                box-shadow: 0 0 15px rgba(0, 255, 136, 0.5);
            }
            .message-info {
                background: rgba(0, 255, 136, 0.1);
                border: 1px solid rgba(0, 255, 136, 0.3);
                padding: 15px;
                border-radius: 10px;
                margin-bottom: 1.5rem;
            }
            .message-info p {
                margin: 5px 0;
                color: #aaa;
            }
            .message-info strong {
                color: #00ff88;
            }
            .dev-mode {
                background: rgba(255, 215, 0, 0.2);
                border: 1px solid rgba(255, 215, 0, 0.5);
                padding: 15px;
                border-radius: 10px;
                margin-bottom: 2rem;
                color: #ffd700;
            }
            .btn-submit {
                width: 100%;
                padding: 15px;
                background: linear-gradient(135deg, #00ff88, #00C6FF);
                color: white;
                border: none;
                border-radius: 10px;
                font-size: 1.1rem;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
                margin-top: 1rem;
            }
            .btn-submit:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 20px rgba(0, 255, 136, 0.4);
            }
            .back-link {
                display: inline-block;
                margin-bottom: 20px;
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
            <div class="container">
                <a href="view_collab.php?id=<?php echo $collab_id; ?>" class="back-link">← Retour à la Collaboration</a>
                
                <div class="edit-form-container">
                    <h2>✏️ Modifier le message</h2>

                    <?php if (!$isLoggedIn): ?>
                        <div class="dev-mode">
                            ⚠️ <strong>Mode développeur</strong> : Vous n'êtes pas connecté. Vous pouvez modifier ce message.
                        </div>
                    <?php endif; ?>

                    <div class="message-info">
                        <p><strong>Projet :</strong> <?php echo htmlspecialchars($collab['titre']); ?></p>
                        <p><strong>Auteur :</strong> User <?php echo htmlspecialchars($message['user_id']); ?></p>
                        <p><strong>Date d'envoi :</strong> <?php echo htmlspecialchars($message['date_message'] ?? 'Date inconnue'); ?></p>
                    </div>

                    <form id="updateMessageForm" action="update_message.php" method="POST">
                        <input type="hidden" name="id" value="<?php echo $message_id; ?>">
                        <input type="hidden" name="collab_id" value="<?php echo $collab_id; ?>">

                        <div class="form-group">
                            <label for="message">Message *</label>
                            <textarea id="message" name="message"><?php echo htmlspecialchars($message['message']); ?></textarea>
                        </div>

                        <button type="submit" class="btn-submit">💾 Mettre à jour le message</button>
                    </form>
                </div>
            </div>
        </main>

        <script src="validation.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const validator = new FormValidator('updateMessageForm');
                
                // Validation pour message
                validator.addRule('message', {
                    required: true,
                    minLength: 1,
                    maxLength: 1000
                }, 'Message requis (1-1000 caractères)');
            });
        </script>

    </body>
    </html>
    <?php
    exit;
}

// Si aucune requête valide, rediriger
header("Location: ../frontoffice/collaborations.php");
exit;
?>
