<?php

// views/comment/index.php



if (session_status() === PHP_SESSION_NONE) {

    session_start();

}

$success = $_SESSION['success'] ?? null;

$error = $_SESSION['error'] ?? null;

unset($_SESSION['success'], $_SESSION['error']);



// $comments est passé par CommentController::index()

$comments = $comments ?? [];

?>



<!DOCTYPE html>

<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Modération des Commentaires</title>

    <link rel="stylesheet" href="../assets/css/style.css">

    <style>

        /* Styles spécifiques pour cette vue */

        .comment-table {

            width: 100%;

            border-collapse: collapse;

            color: #fff;

        }

        .comment-table th, .comment-table td {

            padding: 0.75rem;

            text-align: left;

            border-bottom: 1px solid rgba(0, 255, 136, 0.2);

            vertical-align: top;

        }

        .comment-table th {

            background: rgba(0, 255, 136, 0.1);

            color: #00ff88;

        }

        .delete-btn {

            background-color: #dc3545;

            color: #fff;

            border: none;

            padding: 5px 10px;

            border-radius: 4px;

            cursor: pointer;

            text-decoration: none;

        }

        .delete-btn:hover { background-color: #a71d2a; }

        .dashboard-nav a {

            color: #00ff88; text-decoration: none; padding: 10px 15px;

            border: 1px solid #00ff88; border-radius: 5px; margin-right: 10px;

            transition: background-color 0.3s;

        }

        .dashboard-nav a:hover { background-color: rgba(0, 255, 136, 0.1); }

        .alert {

            padding: 10px;

            margin: 15px 0;

            border-radius: 4px;

            font-weight: bold;

        }

        .success {

            background-color: #d4edda;

            color: #155724;

            border-color: #c3e6cb;

        }

        .error {

            background-color: #f8d7da;

            color: #721c24;

            border-color: #f5c6cb;

        }

    </style>

</head>

<body>

    <header>

    </header>



    <main class="container">

        <div class="dashboard-nav">

             <a href="ArticleController.php?action=dashboard">⬅️ Retour au Dashboard</a>

        </div>



        <h1>Modération des Commentaires</h1>



        <?php if ($success): ?>

            <div class="alert success"><?php echo htmlspecialchars($success); ?></div>

        <?php endif; ?>

        <?php if ($error): ?>

            <div class="alert error"><?php echo htmlspecialchars($error); ?></div>

        <?php endif; ?>

       

        <div class="widget">

            <h3>Commentaires Récents (<?php echo count($comments); ?>)</h3>

           

            <?php if (empty($comments)): ?>

                <p style="color: #ccc;">Aucun commentaire à modérer.</p>

            <?php else: ?>

            <table class="comment-table">

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Article</th>

                        <th>Auteur</th>

                        <th>Contenu</th>

                        <th>Date</th>

                        <th>Action</th>

                    </tr>

                </thead>

                <tbody>

                    <?php foreach ($comments as $comment): ?>

                    <tr>

                        <td><?php echo htmlspecialchars($comment['id']); ?></td>

                        <td>

                            <a href="ArticleController.php?action=show&id=<?php echo htmlspecialchars($comment['article_id']); ?>" style="color: #00ff88; text-decoration: none;">

                                <?php echo htmlspecialchars($comment['article_title']); ?>

                            </a>

                        </td>

                        <td><?php echo htmlspecialchars($comment['author_name']); ?></td>

                        <td><?php echo htmlspecialchars(substr($comment['content'], 0, 80)) . (strlen($comment['content']) > 80 ? '...' : ''); ?></td>

                        <td><?php echo date('d/m/Y H:i', strtotime($comment['created_at'])); ?></td>

                        <td>

                            <form action="CommentController.php" method="GET" style="display:inline-block;"

                                     onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?');">

                                <input type="hidden" name="action" value="delete">

                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($comment['id']); ?>">

                                <button type="submit" class="delete-btn">Supprimer</button>

                            </form>

                        </td>

                    </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

            <?php endif; ?>

        </div>

    </main>



    <footer>

        <div class="container">

            <p>&copy; 2025 GameHub. Tous droits réservés.</p>

        </div>

    </footer>

</body>

</html>