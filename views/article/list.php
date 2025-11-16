// views/article/list.php (CODE COMPLET CORRIGÉ)

<?php
// list.php
// Front Office pour afficher la liste des articles (Vue dynamique)
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameHub - Derniers Articles</title>
    <link rel="stylesheet" href="../assets/css/frontstyle.css"> 
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

    <section id="home" class="hero">
        <div class="container">
            <h2>Bienvenue sur GameHub !</h2>
            <p>Le meilleur de l'actualité gaming, des tests et des astuces.</p>
            <a href="#articles-list" class="shop-now-btn">Voir les Articles</a>
        </div>
    </section>

    <section id="articles-list" class="deals">
        <div class="container">
            <h3>Derniers Articles</h3>
            <div class="deal-cards article-cards">
                
                <?php 
                if (isset($articles) && is_array($articles)) : 
                    foreach ($articles as $article) : 
                ?>
                    <div class="card article-card">
                        <h4><?php echo htmlspecialchars($article['title']); ?></h4>
                        
                        <p class="article-meta">
                            Par: **<?php echo htmlspecialchars($article['author_name']); ?>** <br>
                            Publié le: <?php echo date('d/m/Y', strtotime($article['created_at'])); ?>
                        </p>
                        
                        <a href="ArticleController.php?action=show&id=<?php echo $article['id']; ?>" class="super-button">Lire l'Article</a>
                    </div>
                <?php 
                    endforeach; 
                else : 
                ?>
                    <p>Aucun article trouvé pour le moment.</p>
                <?php endif; ?>

            </div>
        </div>
    </section>

    <footer id="contact">
        <div class="container">
            <p>&copy; 2025 GameHub. Tous droits réservés.</p>
            <p>Contact: info@gamehub.com</p>
        </div>
    </footer>
    
    <script src="../assets/js/frontscript.js"></script> 
</body>
</html>