<?php
session_start();

// Mode développeur : permettre l'accès même sans connexion
$isLoggedIn = isset($_SESSION['user_id']);
$defaultOwnerId = 1; // ID par défaut pour le développeur

// Chemins relatifs depuis view/backoffice/collabcrud/
require_once __DIR__ . "/../../../controller/controllercollab/CollabProjectController.php";
// Note: CollabProject.php est déjà inclus par CollabProjectController.php

// Variables pour les messages
$message = '';
$messageType = ''; // 'success' ou 'error'
$createdId = null;

// Si le formulaire est envoyé
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Utiliser l'ID de session s'il existe, sinon utiliser celui du formulaire ou l'ID par défaut
    $owner_id = $isLoggedIn ? $_SESSION['user_id'] : (isset($_POST['owner_id']) ? intval($_POST['owner_id']) : $defaultOwnerId);
    $titre = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $max_membres = isset($_POST['max_membres']) ? intval($_POST['max_membres']) : 5;
    $image = isset($_POST['image']) ? trim($_POST['image']) : '';

    // Validation
    if (empty($titre) || empty($description)) {
        $message = 'Veuillez remplir tous les champs requis.';
        $messageType = 'error';
    } elseif ($max_membres < 1 || $max_membres > 20) {
        $message = 'Le nombre de membres doit être entre 1 et 20.';
        $messageType = 'error';
    } else {
        $collab = new CollabProject(
            null,
            $owner_id,
            $titre,
            $description,
            date("Y-m-d"),
            "ouvert",
            $max_membres,
            $image ?: null
        );

        $controller = new CollabProjectController();
        $createdId = $controller->create($collab);

        if ($createdId) {
            $message = '✅ Collaboration lancée avec succès ! Votre projet collaboratif est maintenant disponible.';
            $messageType = 'success';
        } else {
            $message = '❌ Erreur : impossible de créer la collaboration. Veuillez réessayer.';
            $messageType = 'error';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un projet collaboratif - GameHub Pro</title>
    <link rel="stylesheet" href="../../frontoffice/collaborations.css">
    <style>
        body {
            padding-top: 100px;
        }
        .create-form-container {
            max-width: 700px;
            margin: 0 auto;
            padding: 2rem;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 255, 136, 0.2);
            border: 1px solid rgba(0, 255, 136, 0.3);
        }
        .create-form-container h2 {
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
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(0, 255, 136, 0.3);
            border-radius: 10px;
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #00ff88;
            box-shadow: 0 0 15px rgba(0, 255, 136, 0.3);
        }
        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }
        .dev-mode {
            background: rgba(255, 215, 0, 0.2);
            border: 2px solid rgba(255, 215, 0, 0.5);
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            color: #ffd700;
        }
        .btn-submit {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #00ff88, #00ffea);
            border: none;
            border-radius: 10px;
            color: #000;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 0 25px rgba(0, 255, 136, 0.6);
        }
        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 0 35px rgba(0, 255, 136, 0.9);
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
        .message {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 2rem;
            font-weight: 600;
            text-align: center;
            animation: slideIn 0.5s ease;
        }
        .message.success {
            background: rgba(0, 255, 234, 0.2);
            color: #00ffea;
            border: 2px solid rgba(0, 255, 234, 0.5);
            box-shadow: 0 0 20px rgba(0, 255, 234, 0.3);
        }
        .message.error {
            background: rgba(255, 51, 92, 0.2);
            color: #ff335c;
            border: 2px solid rgba(255, 51, 92, 0.5);
            box-shadow: 0 0 20px rgba(255, 51, 92, 0.3);
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
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
        <div class="container">
            <a href="../../frontoffice/collaborations.php" class="back-link">← Retour aux Collaborations</a>
            
            <div class="create-form-container">
                <h2>Créer un Projet Collaboratif</h2>

                <?php if (!empty($message)): ?>
                    <div class="message <?php echo $messageType; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <?php if (!$isLoggedIn): ?>
                    <div class="dev-mode">
                        ⚠️ <strong>Mode développeur</strong> : Vous n'êtes pas connecté. Veuillez entrer un ID utilisateur pour créer la collaboration.
                    </div>
                <?php endif; ?>

                <?php if ($messageType === 'success'): ?>
                    <script>
                        // Masquer le formulaire après succès
                        document.addEventListener('DOMContentLoaded', function() {
                            var form = document.querySelector('form');
                            if (form) {
                                form.style.opacity = '0.5';
                                form.style.pointerEvents = 'none';
                            }
                            
                            // Redirection après 3 secondes
                            setTimeout(function() {
                                window.location.href = '../../frontoffice/collaborations.php';
                            }, 3000);
                        });
                    </script>
                <?php endif; ?>

                <?php if ($messageType !== 'success'): ?>
                <form action="" method="POST">
                    
                    <?php if (!$isLoggedIn): ?>
                    <div class="form-group">
                        <label for="owner_id">ID Utilisateur (Owner) *</label>
                        <input type="number" id="owner_id" name="owner_id" value="<?php echo $defaultOwnerId; ?>" min="1" required>
                    </div>
                    <?php else: ?>
                        <input type="hidden" name="owner_id" value="<?php echo $_SESSION['user_id']; ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="titre">Titre du projet *</label>
                        <input type="text" id="titre" name="titre" required placeholder="Ex: Développement d'un jeu de stratégie">
                    </div>

                    <div class="form-group">
                        <label for="description">Description *</label>
                        <textarea id="description" name="description" required placeholder="Décrivez votre projet collaboratif..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="max_membres">Nombre maximum de membres *</label>
                        <input type="number" id="max_membres" name="max_membres" min="1" max="20" value="5" required>
                    </div>

                    <div class="form-group">
                        <label for="image">Image (URL) - Optionnel</label>
                        <input type="text" id="image" name="image" placeholder="https://example.com/image.jpg">
                    </div>

                    <button type="submit" class="btn-submit">🚀 Créer la Collaboration</button>

                </form>
                <?php endif; ?>
            </div>
        </div>
    </main>

</body>
</html>
