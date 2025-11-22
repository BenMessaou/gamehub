<?php
include '../../../controller/ProjectController.php';
require_once __DIR__ . '/../../../model/Project.php';

$error = '';
$project = null;
$plateformesValue = '';
$tagsValue = '';
$screensValue = '';
$projectC = new ProjectController();

// Récupérer les données du projet (from GET or POST)
$projectId = $_GET['id'] ?? $_POST['id'] ?? null;

if ($projectId) {
    $project = $projectC->showProject($projectId);
    if ($project) {
        $plateformesValue = implode(", ", json_decode($project['plateformes'] ?? '[]', true) ?? []);
        $tagsValue = implode(", ", json_decode($project['tags'] ?? '[]', true) ?? []);
        $screensValue = implode(", ", json_decode($project['screenshots'] ?? '[]', true) ?? []);
    }
}

// Mise à jour
if (
    isset($_POST['id'], $_POST['nom'], $_POST['developpeur'], $_POST['date_creation'], $_POST['categorie'], $_POST['description'], $_POST['developpeur_id'])
) {
    if (
        !empty($_POST['id']) && !empty($_POST['nom']) && !empty($_POST['developpeur']) &&
        !empty($_POST['date_creation']) && !empty($_POST['categorie']) && !empty($_POST['description']) &&
        !empty($_POST['developpeur_id'])
    ) {

        // Convertir les champs JSON
        $plateformes = !empty($_POST['plateformes']) ? explode(",", $_POST['plateformes']) : [];
        $tags        = !empty($_POST['tags']) ? explode(",", $_POST['tags']) : [];
        $screenshots = !empty($_POST['screenshots']) ? explode(",", $_POST['screenshots']) : [];

        $project = new Project(
            $_POST['id'],
            $_POST['nom'],
            $_POST['developpeur'],
            $_POST['date_creation'],
            $_POST['categorie'],
            $_POST['description'],
            $_POST['image'] ?? null,
            $_POST['trailer'] ?? null,
            (int)$_POST['developpeur_id'],
            !empty($_POST['age_recommande']) ? (int)$_POST['age_recommande'] : null,
            $_POST['lieu'] ?? null,
            $_POST['lien_telechargement'] ?? null,
            $plateformes,
            $tags,
            $screenshots
        );

        $projectC->updateProject($project, $_POST['id']);

        header('Location: projectlist.php');
        exit();
    } else {
        $error = 'Please fill all required fields.';
    }
} elseif ($project) {
    $plateformesValue = implode(", ", json_decode($project['plateformes'] ?? '[]', true) ?? []);
    $tagsValue = implode(", ", json_decode($project['tags'] ?? '[]', true) ?? []);
    $screensValue = implode(", ", json_decode($project['screenshots'] ?? '[]', true) ?? []);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>GameHub | Update Project</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Orbitron:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/lineicons.css" />
    <link rel="stylesheet" href="styles.css" />
    <style>
        .layout-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
        }
        .update-card {
            background: var(--bg-card);
            border-radius: var(--radius);
            border: 1px solid var(--border);
            padding: 28px;
            box-shadow: var(--shadow);
        }
        .form-label {
            font-weight: 600;
            color: var(--text);
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
            box-shadow: 0 0 18px rgba(255,0,199,0.35);
            background: rgba(0, 0, 0, 0.65);
        }
        textarea.neon-field {
            min-height: 140px;
        }
        .section-title {
            font-family: Orbitron, sans-serif;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--cyan);
            margin-bottom: 16px;
        }
        .cta-zone {
            margin-top: 30px;
            display: flex;
            justify-content: center;
            gap: 16px;
            flex-wrap: wrap;
        }
        .btn-gradient {
            padding: 12px 36px;
            border-radius: 999px;
            border: none;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(120deg, #ff00c7, #7c00ff, #00ffea);
            background-size: 200% 200%;
            transition: var(--transition);
        }
        .btn-gradient:hover {
            background-position: 100% 0;
            transform: translateY(-2px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.45);
        }
        .btn-ghost {
            border: 1px solid var(--border);
            border-radius: 999px;
            padding: 12px 24px;
            color: var(--text);
            text-decoration: none;
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
            <a href="addProject.php" class="nav-link">Add Project</a>
        </nav>
    </div>
</header>

<main class="admin-main">
    <div class="container">

        <section class="admin-section">
            <h2>Modifier un projet</h2>
            <p class="section-subtitle">Ajustez les informations du projet puis enregistrez pour mettre à jour la fiche GameHub.</p>

            <?php if (!empty($error)) { ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php } ?>

            <?php if (!$project) { ?>
                <div class="alert alert-warning">Project not found. <a href="projectlist.php">Return to project list</a></div>
            <?php } else { ?>

            <form method="POST" class="form-neon">
                <input type="hidden" name="id" value="<?= $project['id'] ?? '' ?>">

                <div class="layout-grid">
                    <div class="update-card">
                        <p class="section-title">Informations principales</p>
                        <div class="mb-3">
                            <label class="form-label">Nom du projet</label>
                            <input type="text" class="form-control neon-field" name="nom" required value="<?= $project['nom'] ?? '' ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Développeur</label>
                            <input type="text" class="form-control neon-field" name="developpeur" required value="<?= $project['developpeur'] ?? '' ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date de création</label>
                            <input type="date" class="form-control neon-field" name="date_creation" required value="<?= $project['date_creation'] ?? '' ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catégorie</label>
                            <select class="form-select neon-field" name="categorie" required>
                                <option value="" disabled>Choose a category</option>
                                <?php
                                $categories = ["Action", "Aventure", "RPG", "Stratégie", "Puzzle", "Plateforme", "Simulation", "Course", "Horreur", "Sport", "Combat", "Autres"];
                                foreach ($categories as $cat) {
                                    $sel = ($project['categorie'] ?? '') === $cat ? 'selected' : '';
                                    echo "<option value=\"$cat\" $sel>$cat</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control neon-field" name="description" required><?= $project['description'] ?? '' ?></textarea>
                        </div>
                    </div>

                    <div class="update-card">
                        <p class="section-title">Media & Meta</p>
                        <div class="mb-3">
                            <label class="form-label">Image URL</label>
                            <input type="text" class="form-control neon-field" name="image" value="<?= $project['image'] ?? '' ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Trailer URL</label>
                            <input type="text" class="form-control neon-field" name="trailer" value="<?= $project['trailer'] ?? '' ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lien de téléchargement</label>
                            <input type="text" class="form-control neon-field" name="lien_telechargement" value="<?= $project['lien_telechargement'] ?? '' ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Plateformes (séparées par ,)</label>
                            <input type="text" class="form-control neon-field" name="plateformes" value="<?= $plateformesValue ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tags (séparés par ,)</label>
                            <input type="text" class="form-control neon-field" name="tags" value="<?= $tagsValue ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Screenshots URLs (séparées par ,)</label>
                            <input type="text" class="form-control neon-field" name="screenshots" value="<?= $screensValue ?>">
                        </div>
                    </div>

                    <div class="update-card">
                        <p class="section-title">Données internes</p>
                        <div class="mb-3">
                            <label class="form-label">Développeur ID</label>
                            <input type="number" class="form-control neon-field" name="developpeur_id" required value="<?= $project['developpeur_id'] ?? '' ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Âge recommandé</label>
                            <input type="number" class="form-control neon-field" name="age_recommande" value="<?= $project['age_recommande'] ?? '' ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lieu</label>
                            <input type="text" class="form-control neon-field" name="lieu" value="<?= $project['lieu'] ?? '' ?>">
                        </div>
                    </div>
                </div>

                <div class="cta-zone">
                    <button type="submit" class="btn-gradient">Mettre à jour le projet</button>
                    <a href="projectlist.php" class="btn-ghost">Annuler</a>
                </div>
            </form>
            <?php } ?>
        </section>

    </div>
</main>

<footer class="admin-footer">
    GameHub Admin &bull; Propulsé par la team XR Labs
</footer>

<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
