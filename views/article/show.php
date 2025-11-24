<?php
// views/article/show.php (MODIFIÉ)

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
    // Redirection gérée dans le contrôleur, mais ajout d'une sécurité
    header('Location: ArticleController.php?action=list');
    exit;
}
$comments = $comments ?? []; 
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameHub | <?php echo htmlspecialchars($article['title']); ?></title>
    <link rel="stylesheet" href="../assets/css/frontstyle.css"> 
    <style>
        /* Styles spécifiques au show.php - (Doit être dans frontstyle.css pour être propre, mais laissé ici pour la démo) */
        .article-view {
            max-width: 900px; margin: 50px auto; padding: 40px; background: rgba(0, 0, 0, 0.8); border-radius: 15px; box-shadow: 0 0 30px rgba(0, 255, 136, 0.3); color: #ccc;
        }
        .article-view h1 { color: #00ff88; font-size: 2.5em; margin-bottom: 0.5em; }
        .article-meta { color: #aaa; font-style: italic; margin-bottom: 25px; border-bottom: 1px dashed rgba(255, 255, 255, 0.1); padding-bottom: 10px; }
        .article-content { font-size: 1.1em; white-space: pre-wrap; }
        .article-content p { margin-bottom: 1.5em; }
        .comments-section { max-width: 900px; margin: 50px auto; padding: 30px; background: rgba(0, 0, 0, 0.7); border-radius: 15px; box-shadow: 0 0 25px rgba(255, 0, 150, 0.2); border: 1px solid rgba(255, 0, 150, 0.3); }
        .comments-section h3 { color: #ff0096; border-bottom: 2px solid rgba(255, 0, 150, 0.5); padding-bottom: 10px; margin-bottom: 20px; font-size: 1.8em; }
        .comment-form .form-group textarea { width: 100%; padding: 15px; border-radius: 8px; border: 1px solid rgba(0, 255, 136, 0.3); background: rgba(15, 15, 35, 0.9); color: #fff; resize: vertical; min-height: 120px; }
        .comment-item { padding: 15px 20px; border: 1px solid rgba(0, 255, 136, 0.2); border-radius: 8px; margin-bottom: 20px; background: rgba(25, 25, 45, 0.9); box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3); }
        .comment-author { font-size: 0.9em; color: #00ff88; margin-bottom: 8px; border-bottom: 1px dashed rgba(0, 255, 136, 0.2); padding-bottom: 5px; }
        .comment-author span { font-style: italic; color: #aaa; font-weight: normal; }
        .comment-content { color: #ccc; white-space: pre-wrap; }
        .error-message { color: #ff0096; font-size: 0.9em; margin-top: 5px; }
        .message.success { background-color: #28a745; color: white; padding: 10px; border-radius: 5px; margin: 10px auto; max-width: 900px; }
        .message.error { background-color: #dc3545; color: white; padding: 10px; border-radius: 5px; margin: 10px auto; max-width: 900px; }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1 class="logo">GameHub</h1> 
            <nav>
                <ul>
                    <li><a href="ArticleController.php?action=list" class="super-button">Accueil</a></li>
                    <li><a href="ArticleController.php?action=dashboard" class="super-button">Admin</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <?php if (!empty($success)): ?>
            <div class="message success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <div class="article-view">
            <h1><?php echo htmlspecialchars($article['title']); ?></h1>
            
            <p class="article-meta">
                Publié le **<?php echo date('d/m/Y à H:i', strtotime($article['created_at'])); ?>** par **<?php echo htmlspecialchars($article['author_name']); ?>**
            </p>

            <div class="article-content">
                <?php echo nl2br(htmlspecialchars($article['content'])); ?>
            </div>

            <div style="margin-top: 40px;">
                 <a href="ArticleController.php?action=list" class="super-button">← Retour à la liste des articles</a>
            </div>
        </div>

        <div class="comments-section">
            <h3>Laisser un Commentaire</h3>

            <div class="comment-form">
                <form action="CommentController.php?action=store" method="POST">
                    <input type="hidden" name="article_id" value="<?php echo htmlspecialchars($article['id']); ?>">

                    <div class="form-group">
                        <textarea id="content" name="content" rows="4" placeholder="Votre commentaire..." required><?php echo htmlspecialchars($input['content'] ?? ''); ?></textarea>
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
                                Posté par **<?php echo htmlspecialchars($comment['author_name']); ?>** - 
                                <span><?php echo date('d/m/Y à H:i', strtotime($comment['created_at'])); ?></span>
                            </p>
                            <p class="comment-content">
                                <?php echo nl2br(htmlspecialchars($comment['content'])); ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: #fff;">Soyez le premier à commenter cet article !</p>
                <?php endif; ?>
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