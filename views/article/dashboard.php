<?php
// dashboard.php
// Ce fichier affichera le Back Office pour gérer le module Article
// Les variables PHP ($totalArticles, $totalAuthors, $publishedToday, $growth, $articles) 
// seront alimentées depuis ArticleController.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - GameHub Articles</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1 class="logo">GameHub Admin</h1>
            <nav>
                <ul>
                    <li><a href="#dashboard" class="super-button">Dashboard</a></li>
                    <li><a href="#articles" class="super-button">Articles</a></li>
                    <li><a href="#analytics" class="super-button">Analytics</a></li>
                    <li><a href="#settings" class="super-button">Settings</a></li>
                </ul>
            </nav>
            <button id="sidebar-toggle" class="sidebar-toggle">☰</button>
        </div>
    </header>

    <aside id="sidebar" class="sidebar">
        <nav>
            <ul>
                <li><a href="#dashboard">Dashboard</a></li>
                <li><a href="#articles">Articles</a></li>
                <li><a href="#analytics">Analytics</a></li>
                <li><a href="#settings">Settings</a></li>
            </ul>
        </nav>
    </aside>

    <main id="main-content" class="main-content">
        <section id="dashboard" class="dashboard">
            <div class="container">
                <h2>Articles Dashboard</h2>
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
                        <p class="stat-number"><?php echo $publishedToday ?? '0'; ?></p>
                    </div>
                    <div class="stat-card">
                        <h3>Growth</h3>
                        <p class="stat-number"><?php echo $growth ?? '0%'; ?></p>
                    </div>
                </div>

                <div class="dashboard-widgets">
                    <div class="widget">
                        <h3>Recent Articles</h3>
                        <table class="user-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($articles)) {
                                    foreach ($articles as $article) {
                                        echo "<tr>
                                            <td>{$article['id']}</td>
                                            <td>{$article['title']}</td>
                                            <td>{$article['author']}</td>
                                            <td>{$article['status']}</td>
                                            <td>
                                                <a href='edit.php?id={$article['id']}'>Edit</a> |
                                                <a href='delete.php?id={$article['id']}'>Delete</a>
                                            </td>
                                        </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5'>No articles found.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="widget">
                        <h3>Analytics Chart</h3>
                        <div class="chart-placeholder">
                            <p>Chart Placeholder - Article stats over time</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script src="../../assets/js/script.js"></script>
</body>
</html>
