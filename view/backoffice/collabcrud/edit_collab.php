<?php
session_start();

// Mode développeur : permettre l'accès même sans connexion
$isLoggedIn = isset($_SESSION['user_id']);

require_once __DIR__ . "/../../../controller/controllercollab/CollabProjectController.php";

$controller = new CollabProjectController();

// vérifier si un id est reçu
if (!isset($_GET['id'])) {
    die("ID manquant.");
}

$id = $_GET['id'];
$collab = $controller->getById($id);

if (!$collab) {
    die("Projet introuvable.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Collaboration - GameHub Pro</title>
    <link rel="stylesheet" href="../../frontoffice/collaborations.css">
    <style>
        body {
            padding-top: 100px;
        }
        .edit-form-container {
            max-width: 700px;
            margin: 0 auto;
            padding: 2rem;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 255, 136, 0.2);
            border: 1px solid rgba(0, 255, 136, 0.3);
        }
        .edit-form-container h2 {
            color: #00ff88;
            margin-bottom: 2rem;
            text-shadow: 0 0 20px rgba(0, 255, 136, 0.5);
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #00ffea;
            font-weight: 600;
        }
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(0, 255, 136, 0.3);
            border-radius: 10px;
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #00ff88;
            box-shadow: 0 0 15px rgba(0, 255, 136, 0.3);
        }
        .form-group input[type="file"] {
            padding: 8px;
            cursor: pointer;
        }
        .form-group input[type="file"]::file-selector-button {
            padding: 8px 16px;
            background: rgba(0, 255, 136, 0.2);
            color: #00ff88;
            border: 2px solid rgba(0, 255, 136, 0.5);
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            margin-right: 10px;
            transition: all 0.3s ease;
        }
        .form-group input[type="file"]::file-selector-button:hover {
            background: rgba(0, 255, 136, 0.3);
            box-shadow: 0 0 15px rgba(0, 255, 136, 0.5);
        }
        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }
        .dev-mode {
            background: rgba(255, 215, 0, 0.2);
            border: 2px solid rgba(255, 215, 0, 0.5);
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            color: #ffd700;
        }
        .btn-submit {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #00ff88, #00ffea);
            border: none;
            border-radius: 10px;
            color: #000;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 0 25px rgba(0, 255, 136, 0.6);
        }
        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 0 35px rgba(0, 255, 136, 0.9);
        }
        .back-link {
            display: inline-block;
            margin-bottom: 2rem;
            color: #00ff88;
            text-decoration: none;
            padding: 10px 20px;
            border: 2px solid rgba(0, 255, 136, 0.5);
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .back-link:hover {
            background: rgba(0, 255, 136, 0.2);
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div style="display: flex; align-items: center; gap: 10px;">
                <img src="../../frontoffice/assests/logo.png" alt="Logo GameHub Pro" style="width: 50px; height: 50px;">
                <h1 class="logo">GameHub Pro</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="../../frontoffice/index.php" class="super-button">Home</a></li>
                    <li><a href="../../frontoffice/collaborations.php" class="super-button">Collaborations</a></li>
                    <li><a href="collaboration.php" class="super-button">Gestion Collab</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <a href="view_collab.php?id=<?php echo $id; ?>" class="back-link">← Retour à la Collaboration</a>
            
            <div class="edit-form-container">
                <h2>✏️ Modifier la Collaboration</h2>

                <?php if (!$isLoggedIn): ?>
                    <div class="dev-mode">
                        ⚠️ <strong>Mode développeur</strong> : Vous n'êtes pas connecté. Vous pouvez modifier cette collaboration.
                    </div>
                <?php endif; ?>

                <form id="editCollabForm" action="update_collab.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">

                    <div class="form-group">
                        <label for="titre">Titre *</label>
                        <input type="text" id="titre" name="titre" value="<?php echo htmlspecialchars($collab['titre']); ?>">
                    </div>

                    <div class="form-group">
                        <label for="description">Description *</label>
                        <textarea id="description" name="description"><?php echo htmlspecialchars($collab['description']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="statut">Statut *</label>
                        <select id="statut" name="statut">
                            <option value="ouvert" <?php if ($collab['statut']=="ouvert") echo "selected"; ?>>Ouvert</option>
                            <option value="en_cours" <?php if ($collab['statut']=="en_cours") echo "selected"; ?>>En cours</option>
                            <option value="ferme" <?php if ($collab['statut']=="ferme") echo "selected"; ?>>Fermé</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="max_membres">Nombre maximum de membres *</label>
                        <input type="number" id="max_membres" name="max_membres" value="<?php echo $collab['max_membres']; ?>">
                    </div>

                    <div class="form-group">
                        <label for="image">Image - Optionnel</label>
                        <?php if (!empty($collab['image'])): ?>
                            <div style="margin-bottom: 1rem; padding: 1rem; background: rgba(0, 255, 136, 0.1); border-radius: 10px; border: 2px solid rgba(0, 255, 136, 0.3);">
                                <p style="color: #00ffea; font-size: 0.9rem; margin-bottom: 0.5rem; font-weight: 600;">🖼️ Image actuelle :</p>
                                <img src="<?php echo htmlspecialchars($collab['image']); ?>" alt="Image actuelle" style="max-width: 200px; max-height: 200px; border-radius: 10px; border: 2px solid rgba(0, 255, 136, 0.3); display: block;">
                                <p style="color: #aaa; font-size: 0.8rem; margin-top: 0.5rem;">Sélectionnez une nouvelle image pour la remplacer</p>
                            </div>
                        <?php endif; ?>
                        <input type="file" id="image" name="image" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp">
                        <p style="color: #aaa; font-size: 0.85rem; margin-top: 0.5rem;">📎 Format accepté : JPG, PNG, GIF, WebP (max 5MB)</p>
                        <div id="image-preview" style="margin-top: 1rem; display: none;">
                            <p style="color: #00ffea; font-size: 0.9rem; margin-bottom: 0.5rem; font-weight: 600;">👁️ Aperçu de la nouvelle image :</p>
                            <img id="preview-img" src="" alt="Aperçu" style="max-width: 200px; max-height: 200px; border-radius: 10px; border: 2px solid rgba(0, 255, 136, 0.3); display: block;">
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">💾 Mettre à jour la Collaboration</button>
                </form>
            </div>
        </div>
    </main>

    <script>
        // Aperçu de l'image avant upload
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const previewDiv = document.getElementById('image-preview');
            const previewImg = document.getElementById('preview-img');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewDiv.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                previewDiv.style.display = 'none';
            }
        });
    </script>

    <script src="validation.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const validator = new FormValidator('editCollabForm');
            
            // Validation pour titre
            validator.addRule('titre', {
                required: true,
                minLength: 3,
                maxLength: 200
            }, 'Titre requis (3-200 caractères)');
            
            // Validation pour description
            validator.addRule('description', {
                required: true,
                minLength: 10,
                maxLength: 2000
            }, 'Description requise (10-2000 caractères)');
            
            // Validation pour statut
            validator.addRule('statut', {
                required: true
            }, 'Statut requis');
            
            // Validation pour max_membres
            validator.addRule('max_membres', {
                required: true,
                min: 1,
                max: 20
            }, 'Nombre de membres requis (1-20)');
        });
    </script>

</body>
</html>
