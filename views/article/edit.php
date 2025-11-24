<?php
// views/article/edit.php
// Nettoyé de toute validation HTML5 (ex: 'required').

if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}
$errors = $_SESSION['article_errors'] ?? []; 
$input = $_SESSION['article_input'] ?? []; 
unset($_SESSION['article_errors'], $_SESSION['article_input']);

// $article est censé être passé par ArticleController::edit()
if (!isset($article) || empty($article)) {
    // Redirection si l'article n'existe pas
    header('Location: ArticleController.php?action=dashboard');
    exit;
}

// Les valeurs d'input par défaut sont celles de l'article, sauf si une erreur de validation renvoie les inputs utilisateur
$title_value = $input['title'] ?? $article['title'];
$content_value = $input['content'] ?? $article['content'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameHub Admin | Éditer Article</title>
    <link rel="stylesheet" href="../assets/css/style.css"> 
</head>
<body>
    <header>
        <div class="container">
            <h1 class="logo">GameHub Admin</h1>
            <nav>
                <ul>
                    <li><a href="ArticleController.php?action=list" class="super-button">Front Office</a></li>
                    <li><a href="ArticleController.php?action=dashboard" class="super-button">Dashboard</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div id="main-content">
        <div class="container">
            <div class="form-section">
                <h2>Éditer l'Article: <?php echo htmlspecialchars($article['title']); ?></h2>
                
                <form action="ArticleController.php?action=update" method="POST">
                    
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($article['id']); ?>">
                    
                    <div class="form-group">
                        <label for="title">Titre :</label>
                        <input type="text" name="title" id="title" class="form-control" 
                               value="<?php echo htmlspecialchars($title_value); ?>">
                        <?php if (isset($errors['title'])): ?>
                            <p class="error-message"><?php echo $errors['title']; ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="content">Contenu :</label>
                        <textarea name="content" id="content" class="form-control" rows="10"><?php echo htmlspecialchars($content_value); ?></textarea>
                        <?php if (isset($errors['content'])): ?>
                            <p class="error-message"><?php echo $errors['content']; ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <button type="submit" class="submit-btn super-button">Mettre à jour l'Article</button>
                </form>

                <a href="ArticleController.php?action=dashboard" class="back-link">← Retour au Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>