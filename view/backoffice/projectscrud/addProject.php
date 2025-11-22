<?php
include '../../../controller/ProjectController.php';
require_once __DIR__ . '/../../../model/Project.php';

$error = "";
$projectC = new ProjectController();

// Créer le dossier uploads s'il n'existe pas
$uploadDir = __DIR__ . '/uploads/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// ======= Vérification formulaire =======
if (
    isset($_POST["nom"]) &&
    isset($_POST["developpeur"]) &&
    isset($_POST["date_creation"]) &&
    isset($_POST["categorie"]) &&
    isset($_POST["description"]) &&
    isset($_POST["developpeur_id"])
) {
    if (
        !empty($_POST["nom"]) &&
        !empty($_POST["developpeur"]) &&
        !empty($_POST["date_creation"]) &&
        !empty($_POST["categorie"]) &&
        !empty($_POST["description"]) &&
        !empty($_POST["developpeur_id"])
    ) {

        // Gestion de l'upload d'image
        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['image'];
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            $maxSize = 5 * 1024 * 1024; // 5MB
            
            if (in_array($file['type'], $allowedTypes) && $file['size'] <= $maxSize) {
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $fileName = uniqid('project_', true) . '.' . $extension;
                $targetPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                    // Chemin relatif pour la base de données (accessible depuis frontoffice)
                    $imagePath = '../backoffice/projectscrud/uploads/' . $fileName;
                } else {
                    $error = "Erreur lors de l'upload de l'image.";
                }
            } else {
                $error = "Format d'image non supporté ou fichier trop volumineux (max 5MB).";
            }
        } elseif (isset($_POST['image_url']) && !empty($_POST['image_url'])) {
            // Fallback: utiliser l'URL si fournie
            $imagePath = $_POST['image_url'];
        }

        // conversion tableaux JSON
        $plateformes = !empty($_POST['plateformes']) ? explode(",", $_POST['plateformes']) : [];
        $tags        = !empty($_POST['tags']) ? explode(",", $_POST['tags']) : [];
        $screenshots = !empty($_POST['screenshots']) ? explode(",", $_POST['screenshots']) : [];

        if (empty($error)) {
            // Création de l'objet Project
            $project = new Project(
                null,
                $_POST['nom'],
                $_POST['developpeur'],
                $_POST['date_creation'],
                $_POST['categorie'],
                $_POST['description'],
                $imagePath,
                $_POST['trailer'] ?? null,
                (int)$_POST['developpeur_id'],
                !empty($_POST['age_recommande']) ? (int)$_POST['age_recommande'] : null,
                $_POST['lieu'] ?? null,
                $_POST['lien_telechargement'] ?? null,
                $plateformes,
                $tags,
                $screenshots
            );

            $projectC->addProject($project);

            header('Location: projectlist.php');
            exit;
        }
    } else {
        $error = "Missing information";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Project</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Orbitron:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="styles.css">
    <style>
        .add-project-hero {
            display: flex;
            flex-direction: column;
            gap: 18px;
            padding: 36px;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            background: linear-gradient(135deg, rgba(255,0,199,0.25), rgba(0,255,234,0.12));
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.45);
        }
        .add-project-hero h1 {
            font-family: Orbitron, sans-serif;
            font-size: 2.6rem;
            margin: 0;
            background: linear-gradient(90deg, #ff00c7, #00ffea);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .add-project-hero p {
            color: var(--text-light);
            max-width: 760px;
        }
        .form-card {
            margin-top: 32px;
            background: var(--bg-card);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            padding: 32px;
            box-shadow: var(--shadow);
        }
        .form-section-title {
            font-family: Orbitron, sans-serif;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 1.1rem;
            margin-bottom: 18px;
            color: var(--cyan);
        }
        .neon-field {
            background: rgba(5, 0, 20, 0.75);
            border: 1px solid rgba(255, 0, 199, 0.2);
            color: #fff;
            border-radius: var(--radius-sm);
            padding: 12px 16px;
            transition: var(--transition-fast);
        }
        .neon-field:focus {
            border-color: var(--primary);
            box-shadow: 0 0 18px rgba(255, 0, 199, 0.35);
            background: rgba(0, 0, 0, 0.65);
        }
        .file-input {
            padding: 10px;
            cursor: pointer;
        }
        .file-input::-webkit-file-upload-button {
            background: linear-gradient(90deg, #ff00c7, #00ffea);
            color: #000;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            margin-right: 12px;
        }
        .file-input::-webkit-file-upload-button:hover {
            background: linear-gradient(90deg, #00ffea, #ff00c7);
        }
        label.form-label {
            font-weight: 600;
            color: var(--text);
        }
        .stack-2 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
        }
        textarea.neon-field {
            min-height: 130px;
        }
        .badges-helper {
            font-size: 0.85rem;
            color: var(--text-light);
        }
        .cta-zone {
            margin-top: 24px;
            display: flex;
            justify-content: center;
        }
        .btn-submit {
            padding: 12px 44px;
            border-radius: 999px;
            border: none;
            font-size: 1rem;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(120deg, #ff00c7, #7c00ff, #00ffea);
            background-size: 200% 200%;
            box-shadow: 0 18px 40px rgba(255, 0, 199, 0.35);
            transition: var(--transition);
        }
        .btn-submit:hover {
            background-position: 100% 0;
            transform: translateY(-3px);
            box-shadow: 0 25px 50px rgba(0, 255, 234, 0.35);
        }
        small[id$="_error"] {
            color: var(--danger);
        }
    </style>
</head>

<body class="admin-body">
<header class="admin-header">
    <div class="container">
        <div class="admin-logo">
            <img src="../../frontoffice/assests/logo.png" alt="GameHub Logo">
            GameHub Admin
        </div>
        <nav class="admin-nav">
            <a href="index1.html" class="nav-link">Dashboard</a>
            <a href="projectlist.php" class="nav-link">Projects</a>
            <a href="addProject.php" class="nav-link active">Add Project</a>
        </nav>
    </div>
</header>

<main class="admin-main">
    <div class="container">

        <section class="add-project-hero">
            <h1>Ajouter un nouveau projet</h1>
            <p>
                Donnez vie à votre prochain jeu ou outil en remplissant les informations clés.
                Les champs ci-dessous alimentent la base GameHub et permettent à l’équipe marketing
                de préparer les fiches produit et assets à mettre en avant.
            </p>
        </section>

        <section class="form-card">
            <?php if (!empty($error)) { ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php } ?>

            <form action="" method="POST" enctype="multipart/form-data" id="addProjectForm" class="form-neon">

                <div class="form-section">
                    <p class="form-section-title">Infos principales</p>
                    <div class="stack-2">
                        <div>
                            <label class="form-label">Nom du projet</label>
                            <input type="text" class="form-control neon-field" name="nom" id="nom" required>
                            <small id="nom_error"></small>
                        </div>
                        <div>
                            <label class="form-label">Développeur</label>
                            <input type="text" class="form-control neon-field" name="developpeur" id="developpeur" required>
                            <small id="developpeur_error"></small>
                        </div>
                        <div>
                            <label class="form-label">Date de création</label>
                            <input type="date" class="form-control neon-field" name="date_creation" id="date_creation" required>
                            <small id="date_creation_error"></small>
                        </div>
                        <div>
                            <label class="form-label">Catégorie</label>
                            <select class="form-select neon-field" name="categorie" id="categorie">
                                <option value="">-- Choisir --</option>
                                <option>Jeu</option>
                                <option>Application</option>
                                <option>Outil</option>
                                <option>Projet Web</option>
                            </select>
                            <small id="categorie_error"></small>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <p class="form-section-title">Description & assets</p>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control neon-field" name="description" id="description" required></textarea>
                        <small id="description_error"></small>
                    </div>
                    <div class="stack-2">
                        <div>
                            <label class="form-label">Image du projet</label>
                            <input type="file" class="form-control neon-field file-input" name="image" id="image" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp">
                            <small class="text-muted" style="color: var(--text-light); font-size: 0.85rem; display: block; margin-top: 6px;">
                                Formats acceptés: JPG, PNG, GIF, WebP (max 5MB)
                            </small>
                            <input type="hidden" name="image_url" id="image_url" value="">
                        </div>
                        <div>
                            <label class="form-label">Lien Trailer (YouTube / MP4)</label>
                            <input type="text" class="form-control neon-field" name="trailer" placeholder="https://youtu.be/...">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <p class="form-section-title">Meta & distribution</p>
                    <div class="stack-2">
                        <div>
                            <label class="form-label">Développeur ID</label>
                            <input type="number" class="form-control neon-field" name="developpeur_id" id="developpeur_id" required>
                        </div>
                        <div>
                            <label class="form-label">Âge recommandé</label>
                            <input type="number" class="form-control neon-field" name="age_recommande">
                        </div>
                        <div>
                            <label class="form-label">Lieu</label>
                            <input type="text" class="form-control neon-field" name="lieu">
                        </div>
                        <div>
                            <label class="form-label">Lien de téléchargement</label>
                            <input type="text" class="form-control neon-field" name="lien_telechargement" placeholder="https://gamehub.store/...">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <p class="form-section-title">Tags & plateformes</p>
                    <div class="stack-2">
                        <div>
                            <label class="form-label">Plateformes (séparer par des virgules)</label>
                            <input type="text" class="form-control neon-field" name="plateformes" placeholder="Windows, Linux, Android">
                            <p class="badges-helper">Ces informations alimentent les filtres du store.</p>
                        </div>
                        <div>
                            <label class="form-label">Tags (séparer par des virgules)</label>
                            <input type="text" class="form-control neon-field" name="tags" placeholder="Action, 3D, Multijoueur">
                            <p class="badges-helper">Mots-clés utilisés pour le SEO interne.</p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="form-label">Screenshots URLs (séparer par des virgules)</label>
                        <input type="text" class="form-control neon-field" name="screenshots" placeholder="https://cdn/game1.jpg, https://cdn/game2.jpg">
                    </div>
                </div>

                <div class="cta-zone">
                    <button type="submit" class="btn-submit">Ajouter le projet</button>
                </div>

            </form>
        </section>

    </div>
</main>

<footer class="admin-footer">
    GameHub Admin • Propulsé par la team XR Labs
</footer>

<!-- IMPORT DU JS -->
<script src="assets/js/addProject.js"></script>

</body>
</html>
