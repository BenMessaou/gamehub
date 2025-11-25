<?php
// views/article/show.php

// Initialisation de la session et récupération des messages du contrôleur
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}
$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
// Récupère les erreurs de validation PHP du formulaire de commentaire
$errors = $_SESSION['comment_errors'] ?? []; 
$input = $_SESSION['comment_input'] ?? []; 
unset($_SESSION['success'], $_SESSION['error'], $_SESSION['comment_errors'], $_SESSION['comment_input']);

// $article et $comments sont passés par ArticleController::show()

if (!isset($article) || empty($article)) {
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
    <style>.error-message { color: #ff0055; margin-top: 5px; font-size: 0.9em; }</style>
</head>
<body>

    <header>
        <div class="container">
            <h1 class="logo">GameHub</h1>
            <nav>
                <ul>
                    <li><a href="ArticleController.php?action=list" class="super-button">Accueil</a></li>
                    <li><a href="ArticleController.php?action=dashboard" class="super-button">Admin Dashboard</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <div class="container">
            <div class="article-view">
                <a href="ArticleController.php?action=list" class="back-link">← Retour aux articles</a>
                
                <h1><?php echo htmlspecialchars($article['title']); ?></h1>
                <p class="article-meta">
                    Publié le <?php echo date('d/m/Y', strtotime($article['created_at'])); ?> par <?php echo htmlspecialchars($article['author_name']); ?>
                </p>

                <div class="article-content">
                    <?php echo nl2br(htmlspecialchars($article['content'])); ?>
                </div>
            </div>

            <div class="comments-section">
                
                <div class="comment-form">
                    <h3>Laisser un commentaire</h3>

                    <?php if ($success): ?>
                        <p class="message success"><?php echo $success; ?></p>
                    <?php endif; ?>
                    <?php if ($error): ?>
                        <p class="message error-global"><?php echo $error; ?></p>
                    <?php endif; ?>

                    <form action="CommentController.php?action=store&article_id=<?php echo $article['id']; ?>" method="POST" novalidate>
                        <div class="form-group">
                            <label for="content">Votre commentaire :</label>
                            <textarea name="content" id="content" class="form-control" rows="5"><?php echo htmlspecialchars($input['content'] ?? ''); ?></textarea>
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
                        <p style="color: #fff; text-align: center; padding: 20px;">Soyez le premier à commenter cet article !</p>
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
</body>
</html>