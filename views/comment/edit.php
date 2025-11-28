<?php
// views/comment/edit.php

// Initialisation de la session et récupération des messages du contrôleur
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}
$error = $_SESSION['error'] ?? null;
$errors = $_SESSION['comment_errors'] ?? []; 
// L'input par défaut est le contenu existant, écrasé par les erreurs de validation
$input_content = $_SESSION['comment_input']['content'] ?? ($comment['content'] ?? ''); 

unset($_SESSION['error'], $_SESSION['comment_errors'], $_SESSION['comment_input']);

// $comment et $article_id sont passés par CommentController::edit()
if (!isset($comment)) {
    header('Location: ArticleController.php?action=list');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Commentaire</title>
    <link rel="stylesheet" href="../assets/css/frontstyle.css"> 
    <style>
        .edit-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background: rgba(0, 0, 0, 0.8);
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 255, 136, 0.3);
            color: #fff;
        }
        .edit-container h1 { color: #00ff88; margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; color: #ccc; }
        .form-group textarea {
            width: 100%; padding: 10px; border-radius: 5px; 
            border: 1px solid #00ff8855; background: #1a1a2e; color: #fff; resize: vertical;
        }
        .error-message { color: #ff4d4d; margin-top: 5px; font-size: 0.9em; }
        .submit-btn { margin-top: 20px; }
        .back-link { display: block; margin-top: 20px; color: #00ff88; text-decoration: none; }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <header>
        </header>

    <main class="container">
        <div class="edit-container">
            <h1>Modifier le Commentaire #<?php echo htmlspecialchars($comment['id']); ?></h1>

            <?php if ($error): ?>
                <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form action="../controllers/CommentController.php?action=update" method="POST">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($comment['id']); ?>">
                <input type="hidden" name="article_id" value="<?php echo htmlspecialchars($article_id); ?>">

                <div class="form-group">
                    <label for="content">Contenu du Commentaire:</label>
                    <textarea id="content" name="content" rows="6"><?php echo htmlspecialchars($input_content); ?></textarea>
                    <?php if (isset($errors['content'])): ?>
                        <p class="error-message"><?php echo $errors['content']; ?></p>
                    <?php endif; ?>
                </div>
                
                <button type="submit" class="super-button submit-btn">Enregistrer les modifications</button>
            </form>

            <a href="../controllers/ArticleController.php?action=show&id=<?php echo htmlspecialchars($article_id); ?>" class="back-link">
                Retour à l'article
            </a>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 GameHub. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>