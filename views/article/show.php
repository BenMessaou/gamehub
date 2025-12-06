<?php
// views/article/show.php (VERSION CORRIGÉE DU CHEMIN D'IMAGE)

// Initialisation de la session et récupération des messages du contrôleur
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}
$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
$errors = $_SESSION['comment_errors'] ?? []; 
$input = $_SESSION['comment_input'] ?? []; 
unset($_SESSION['success'], $_SESSION['error'], $_SESSION['comment_errors'], $_SESSION['comment_input']);

// $article et $comments sont passés par ArticleController::show()

if (!isset($article) || empty($article)) {
    header('Location: ArticleController.php?action=list');
    exit;
}
$comments = $comments ?? [];

// ID de l'utilisateur actuellement "connecté" (À adapter à votre logique de session/authentification)
$CURRENT_USER_ID = 1; 

// ⭐️ CORRECTION DU CHEMIN D'IMAGE (Simplifié et Robuste)
$base_path = '/gamehub'; // Assurez-vous que '/gamehub' est bien le nom de votre dossier racine web
$image_src = '';
if (!empty($article['image_path'])) {
    // Si $article['image_path'] vaut 'public/uploads/xxx.jpg', 
    // $image_src vaudra '/gamehub/public/uploads/xxx.jpg' (chemin web correct)
    $image_src = $base_path . '/' . htmlspecialchars($article['image_path']);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameHub | <?php echo htmlspecialchars($article['title']); ?></title>
    <link rel="stylesheet" href="../assets/css/frontstyle.css"> 
    <style>
        /* Styles spécifiques au show.php */
        .comment-actions { margin-top: 5px; font-size: 0.8rem; }
        .action-btn {
            display: inline-block; padding: 5px 10px; margin-right: 5px; 
            border-radius: 5px; text-decoration: none; color: #fff; 
            cursor: pointer; transition: background-color 0.3s; border: none;
        }
        .edit-btn { background-color: #007bff; }
        .delete-btn { background-color: #dc3545; }
        .edit-btn:hover { background-color: #0056b3; }
        .delete-btn:hover { background-color: #a71d2a; }
        .comment-item {
            background: rgba(0, 0, 0, 0.5); padding: 15px; margin-bottom: 15px;
            border-radius: 10px; border-left: 5px solid #00ff88;
        }
        .error-message { color: #ff4d4d; font-size: 0.9em; margin-top: 5px; }
        .article-header-nav a {
            color: #00ff88; margin-right: 20px; text-decoration: none;
        }
        .article-header-nav { margin-bottom: 20px; }
        
        /* STYLES POUR L'IMAGE PRINCIPALE */
        .article-main-image {
            width: 100%; 
            max-height: 400px; 
            object-fit: cover; 
            border-radius: 10px;
            margin: 20px 0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5); 
        }
        
        /* Styles TTS */
        .tts-controls button {
            margin-right: 10px;
            font-size: 1rem;
            padding: 10px 15px;
        }

        /* STYLES POUR LE CADRE DU COMMENTAIRE */
        .comment-form-box {
            background: rgba(30, 30, 30, 0.8);
            padding: 25px;
            border-radius: 12px;
            border: 2px solid #00ff88;
            box-shadow: 0 0 20px rgba(0, 255, 136, 0.2);
            margin-bottom: 30px;
        }

        .comment-form-box h3 {
            color: #fff;
            margin-top: 0;
            border-bottom: 1px solid #444;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #00ff88;
            font-weight: bold;
        }
        
        .form-group input[type="text"], 
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #555;
            border-radius: 5px;
            background-color: #222;
            color: #fff;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }
        
        .form-group input[type="text"]:focus, 
        .form-group textarea:focus {
            border-color: #00ff88;
            outline: none;
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
                    <li><a href="ArticleController.php?action=list" class="super-button">Articles</a></li>
                    <li><a href="ArticleController.php?action=dashboard" class="super-button">Admin (Back Office)</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="article-header-nav">
             <a href="ArticleController.php?action=list">⬅️ Retour à la liste des articles</a>
        </div>

        <?php if ($success): ?>
            <div class="alert success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <div class="article-view">
            <article class="article-content">
                <h1><?php echo htmlspecialchars($article['title']); ?></h1>
                
                <?php if (!empty($article['image_path'])): ?>
                    <img src="<?php echo $image_src; ?>" 
                        alt="<?php echo htmlspecialchars($article['title']); ?>" 
                        class="article-main-image">
                <?php endif; ?>
                
                <p class="article-meta">
                    Publié le <?php echo date('d/m/Y', strtotime($article['created_at'])); ?> 
                    par **<?php echo htmlspecialchars($article['author_name'] ?? 'Inconnu'); ?>**
                </p>
                
                <div class="article-body">
                    <?php echo nl2br(htmlspecialchars($article['content'])); ?>
                </div>
                <div class="tts-controls" style="margin-top: 30px; margin-bottom: 20px;">
                    <button id="listen-article-btn" class="super-button" style="display: none;">
                        🔊 Écouter l'article
                    </button>
                    <button id="stop-article-btn" class="super-button" style="display:none; background-color: #dc3545;">
                        ⏹️ Arrêter la lecture
                    </button>
                </div>
                <div id="article-to-read" style="display:none;">
                    <?php 
                        echo htmlspecialchars($article['title']) . ". ";
                        echo htmlspecialchars($article['content']); 
                    ?>
                </div>
                </article>

            <div class="comment-section">
                <div class="comment-form-box">
                    <h3>Poster un Commentaire</h3>
                    <form action="../controllers/CommentController.php?action=store" method="POST">
                        <input type="hidden" name="article_id" value="<?php echo $article['id']; ?>">
                        
                        <div class="form-group">
                            <label for="author_name">Votre Nom/Pseudonyme (obligatoire):</label>
                            <input type="text" id="author_name" name="author_name"
                                    value="<?php echo htmlspecialchars($input['author_name'] ?? ''); ?>">
                            <?php if (isset($errors['author_name'])): ?>
                                <p class="error-message"><?php echo $errors['author_name']; ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="content">Votre Commentaire:</label>
                            <textarea id="content" name="content" rows="4"><?php echo htmlspecialchars($input['content'] ?? ''); ?></textarea>
                            <?php if (isset($errors['content'])): ?>
                                <p class="error-message"><?php echo $errors['content']; ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <button type="submit" class="super-button submit-btn">Poster le Commentaire</button>
                    </form>
                </div>

                <div class="comments-list">
                    <h3>Liste des Commentaires (<?php echo count($comments); ?>)</h3>
                    <?php if (!empty($comments)): ?>
                        <?php foreach ($comments as $comment): ?>
                            <div class="comment-item">
                                <p class="comment-author">
                                    Posté par **<?php 
                                        echo htmlspecialchars($comment['author_name'] ?? 'Inconnu'); 
                                    ?>** - 
                                    <span><?php echo date('d/m/Y à H:i', strtotime($comment['created_at'])); ?></span>
                                </p>
                                <p class="comment-content">
                                    <?php echo nl2br(htmlspecialchars($comment['content'])); ?>
                                </p>
                                
                                <?php 
                                if ($comment['user_id'] == $CURRENT_USER_ID): 
                                ?>
                                <div class="comment-actions">
                                    <a href="../controllers/CommentController.php?action=edit&id=<?php echo $comment['id']; ?>" class="action-btn edit-btn">
                                        Modifier
                                    </a>
                                    
                                    <form action="../controllers/CommentController.php?action=delete" method="POST" style="display:inline-block;" 
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?');">
                                        <input type="hidden" name="id" value="<?php echo $comment['id']; ?>">
                                        <input type="hidden" name="article_id" value="<?php echo $article['id']; ?>">
                                        <button type="submit" class="action-btn delete-btn">Supprimer</button>
                                    </form>
                                </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="color: #fff;">Soyez le premier à commenter cet article !</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 GameHub. Tous droits réservés.</p>
        </div>
    </footer>
    <script src="../assets/js/frontscript.js"></script> 
</body>
</html>