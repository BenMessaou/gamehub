// views/article/edit.php

<?php
// Démarrer la session (nécessaire pour les messages d'erreur)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Les variables d'entrée peuvent venir soit d'une erreur de validation (input), soit de la DB (article)
$errors = $_SESSION['errors'] ?? [];
$input = $_SESSION['input'] ?? [];

// Si le formulaire a échoué, on utilise $input. Sinon, on utilise les données $article (chargées par le contrôleur)
$data = empty($input) ? ($article ?? []) : $input;

// On efface les sessions après utilisation
unset($_SESSION['errors'], $_SESSION['input']);

// Si l'article n'existe pas, on affiche un message d'erreur
if (empty($article) && empty($input['id'])) {
    die('<div style="color: red; padding: 20px; font-size: 20px;">ERREUR : Article non trouvé ou ID manquant.</div>');
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Article #<?php echo htmlspecialchars($data['id'] ?? 'N/A'); ?> - GameHub Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Styles spécifiques au formulaire de modification */
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
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: bold; color: #ccc; }
        .form-group input[type="text"], .form-group textarea, .form-group select {
            width: 100%; padding: 10px; border: 1px solid rgba(0, 255, 136, 0.5); border-radius: 5px; background: #1a1a2e; color: white;
        }
        .error-message { color: #ff6b6b; margin-top: 5px; font-size: 0.9em; }
        .submit-btn { display: block; width: 100%; padding: 12px; text-align: center; font-size: 1.2rem; }
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
            <h2>Modification de l'Article : #<?php echo htmlspecialchars($data['id'] ?? ''); ?></h2>

            <form action="ArticleController.php?action=update" method="POST">
                
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($data['id'] ?? ''); ?>">

                <div class="form-group">
                    <label for="title">Titre de l'Article :</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($data['title'] ?? ''); ?>" required>
                    <?php if (isset($errors['title'])): ?>
                        <p class="error-message"><?php echo $errors['title']; ?></p>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="content">Contenu / Description :</label>
                    <textarea id="content" name="content" rows="10" required><?php echo htmlspecialchars($data['content'] ?? ''); ?></textarea>
                    <?php if (isset($errors['content'])): ?>
                        <p class="error-message"><?php echo $errors['content']; ?></p>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="user_id">Auteur (Sélectionner l'ID) :</label>
                    <select id="user_id" name="user_id" required>
                        <option value="">-- Choisir un auteur --</option>
                        <?php $selected_user_id = $data['user_id'] ?? 0; ?>
                        
                        <option value="1" <?php echo ($selected_user_id == 1) ? 'selected' : ''; ?>>1 - Admin GameHub</option>
                        <option value="2" <?php echo ($selected_user_id == 2) ? 'selected' : ''; ?>>2 - Super Gamer</option>
                    </select>
                    <?php if (isset($errors['user_id'])): ?>
                        <p class="error-message"><?php echo $errors['user_id']; ?></p>
                    <?php endif; ?>
                </div>
                
                <button type="submit" class="super-button submit-btn">Sauvegarder les Modifications</button>

            </form>
        </div>
    </main>
</body>
</html>