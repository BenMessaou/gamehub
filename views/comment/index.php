<?php
// views/comment/index.php (NOUVEAU)

if (session_status() === PHP_SESSION_NONE) { session_start(); }
// $comments est passé par CommentController::index()
$success = $_SESSION['success'] ?? null;
unset($_SESSION['success']); 
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modération Commentaires - GameHub Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Styles pour la page de modération */
        .comments-list-section { padding: 50px 0; }
        .data-table.comment-table { width: 100%; border-collapse: collapse; }
        .data-table.comment-table th { background: rgba(255, 0, 150, 0.1); color: #ff0096; }
        .data-table.comment-table td { max-width: 400px; white-space: normal; word-wrap: break-word; }
        .action-btn.delete { background-color: #dc3545; color: white; padding: 5px 10px; text-decoration: none; border-radius: 5px; font-size: 0.9em; transition: background-color 0.3s; }
        .action-btn.delete:hover { background-color: #c82333; }
    </style>
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
        
        <section class="comments-list-section">
            <div class="container">
                <h2>Modération des Commentaires (<?php echo count($comments ?? []); ?>)</h2>
                
                <div class="widget wide-widget">
                    <h3>Liste des Commentaires à Modérer</h3>
                    <table class="data-table comment-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Auteur</th>
                                <th>Article</th>
                                <th>Contenu</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if (!empty($comments) && is_array($comments)) {
                                foreach ($comments as $comment) {
                                    echo "<tr>
                                        <td>" . htmlspecialchars($comment['id']) . "</td>
                                        <td>" . htmlspecialchars($comment['author_name']) . "</td>
                                        <td><a href='ArticleController.php?action=show&id={$comment['article_id']}' style='color: #00ff88;'> " . htmlspecialchars($comment['article_title']) . "</a></td>
                                        <td>" . htmlspecialchars(substr($comment['content'], 0, 100)) . (strlen($comment['content']) > 100 ? '...' : '') . "</td>
                                        <td>" . date('d/m/Y H:i', strtotime($comment['created_at'])) . "</td> 
                                        <td>
                                            <a href='CommentController.php?action=delete&id={$comment['id']}' class='action-btn delete' onclick=\"return confirm('Êtes-vous sûr de vouloir supprimer ce commentaire?');\">Supprimer</a>
                                        </td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6'>Aucun commentaire à modérer.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>

    <script src="../assets/js/script.js"></script>
</body>
</html>