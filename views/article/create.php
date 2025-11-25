<?php
// views/article/create.php
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}
$errors = $_SESSION['article_errors'] ?? []; 
$input = $_SESSION['article_input'] ?? []; 
$global_error = $_SESSION['error'] ?? null; // Récupère l'erreur globale
unset($_SESSION['article_errors'], $_SESSION['article_input'], $_SESSION['error']);

$current_user_id = 1; 
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameHub Admin | Créer Article</title>
    <link rel="stylesheet" href="../assets/css/style.css"> 
    <link rel="stylesheet" href="../assets/css/frontstyle.css"> 
    <style>.error-message { color: #ff0055; margin-top: 5px; font-size: 0.9em; }</style>
</head>
<body>
    <header>
        <div class="container">
            <h1 class="logo">GameHub Admin</h1>
            <nav>
                <ul>
                    <li><a href="ArticleController.php?action=list" class="super-button">Front Office</a></li>
                    <li><a href="ArticleController.php?action=dashboard" class="super-button">Dashboard</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div id="main-content">
        <div class="container">
            <div class="form-section">
                <h2>Créer un nouvel Article</h2>
                
                <?php if ($global_error): ?>
                    <p class="message error-global"><?php echo $global_error; ?></p>
                <?php endif; ?>
                
                <form action="ArticleController.php?action=store" method="POST" novalidate>
                    
                    <div class="form-group">
                        <label for="title">Titre :</label>
                        <input type="text" name="title" id="title" class="form-control" 
                               value="<?php echo htmlspecialchars($input['title'] ?? ''); ?>">
                        <?php if (isset($errors['title'])): ?>
                            <p class="error-message"><?php echo $errors['title']; ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="content">Contenu :</label>
                        <textarea name="content" id="content" class="form-control" rows="10"><?php echo htmlspecialchars($input['content'] ?? ''); ?></textarea>
                        <?php if (isset($errors['content'])): ?>
                            <p class="error-message"><?php echo $errors['content']; ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <input type="hidden" name="user_id" value="<?php echo $current_user_id; ?>">
                    
                    <button type="submit" class="submit-btn super-button">Enregistrer l'Article</button>
                </form>

                <a href="ArticleController.php?action=dashboard" class="back-link">← Retour au Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>