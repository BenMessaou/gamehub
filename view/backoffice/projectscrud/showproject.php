<?php
include '../../../controller/ProjectController.php';

$projectC = new ProjectController();
$id = isset($_GET['id']) ? (int) $_GET['id'] : null;

if (!$id) {
    header('Location: projectlist.php');
    exit;
}

$project = $projectC->showProject($id);

if ($project) {
    $project['plateformes'] = json_decode($project['plateformes'] ?? '[]', true) ?? [];
    $project['tags'] = json_decode($project['tags'] ?? '[]', true) ?? [];
    $project['screenshots'] = json_decode($project['screenshots'] ?? '[]', true) ?? [];
} else {
    $project = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameHub | Détails projet</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Orbitron:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/css/lineicons.css" />
    <link rel="stylesheet" href="styles.css" />
    <style>
        .project-hero {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 30px;
            padding: 36px;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            background: linear-gradient(120deg, rgba(255,0,199,0.18), rgba(0,255,234,0.12));
            box-shadow: 0 25px 60px rgba(0,0,0,0.45);
        }
        .cover-frame {
            border-radius: var(--radius);
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.08);
            box-shadow: 0 20px 40px rgba(0,0,0,0.35);
            min-height: 260px;
            background: rgba(5,0,20,0.6);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .cover-frame img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-top: 28px;
        }
        .meta-tile {
            padding: 16px 20px;
            border-radius: var(--radius-sm);
            border: 1px solid rgba(255, 0, 199, 0.25);
            background: rgba(6,0,25,0.75);
        }
        .meta-tile span {
            display: block;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: var(--text-light);
        }
        .meta-tile strong {
            font-size: 1.2rem;
            color: #fff;
        }
        .chip {
            display: inline-flex;
            align-items: center;
            padding: 6px 14px;
            border-radius: 999px;
            border: 1px solid rgba(0,255,234,0.4);
            color: var(--cyan);
            margin: 4px;
            font-size: 0.85rem;
        }
        .detail-card {
            margin-top: 36px;
            padding: 32px;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            background: rgba(5,0,20,0.9);
            box-shadow: var(--shadow);
        }
        .detail-card h3 {
            margin-bottom: 16px;
            font-family: Orbitron, sans-serif;
        }
        .screens-grid {
            margin-top: 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
        }
        .screens-grid img {
            width: 100%;
            height: 140px;
            object-fit: cover;
            border-radius: var(--radius-sm);
            border: 1px solid rgba(255,255,255,0.1);
        }
        .actions-bar {
            margin-top: 30px;
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
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

        <?php if (!$project): ?>
            <section class="admin-section">
                <h2>Projet introuvable</h2>
                <p class="section-subtitle">Aucune fiche ne correspond à l'identifiant demandé.</p>
                <a href="projectlist.php" class="btn-validate text-decoration-none">Retour à la liste</a>
            </section>
        <?php else: ?>

        <section class="project-hero">
            <div class="cover-frame">
                <?php if (!empty($project['image'])): ?>
                    <img src="<?= htmlspecialchars($project['image']); ?>" alt="<?= htmlspecialchars($project['nom']); ?>">
                <?php else: ?>
                    <span class="text-light">Pas d'image</span>
                <?php endif; ?>
            </div>
            <div>
                <h1 style="font-family: Orbitron, sans-serif; font-size: 2.4rem;"><?= htmlspecialchars($project['nom']); ?></h1>
                <p class="section-subtitle mb-3">
                    <?= htmlspecialchars($project['developpeur']); ?> • <?= htmlspecialchars($project['categorie']); ?>
                </p>
                <div class="meta-grid">
                    <div class="meta-tile">
                        <span>Date de création</span>
                        <strong><?= htmlspecialchars($project['date_creation']); ?></strong>
                    </div>
                    <div class="meta-tile">
                        <span>Âge recommandé</span>
                        <strong><?= $project['age_recommande'] ?? '--'; ?>+</strong>
                    </div>
                    <div class="meta-tile">
                        <span>Lieu</span>
                        <strong><?= $project['lieu'] ?? '--'; ?></strong>
                    </div>
                    <div class="meta-tile">
                        <span>Développeur ID</span>
                        <strong>#<?= $project['developpeur_id']; ?></strong>
                    </div>
                </div>
            </div>
        </section>

        <section class="detail-card">
            <h3>Description</h3>
            <p><?= nl2br(htmlspecialchars($project['description'] ?? '')); ?></p>

            <?php if (!empty($project['plateformes'])): ?>
                <h3 class="mt-4">Plateformes</h3>
                <?php foreach ($project['plateformes'] as $platform): ?>
                    <span class="chip"><?= htmlspecialchars($platform); ?></span>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (!empty($project['tags'])): ?>
                <h3 class="mt-4">Tags</h3>
                <?php foreach ($project['tags'] as $tag): ?>
                    <span class="chip"><?= htmlspecialchars($tag); ?></span>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (!empty($project['screenshots'])): ?>
                <h3 class="mt-4">Screenshots</h3>
                <div class="screens-grid">
                    <?php foreach ($project['screenshots'] as $screen): ?>
                        <img src="<?= htmlspecialchars($screen); ?>" alt="Screenshot">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="actions-bar">
                <?php if (!empty($project['lien_telechargement'])): ?>
                    <a href="<?= htmlspecialchars($project['lien_telechargement']); ?>" target="_blank" class="btn-outline">
                        <i class="lni lni-download"></i> Lien téléchargement
                    </a>
                <?php endif; ?>
                <?php if (!empty($project['trailer'])): ?>
                    <a href="<?= htmlspecialchars($project['trailer']); ?>" target="_blank" class="btn-outline">
                        <i class="lni lni-play"></i> Voir trailer
                    </a>
                <?php endif; ?>
                <form action="updateproject.php" method="POST">
                    <input type="hidden" name="id" value="<?= $project['id']; ?>">
                    <button type="submit" class="btn-outline"><i class="lni lni-pencil"></i> Modifier</button>
                </form>
                <a href="projectlist.php" class="btn-ghost">Retour liste</a>
            </div>
        </section>

        <?php endif; ?>
    </div>
</main>

<footer class="admin-footer">
    GameHub Admin &bull; Propulsé par la team XR Labs
</footer>

<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>

