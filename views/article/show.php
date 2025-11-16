// views/article/show.php (CODE COMPLET)

<?php
// Assurez-vous que $article est défini par le contrôleur
if (!isset($article) || empty($article)) {
    // Si l'article n'est pas trouvé, le contrôleur devrait déjà avoir redirigé.
    // Cette sécurité est juste au cas où.
    header('Location: ArticleController.php?action=list');
    exit;
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
        /* Styles spécifiques pour la page d'article unique */
        .article-view {
            max-width: 900px;
            margin: 50px auto;
            padding: 40px;
            background: rgba(0, 0, 0, 0.8);
            border-radius: 15px;
            box-shadow: 0 0 30px rgba(0, 255, 136, 0.3);
            color: #ccc;
        }
        .article-view h1 {
            color: #00ff88;
            font-size: 2.5em;
            margin-bottom: 15px;
            border-bottom: 2px solid rgba(0, 255, 136, 0.5);
            padding-bottom: 10px;
        }
        .article-meta {
            font-size: 0.9em;
            color: #888;
            margin-bottom: 30px;
        }
        .article-content {
            font-size: 1.1em;
            line-height: 1.8;
            color: white;
            white-space: pre-wrap; /* Maintient les sauts de ligne si vous utilisez des entrées brutes */
        }
        .article-content p {
            margin-bottom: 1.5em;
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
                    <li><a href="ArticleController.php?action=dashboard" class="super-button">Admin</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
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
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2025 GameHub. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>