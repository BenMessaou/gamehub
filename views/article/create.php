// views/article/create.php (CODE COMPLET)

<?php
// On démarre la session (nécessaire pour les messages d'erreur)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// On récupère les erreurs et les anciennes valeurs si le formulaire a échoué
$errors = $_SESSION['errors'] ?? [];
$input = $_SESSION['input'] ?? [];

// On efface les sessions pour éviter de réafficher les messages après rafraîchissement
unset($_SESSION['errors'], $_SESSION['input']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Article - GameHub Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Style spécifique au formulaire de création (pour s'assurer qu'il s'affiche bien) */
        .form-container {
            max-width: 800px;
            margin: 50px auto;
            background: rgba(0, 0, 0, 0.8);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 30px rgba(0, 255, 136, 0.3);
            border: 1px solid rgba(0, 255, 136, 0.3);
            color: white;
        }
        .form-container h2 {
            color: #00ff88;
            margin-bottom: 25px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #ccc;
        }
        .form-group input[type="text"],
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid rgba(0, 255, 136, 0.5);
            border-radius: 5px;
            background: #1a1a2e;
            color: white;
            transition: border-color 0.3s;
        }
        .form-group input[type="text"]:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #00ff88;
            box-shadow: 0 0 10px rgba(0, 255, 136, 0.5);
        }
        .error-message {
            color: #ff6b6b;
            margin-top: 5px;
            font-size: 0.9em;
        }
        .submit-btn {
            display: block;
            width: 100%;
            padding: 12px;
            text-align: center;
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1 class="logo">GameHub Admin</h1>
            <nav>
                <ul>
                    <li><a href="ArticleController.php?action=dashboard" class="super-button">Dashboard</a></li>
                    <li><a href="ArticleController.php?action=list" class="super-button">Retour au Front</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main id="main-content">
        <div class="form-container">
            <h2>Créer un Nouvel Article</h2>

            <form action="ArticleController.php?action=store" method="POST">
                
                <div class="form-group">
                    <label for="title">Titre de l'Article (max 255 caractères) :</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($input['title'] ?? ''); ?>" required>
                    <?php if (isset($errors['title'])): ?>
                        <p class="error-message"><?php echo $errors['title']; ?></p>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="content">Contenu / Description :</label>
                    <textarea id="content" name="content" rows="10" required><?php echo htmlspecialchars($input['content'] ?? ''); ?></textarea>
                    <?php if (isset($errors['content'])): ?>
                        <p class="error-message"><?php echo $errors['content']; ?></p>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="user_id">Auteur (Sélectionner l'ID) :</label>
                    <select id="user_id" name="user_id" required>
                        <option value="">-- Choisir un auteur --</option>
                        <option value="1" <?php echo (isset($input['user_id']) && $input['user_id'] == 1) ? 'selected' : ''; ?>>1 - Admin GameHub</option>
                        <option value="2" <?php echo (isset($input['user_id']) && $input['user_id'] == 2) ? 'selected' : ''; ?>>2 - Super Gamer</option>
                    </select>
                    <?php if (isset($errors['user_id'])): ?>
                        <p class="error-message"><?php echo $errors['user_id']; ?></p>
                    <?php endif; ?>
                </div>
                
                <button type="submit" class="super-button submit-btn">Créer l'Article</button>

            </form>
        </div>
    </main>
</body>
</html>