
<?php
// views/article/create.php (COMPLET)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$errors = $_SESSION['errors'] ?? [];
$input = $_SESSION['input'] ?? [];
unset($_SESSION['errors'], $_SESSION['input']);

// Simulation de l'utilisateur connecté (à remplacer par la vraie logique d'authentification)
$current_user_id = 1; 
$current_user_name = "Admin User";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Article - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .form-section { max-width: 800px; margin: 40px auto; padding: 30px; background: rgba(0, 0, 0, 0.7); border-radius: 10px; box-shadow: 0 0 20px rgba(0, 255, 136, 0.3); }
        .form-section h2 { color: #00ff88; margin-bottom: 25px; }
        .form-group label { display: block; margin-bottom: 8px; color: #fff; font-weight: bold; }
        .form-group input[type="text"], .form-group textarea, .form-group select { width: 100%; padding: 12px; border: 1px solid #00ff88; border-radius: 5px; background: #1a1a1a; color: #fff; box-sizing: border-box; }
        .form-group textarea { resize: vertical; min-height: 200px; }
        .error-message { color: #ff0096; font-size: 0.9em; margin-top: 5px; }
        .submit-btn { padding: 12px 25px; background-color: #00ff88; color: #000; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; transition: background-color 0.3s; }
        .submit-btn:hover { background-color: #00e077; }
        .back-link { display: inline-block; margin-top: 20px; color: #00ff88; text-decoration: none; }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1 class="logo">GameHub Admin</h1>
            <nav>
                <ul>
                    <li><a href="ArticleController.php?action=dashboard" class="super-button">Dashboard</a></li>
                    <li><a href="CommentController.php?action=index" class="super-button">Modérer Commentaires</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <main>
        <section class="form-section">
            <h2>Créer un Nouvel Article</h2>
            
            <form action="ArticleController.php?action=store" method="POST">
                
                <div class="form-group">
                    <label for="title">Titre de l'article *</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($input['title'] ?? ''); ?>" required>
                    <?php if (isset($errors['title'])): ?>
                        <p class="error-message"><?php echo $errors['title']; ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="content">Contenu de l'article *</label>
                    <textarea id="content" name="content" required><?php echo htmlspecialchars($input['content'] ?? ''); ?></textarea>
                    <?php if (isset($errors['content'])): ?>
                        <p class="error-message"><?php echo $errors['content']; ?></p>
                    <?php endif; ?>
                </div>
                
                <input type="hidden" name="user_id" value="<?php echo $current_user_id; ?>">
                <p style="color: #aaa; font-style: italic;">Auteur actuel: <?php echo htmlspecialchars($current_user_name); ?></p>
                
                <button type="submit" class="submit-btn">Publier l'Article</button>
            </form>
            
            <a href="ArticleController.php?action=dashboard" class="back-link">← Retour au Dashboard</a>
        </section>
    </main>
    
    <script src="../assets/js/script.js"></script>
</body>
</html>