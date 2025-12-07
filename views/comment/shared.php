<?php
// views/comment/shared.php

// S'assurer que $sharedComments est initialisé (passé par ArticleController)
$sharedComments = $sharedComments ?? [];

// Récupération des messages de session
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}
$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameHub | Commentaires Partagés</title>
    <link rel="stylesheet" href="../assets/css/frontstyle.css"> 
    <style>
        /* Styles de base pour l'affichage */
        body { 
            background-color: #121212; 
            color: #fff; 
            font-family: sans-serif;
        }
        .container { 
            max-width: 900px; 
            margin: 0 auto; 
            padding: 20px; 
        }
        h2 { 
            color: #00ff88; 
            border-bottom: 2px solid #00ff88; 
            padding-bottom: 10px;
        }
        .shared-item {
            background: rgba(0, 0, 0, 0.5); 
            padding: 20px; 
            margin-bottom: 20px;
            border-radius: 10px; 
            border-left: 5px solid #28a745; /* Couleur pour le style */
        }
        .shared-meta {
            font-size: 0.9em;
            color: #ccc;
            margin-bottom: 10px;
        }
        blockquote {
            border-left: 3px solid #00ff88; 
            padding-left: 15px; 
            margin: 10px 0;
            font-style: italic;
        }
        .super-button {
            display: inline-block;
            padding: 8px 15px;
            text-decoration: none;
            background-color: #3f51b5;
            color: #fff;
            border-radius: 5px;
            margin-top: 10px;
            transition: background-color 0.3s;
        }
        .super-button:hover {
            background-color: #303f9f;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            color: #fff;
        }
        .alert.success {
            background-color: #28a745;
        }
        .alert.error {
            background-color: #dc3545;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1 class="logo">GameHub</h1> 
            <nav>
                <ul>
                    <li><a href="ArticleController.php?action=list" class="super-button">Accueil</a></li>
                    <li><a href="ArticleController.php?action=sharedComments" class="super-button" style="background-color: #28a745;">✉️ Partagés</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <h2>✉️ Vos Commentaires Partagés</h2>
        
        <?php if ($success): ?>
            <div class="alert success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <div class="shared-list" style="margin-top: 20px;">
        
        <?php if (!empty($sharedComments)): ?>
            
            <?php foreach ($sharedComments as $share): ?>
                <div class="shared-item">
                    
                    <p class="shared-meta">
                        Partagé par **<?php echo htmlspecialchars($share['sender_name'] ?? 'Un utilisateur'); ?>** le <?php echo date('d/m/Y à H:i', strtotime($share['shared_at'])); ?>
                    </p>
                    
                    <p style="font-weight: bold; color: #00ff88;">
                        Commentaire original de : <?php echo htmlspecialchars($share['comment_author'] ?? 'Auteur Inconnu'); ?>
                    </p>
                    
                    <blockquote>
                        <?php echo nl2br(htmlspecialchars($share['comment_content'])); ?>
                    </blockquote>
                    
                    <a href="ArticleController.php?action=show&id=<?php echo htmlspecialchars($share['article_id']); ?>" 
                       class="super-button">
                        Voir l'article: "<?php echo htmlspecialchars($share['article_title']); ?>"
                    </a>
                </div>
            <?php endforeach; ?>
            
        <?php else: ?>
            <p style="color: #fff; margin-top: 30px;">
                Vous n'avez pas encore reçu de commentaires partagés.
            </p>
        <?php endif; ?>
        
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 GameHub. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>