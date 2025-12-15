<?php
// views/article/dashboard.php

// Initialisation de la session au cas où l'ArticleController ne l'aurait pas fait (bonne pratique)
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}
// Note: $stats, $articles, $success, $error sont censés être définis par ArticleController::dashboard()

// Pour éviter les warnings Undefined variable si les variables n'existent pas (même si le Controller devrait les passer)
$stats = $stats ?? [];
$articles = $articles ?? [];
$success = $success ?? ($_SESSION['success'] ?? null);
$error = $error ?? ($_SESSION['error'] ?? null);

// Nettoyage de la session après récupération
unset($_SESSION['success'], $_SESSION['error']);

// *** CORRECTION CRITIQUE DU CHEMIN D'ACCÈS ***
// Utilisez un chemin absolu pour les assets pour éviter les problèmes de navigation.
// Changez '/gamehub' si votre dossier racine XAMPP/WAMP est différent.
$base_path = '/gamehub'; 
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - GameHub Articles</title>
    
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../frontoffice/index.css">
</head>
<body>
    <header>
    <div class="container">
        <h1 class="logo">gamehub</h1>
        <img src="logo.png" class="logo1" alt="">
        <nav>
            <ul>
                <li><a href="../frontoffice/index.php" class="super-button">Projects</a></li>
                <li><a href="#deals" class="super-button">Events
                </a></li>
                <li><a href="../shop.php" class="super-button">Shop </a></li>
                <li><a href="../article/list.php" class="super-button">Article</a></li><li><a class="super-button" href="../index1.php">feedback</a></li>
                <li><a class="super-button" href="../frontoffice/profile.php">Profile</a></li>
                <li><a href="avis.php" class="super-button">Avis </a></li>
            </ul>
        </nav>
    </div>
</header>
    
    <aside id="sidebar" class="sidebar">
        <nav>
            <ul>
                <li><a href="">Dashboard</a></li>
                <li><a href="create.php">Create an Article</a></li>
                <li><a href="index.php">Modify Comments</a></li>
                <li><a href="list.php">Back Front Office</a></li>
            </ul>
        </nav>
    </aside>
    <style>
        .stats-grid { 
            display:grid; 
            grid-template-columns: repeat(auto-fit, minmax(200px,1fr)); 
            gap:20px; 
            margin:40px 0; 
        }
        .stat-card { 
            background:rgba(0,255,136,0.1); 
            padding:25px; 
            border-radius:15px; 
            text-align:center; 
            border:2px solid #00ff88; 
            box-shadow:0 0 20px rgba(0,255,136,0.2);
        }
    </style>

    <main id="main-content">
        <?php if (!empty($success)): ?>
            <div class="message success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="message error-global"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <section id="stats" class="stats-section">
            <div class="container">
                <h2> Dashboard statistics</h2>
                <div class="stats-grid">

                    <div class="stat-card widget">
                        <h3>Total Article</h3>
                        <p class="stat-number"><?php echo htmlspecialchars($stats['totalArticles'] ?? '0'); ?></p></p>
                    </div>
                    
                    <div class="stat-card widget">
                        <h3>unique authors</h3>
                        <p class="stat-number"><?php echo htmlspecialchars($stats['uniqueAuthors'] ?? '0'); ?></p>
                    </div>

                    <div class="stat-card widget">
                        <h3>Today's Articles</h3>
                        <p class="stat-number"><?php echo htmlspecialchars($stats['publishedToday'] ?? '0'); ?></p>
                    </div>
                    
                    <div class="stat-card stat-comments widget">
                        <h3>Total Comments</h3>
                        <p class="stat-number"><?php echo htmlspecialchars($stats['totalComments'] ?? '0'); ?></p> 
                        <a href="../comment/index.php" class="sub-link">Modify Comments</a>
                    </div>
                    
                </div>
            </div>
        </section>

        <section id="articles-list" class="content-section">
            <div class="container">
                <h2>Gestion des Articles</h2>
                
                <div class="widget-grid">
                    <div class="widget wide-widget">
                        <h3>Liste des Articles</h3>
                        <table class="data-table article-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Titre</th>
                                    <th>Auteur</th>
                                    <th>Date Création</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if (!empty($articles) && is_array($articles)) {
                                    foreach ($articles as $article) {
                                        echo "<tr>
                                            <td>" . htmlspecialchars($article['id']) . "</td>
                                            <td>" . htmlspecialchars($article['title']) . "</td>
                                            <td>" . htmlspecialchars($article['author_name']) . " (" . htmlspecialchars($article['author_role']) . ")</td> 
                                            <td>" . date('d/m/Y', strtotime($article['created_at'])) . "</td> 
                                            <td>
                                                <a href='show.php?id={$article['id']}' class='action-btn view'>Voir</a> | 
                                                <a href='edit.php?id={$article['id']}' class='action-btn edit'>Edit</a> |
                                                <a href='delete.php?id={$article['id']}' class='action-btn delete' onclick=\"return confirm('Êtes-vous sûr de vouloir supprimer cet article et tous ses commentaires ?');\">Delete</a>
                                            </td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5' style='text-align: center;'>Aucun article trouvé.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                        <div style="margin-top: 20px; text-align: right;">
                             <a href="create.php" class="super-button">create a new article</a>
                        </div>
                    </div>

                    <div class="widget">
                        <h3>Graphiques</h3>
                        <div class="chart-placeholder">
                            <p>Placeholder pour les statistiques</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script src="<?php echo $base_path; ?>/assets/js/script.js"></script>
</body>
</html>