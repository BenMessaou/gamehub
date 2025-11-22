<?php
include_once __DIR__ . '/../../controller/ProjectController.php';

$projectC = new ProjectController();
$id = isset($_GET['id']) ? (int) $_GET['id'] : null;

if (!$id) {
    header('Location: index.php#new-games');
    exit;
}

$project = $projectC->showProject($id);

if (!$project) {
    header('Location: index.php#new-games');
    exit;
}

// Check if project is published (only published projects are accessible from front office)
if (isset($project['statut']) && $project['statut'] !== 'publie') {
    header('Location: index.php#new-games');
    exit;
}

$project['plateformes'] = json_decode($project['plateformes'] ?? '[]', true) ?? [];
$project['tags'] = json_decode($project['tags'] ?? '[]', true) ?? [];
$project['screenshots'] = json_decode($project['screenshots'] ?? '[]', true) ?? [];
$project['image'] = $project['image'] ?: 'assests/game1.png';

$age = isset($project['age_recommande']) && $project['age_recommande'] !== '' ? $project['age_recommande'] . '+' : '--';
$location = $project['lieu'] ?? 'Location not specified';
$category = $project['categorie'] ?? 'Unknown category';
$download = $project['lien_telechargement'] ?? '#';
$trailer = $project['trailer'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($project['nom']); ?> - GameHub Pro</title>
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700;900&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="c.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.0-2/css/all.min.css">
</head>
<body>

  <!-- HEADER -->
  <header>
    <div class="logo1">
        <img src="assests/l1ogo.png" alt="logo de site">
    </div>
    <div class="logo">GameHub Pro</div>
    <nav>
      <ul>
        <li><a href="index.php#home">Home</a></li>
        <li><a href="index.php#new-games">Recent Games</a></li>
        <li><a href="index.php#about">About</a></li>
        <li><a href="index.php#team">Team</a></li>
        <li><a href="#contact">Contact</a></li>
        <li><a href="addgame.html" class="add-game-btn"><span>Add your game</span></a></li>
      </ul>
    </nav>
    <div class="burger-container">
      <div class="burger"></div>
    </div>
  </header>

  <!-- DÉTAIL DU JEU -->
  <section class="game-detail-section">
    <div class="detail-container">
      <a href="index.php#new-games" class="back-btn">Back to Games</a>

      <h1 class="game-name"><?= htmlspecialchars($project['nom']); ?></h1>

      <?php if (!empty($trailer)): ?>
        <div class="trailer-full">
          <iframe src="<?= htmlspecialchars($trailer); ?>" frameborder="0" allowfullscreen></iframe>
        </div>
      <?php else: ?>
        <div class="trailer-full">
          <img src="<?= htmlspecialchars($project['image']); ?>" alt="<?= htmlspecialchars($project['nom']); ?>">
        </div>
      <?php endif; ?>

      <div class="detail-info">
        <p class="game-description">
          <?= nl2br(htmlspecialchars($project['description'] ?? 'No description provided.')); ?>
        </p>

        <div class="game-stats">
          <p><strong>Publication Date:</strong> <span><?= htmlspecialchars($project['date_creation'] ?? '--'); ?></span></p>
          <p><strong>Recommended Age:</strong> <span><?= htmlspecialchars($age); ?></span></p>
          <p><strong>Development Location:</strong> <span><?= htmlspecialchars($location); ?></span></p>
          <p><strong>Category:</strong> <span><?= htmlspecialchars($category); ?></span></p>
          <p><strong>Platforms:</strong>
            <span>
              <?= count($project['plateformes']) ? htmlspecialchars(implode(', ', $project['plateformes'])) : 'Not specified'; ?>
            </span>
          </p>
          <p><strong>Tags:</strong>
            <span>
              <?= count($project['tags']) ? htmlspecialchars(implode(', ', $project['tags'])) : 'None'; ?>
            </span>
          </p>
        </div>

        <div class="developer-detail">
          <h3>Developer</h3>
          <p><strong>Studio:</strong> <span><?= htmlspecialchars($project['developpeur']); ?></span></p>
          <p><strong>Internal ID:</strong> <span>#<?= htmlspecialchars($project['developpeur_id']); ?></span></p>
        </div>

        <?php if (!empty($project['screenshots'])): ?>
          <div class="screens-grid" style="margin-top:20px;">
            <?php foreach ($project['screenshots'] as $screen): ?>
              <img src="<?= htmlspecialchars($screen); ?>" alt="Screenshot">
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <div class="detail-actions">
          <?php if (!empty($project['lien_telechargement'])): ?>
            <a href="<?= htmlspecialchars($download); ?>" class="download-btn" target="_blank">
              Download / Play
            </a>
          <?php endif; ?>
          <a href="index.php#new-games" class="wishlist-btn">
            Back to Games
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer class="footer">
    <div class="footer-content">
      <div class="footer-section about">
        <h3>About</h3>
        <p>GameHub Pro connects developers and gamers for a more creative and collaborative gaming future.</p>
      </div>
      <div class="footer-section links">
        <h3>Quick Links</h3>
        <ul>
        <li><a href="index.php#home">Home</a></li>
        <li><a href="index.php#new-games">Recent Games</a></li>
        <li><a href="index.php#about">About</a></li>
        </ul>
      </div>
      <div class="footer-section social">
        <h3>Follow Us</h3>
        <div class="social-icons">
          <a href="#"><i class="fab fa-facebook-f"></i></a>
          <a href="#"><i class="fab fa-twitter"></i></a>
          <a href="#"><i class="fab fa-instagram"></i></a>
          <a href="#"><i class="fab fa-discord"></i></a>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      <p>© 2025 GameHub Pro | All rights reserved | Tunis, Tunisia</p>
    </div>
  </footer>
<script src="c.js"></script>

</body>
</html>