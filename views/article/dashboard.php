<?php
// views/article/dashboard.php (MODIFIÉ)

// Les variables $totalArticles, $totalComments, $articles, $success, $error sont passées par ArticleController::dashboard()
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - GameHub Articles</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1 class="logo">GameHub Admin</h1>
            <nav>
                <ul>
                    <li><a href="ArticleController.php?action=dashboard" class="super-button">Dashboard</a></li>
                    <li><a href="ArticleController.php?action=create" class="super-button">+ Créer un Article</a></li>
                    <li><a href="CommentController.php?action=index" class="super-button">Modérer Commentaires</a></li>
                    <li><a href="ArticleController.php?action=list" class="super-button">Retour Front Office</a></li>
                </ul>
            </nav>
            <button id="sidebar-toggle" class="sidebar-toggle">☰</button>
        </div>
    </header>
    
    <aside id="sidebar" class="sidebar">
        <nav>
            <ul>
                <li><a href="ArticleController.php?action=dashboard">Dashboard</a></li>
                <li><a href="ArticleController.php?action=create">Créer un Article</a></li>
                <li><a href="CommentController.php?action=index">Modérer Commentaires</a></li>
                <li><a href="ArticleController.php?action=list">Retour Front Office</a></li>
            </ul>
        </nav>
    </aside>

    <main id="main-content">
        <?php if (!empty($success)): ?>
            <div class="message success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <section id="stats" class="stats-section">
            <div class="container">
                <h2>Statistiques du Dashboard</h2>
                <div class="stats-grid">
                    
                    <div class="stat-card">
                        <h3>Total Articles</h3>
                        <p class="stat-number"><?php echo $totalArticles; ?></p>
                    </div>
                    
                    <div class="stat-card">
                        <h3>Auteurs Uniques</h3>
                        <p class="stat-number"><?php echo $uniqueAuthors; ?></p>
                    </div>

                    <div class="stat-card">
                        <h3>Articles Aujourd'hui</h3>
                        <p class="stat-number"><?php echo $publishedToday; ?></p>
                    </div>
                    
                    <div class="stat-card stat-comments">
                        <h3>Total Commentaires</h3>
                        <p class="stat-number"><?php echo $totalComments; ?></p> 
                        <a href="CommentController.php?action=index" class="sub-link">Modérer les Commentaires →</a>
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
                                if (isset($articles) && is_array($articles)) {
                                    foreach ($articles as $article) {
                                        echo "<tr>
                                            <td>" . htmlspecialchars($article['id']) . "</td>
                                            <td>" . htmlspecialchars($article['title']) . "</td>
                                            <td>" . htmlspecialchars($article['author_name']) . " ({$article['author_role']})</td> 
                                            <td>" . date('d/m/Y', strtotime($article['created_at'])) . "</td> 
                                            <td>
                                                <a href='ArticleController.php?action=show&id={$article['id']}' class='action-btn view'>Voir</a> | 
                                                <a href='ArticleController.php?action=edit&id={$article['id']}' class='action-btn edit'>Edit</a> |
                                                <a href='ArticleController.php?action=delete&id={$article['id']}' class='action-btn delete' onclick=\"return confirm('Êtes-vous sûr de vouloir supprimer cet article?');\">Delete</a>
                                            </td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5'>Aucun article trouvé.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                        <div style="margin-top: 20px; text-align: right;">
                             <a href="ArticleController.php?action=create" class="super-button">+ Créer un nouvel article</a>
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

    <script src="../assets/js/script.js"></script>
</body>
</html>