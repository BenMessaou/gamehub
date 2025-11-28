<?php
// views/article/list.php (CODE COMPLET MIS À JOUR)

if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Récupération des messages de session
$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
$article_error = $_SESSION['article_error'] ?? null;

// Nettoyage des messages après affichage
unset($_SESSION['success'], $_SESSION['error'], $_SESSION['article_error']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameHub - Derniers Articles</title>
    <link rel="stylesheet" href="../assets/css/frontstyle.css"> 
    <style>
        /* Styles simples pour les messages et le formulaire de recherche */
        .message-box {
            padding: 15px;
            margin: 20px auto;
            border-radius: 5px;
            max-width: 90%;
            text-align: center;
            font-weight: bold;
        }
        .success-box {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error-box {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .search-form {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-bottom: 30px;
            padding: 15px;
            background-color: #2a2a2a; /* Couleur sombre du thème */
            border-radius: 5px;
            max-width: 90%;
            margin-left: auto;
            margin-right: auto;
        }
        .search-form label {
            color: #fff;
            white-space: nowrap;
        }
        .search-form input[type="text"] {
            padding: 8px;
            border-radius: 3px;
            border: 1px solid #555;
            background-color: #333;
            color: #fff;
        }
        .search-form button {
            background-color: #e50914; 
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .search-form button:hover {
            background-color: #f40612;
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

    <section id="home" class="hero">
        <div class="container">
            <h2>Bienvenue sur GameHub !</h2>
            <p>Le meilleur de l'actualité gaming, des tests et des astuces.</p>
            <a href="#articles-list" class="shop-now-btn">Voir les Articles</a>
        </div>
    </section>
    
    <?php if ($success): ?>
        <div class="message-box success-box">
            <p><?php echo htmlspecialchars($success); ?></p>
        </div>
    <?php endif; ?>

    <?php if ($error || $article_error): ?>
        <div class="message-box error-box">
            <p><?php echo htmlspecialchars($error ?? $article_error); ?></p>
        </div>
    <?php endif; ?>
    
    <section id="articles-list" class="deals">
        <div class="container">
            <h3>Derniers Articles</h3>
            
            <form action="ArticleController.php" method="GET" class="search-form">
                <input type="hidden" name="action" value="searchByDate">
                <label for="search_date">Filtrer par date (AAAA-MM-JJ) :</label>
                <input type="text" id="search_date" name="search_date" placeholder="Ex: 2024-01-30" required>
                <button type="submit">Rechercher</button>
            </form>
            <br>
            
            <div class="deal-cards article-cards">
                
                <?php 
                // La variable $articles est passée par ArticleController::list()
                if (isset($articles) && is_array($articles) && !empty($articles)) : 
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
                    <p style="text-align: center; color: #fff;">Aucun article trouvé pour le moment. Créez-en un dans l'Admin !</p>
                <?php endif; ?>

            </div>
        </div>
    </section>

    <footer id="contact">
        <div class="container">
            <p>&copy; 2025 GameHub. Tous droits réservés.</p>
        </div>
    </footer>
    
    <script src="../assets/js/frontscript.js"></script> 
</body>
</html>