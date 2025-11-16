// views/article/dashboard.php (CODE COMPLET CORRIGÉ)

<?php
// dashboard.php
// Ce fichier affichera le Back Office pour gérer le module Article
// Les variables PHP sont alimentées depuis ArticleController.php
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
                <li><a href="ArticleController.php?action=list">Front Office</a></li>
            </ul>
        </nav>
    </aside>

    <main id="main-content" class="main-content">
        <section id="dashboard" class="dashboard">
            <div class="container">
                <h2>Articles Dashboard</h2>
                
                <?php if (isset($successMessage)): ?>
                    <div style="background-color: #00ff88; color: black; padding: 15px; margin-bottom: 20px; border-radius: 5px; font-weight: bold;">
                        <?php echo htmlspecialchars($successMessage); ?>
                    </div>
                <?php endif; ?>
                
                <div class="stats-cards">
                    <div class="stat-card">
                        <h3>Total Articles</h3>
                        <p class="stat-number"><?php echo $totalArticles ?? '0'; ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Total Authors</h3>
                        <p class="stat-number"><?php echo $totalAuthors ?? '0'; ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Published Today</h3>
                        <p class="stat-number"><?php echo '0'; ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Growth</h3>
                        <p class="stat-number"><?php echo '0%'; ?></p>
                    </div>
                </div>

                <div class="dashboard-widgets">
                    <div class="widget">
                        <h3>Articles Récents</h3>
                        <table class="user-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>Role</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($articles)) {
                                    foreach ($articles as $article) {
                                        // Correction des clés pour utiliser celles de ArticleModel::readDashboardArticles
                                        echo "<tr>
                                            <td>{$article['id']}</td>
                                            <td>" . htmlspecialchars($article['title']) . "</td>
                                            <td>" . htmlspecialchars($article['author_name']) . "</td>
                                            <td>" . htmlspecialchars($article['author_role']) . "</td> 
                                            <td>
                                                <a href='ArticleController.php?action=edit&id={$article['id']}'>Edit</a> |
                                                <a href='ArticleController.php?action=delete&id={$article['id']}' onclick=\"return confirm('Êtes-vous sûr de vouloir supprimer cet article?');\">Delete</a>
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