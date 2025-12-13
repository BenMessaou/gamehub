<?php
session_start();

// Mode développeur : permettre l'accès même sans connexion
$isLoggedIn = isset($_SESSION['user_id']);
$defaultOwnerId = 1; // ID par défaut pour le développeur

// Chemins relatifs depuis view/backoffice/collabcrud/
require_once __DIR__ . "/../../../controller/controllercollab/CollabProjectController.php";
// Note: CollabProject.php est déjà inclus par CollabProjectController.php

// Variables pour les messages
$message = '';
$messageType = ''; // 'success' ou 'error'
$createdId = null;

// Si le formulaire est envoyé
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Utiliser l'ID de session s'il existe, sinon utiliser celui du formulaire ou l'ID par défaut
    $owner_id = $isLoggedIn ? $_SESSION['user_id'] : (isset($_POST['owner_id']) ? intval($_POST['owner_id']) : $defaultOwnerId);
    $titre = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $max_membres = isset($_POST['max_membres']) ? intval($_POST['max_membres']) : 5;
    
    // Gestion de l'upload d'image
    $imagePath = null;
    
    // Vérifier si un fichier image a été uploadé
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        if (in_array($file['type'], $allowedTypes) && $file['size'] <= $maxSize) {
            // Créer le dossier uploads s'il n'existe pas
            $uploadDir = __DIR__ . '/../../frontoffice/backoffice/uploads/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $fileName = uniqid('collab_', true) . '.' . $extension;
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                // Chemin absolu depuis la racine du serveur web pour l'affichage
                $imagePath = '/gamehubprjt/view/frontoffice/backoffice/uploads/' . $fileName;
            } else {
                $message = 'Error uploading the image. Please try again.';
                $messageType = 'error';
            }
        } else {
            $message = 'Format d\'image non supporté ou fichier trop volumineux (max 5MB).';
            $messageType = 'error';
        }
    } elseif (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $message = 'Error uploading the image. Error code: ' . $_FILES['image']['error'];
        $messageType = 'error';
    }

    // Validation
    if (empty($titre) || empty($description)) {
        $message = 'Veuillez remplir tous les champs requis.';
        $messageType = 'error';
    } elseif ($max_membres < 1 || $max_membres > 20) {
        $message = 'The number of members must be between 1 and 20.';
        $messageType = 'error';
    } elseif ($messageType !== 'error') {
        $collab = new CollabProject(
            null,
            $owner_id,
            $titre,
            $description,
            date("Y-m-d"),
            "ouvert",
            $max_membres,
            $imagePath
        );

        $controller = new CollabProjectController();
        $createdId = $controller->create($collab);

        if ($createdId) {
            $message = '✅ Collaboration created successfully! Your collaborative project is now available.';
            $messageType = 'success';
        } else {
            $message = '❌ Error: Unable to create the collaboration. Please try again.';
            $messageType = 'error';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create a Collaborative Project - GameHub Pro</title>
    <link rel="stylesheet" href="../../frontoffice/collaborations.css">
    <style>
        body {
            padding-top: 100px;
        }
        .create-form-container {
            max-width: 700px;
            margin: 0 auto;
            padding: 2rem;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 255, 136, 0.2);
            border: 1px solid rgba(0, 255, 136, 0.3);
        }
        .create-form-container h2 {
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
        .form-group textarea {
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
        .form-group textarea:focus {
            outline: none;
            border-color: #00ff88;
            box-shadow: 0 0 15px rgba(0, 255, 136, 0.3);
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
        /* Le bouton utilise maintenant la classe super-button */
        .message {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 2rem;
            font-weight: 600;
            text-align: center;
            animation: slideIn 0.5s ease;
        }
        .message.success {
            background: rgba(0, 255, 234, 0.2);
            color: #00ffea;
            border: 2px solid rgba(0, 255, 234, 0.5);
            box-shadow: 0 0 20px rgba(0, 255, 234, 0.3);
        }
        .message.error {
            background: rgba(255, 51, 92, 0.2);
            color: #ff335c;
            border: 2px solid rgba(255, 51, 92, 0.5);
            box-shadow: 0 0 20px rgba(255, 51, 92, 0.3);
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
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
            <a href="../../frontoffice/collaborations.php" class="super-button" style="margin-bottom: 2rem; display: inline-block;">← Back to Collaborations</a>
            
            <div class="create-form-container">
                <h2>Create a Collaborative Project</h2>

                <?php if (!empty($message)): ?>
                    <div class="message <?php echo $messageType; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <?php if (!$isLoggedIn): ?>
                    <div class="dev-mode">
                        ⚠️ <strong>Developer mode</strong>: You are not logged in. Please enter a user ID to create the collaboration.
                    </div>
                <?php endif; ?>

                <?php if ($messageType === 'success'): ?>
                    <script>
                        // Masquer le formulaire après succès
                        document.addEventListener('DOMContentLoaded', function() {
                            var form = document.querySelector('form');
                            if (form) {
                                form.style.opacity = '0.5';
                                form.style.pointerEvents = 'none';
                            }
                            
                            // Redirection après 3 secondes avec timestamp pour éviter le cache
                            setTimeout(function() {
                                window.location.href = '../../frontoffice/collaborations.php?created=' + Date.now();
                            }, 3000);
                        });
                    </script>
                <?php endif; ?>

                <?php if ($messageType !== 'success'): ?>
                <form id="createCollabForm" action="" method="POST" enctype="multipart/form-data">
                    
                    <?php if (!$isLoggedIn): ?>
                    <div class="form-group">
                        <label for="owner_id">ID Utilisateur (Owner) *</label>
                        <input type="number" id="owner_id" name="owner_id" value="<?php echo $defaultOwnerId; ?>">
                    </div>
                    <?php else: ?>
                        <input type="hidden" name="owner_id" value="<?php echo $_SESSION['user_id']; ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="titre">Project Title *</label>
                        <input type="text" id="titre" name="titre" placeholder="Ex: Strategy Game Development">
                    </div>

                    <div class="form-group">
                        <label for="description">Description *</label>
                        <textarea id="description" name="description" placeholder="Describe your collaborative project..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="max_membres">Maximum Number of Members *</label>
                        <input type="number" id="max_membres" name="max_membres" value="5">
                    </div>

                    <div class="form-group">
                        <label for="image">Image - Optionnel</label>
                        <input type="file" id="image" name="image" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp">
                        <p style="color: #aaa; font-size: 0.85rem; margin-top: 0.5rem;">📎 Format accepté : JPG, PNG, GIF, WebP (max 5MB)</p>
                        <div id="image-preview" style="margin-top: 1rem; display: none;">
                            <p style="color: #00ffea; font-size: 0.9rem; margin-bottom: 0.5rem; font-weight: 600;">👁️ Aperçu de l'image :</p>
                            <img id="preview-img" src="" alt="Aperçu" style="max-width: 200px; max-height: 200px; border-radius: 10px; border: 2px solid rgba(0, 255, 136, 0.3); display: block;">
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">🚀 Create Collaboration</button>

                </form>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script src="validation.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const validator = new FormValidator('createCollabForm');
            
            <?php if (!$isLoggedIn): ?>
            // Validation pour owner_id (si mode développeur)
            validator.addRule('owner_id', {
                required: true,
                min: 1
            }, 'ID utilisateur requis (minimum 1)');
            <?php endif; ?>
            
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
            
            // Validation pour max_membres
            validator.addRule('max_membres', {
                required: true,
                min: 1,
                max: 20
            }, 'Nombre de membres requis (1-20)');
        });
        
        // Gestion de l'aperçu d'image
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('image-preview');
        const previewImg = document.getElementById('preview-img');
        
        if (imageInput && imagePreview && previewImg) {
            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                
                if (file) {
                    // Vérifier le type de fichier
                    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                    if (!allowedTypes.includes(file.type)) {
                        alert('Format d\'image non supporté. Formats acceptés : JPG, PNG, GIF, WebP');
                        imageInput.value = '';
                        imagePreview.style.display = 'none';
                        return;
                    }
                    
                    // Vérifier la taille (5MB max)
                    const maxSize = 5 * 1024 * 1024; // 5MB
                    if (file.size > maxSize) {
                        alert('Fichier trop volumineux. Taille maximum : 5MB');
                        imageInput.value = '';
                        imagePreview.style.display = 'none';
                        return;
                    }
                    
                    // Afficher l'aperçu
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        imagePreview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                } else {
                    imagePreview.style.display = 'none';
                }
            });
        }
    </script>

</body>
</html>
