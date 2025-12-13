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
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($project['nom']); ?> - GameHub Pro</title>
  <link rel="stylesheet" href="collaborations.css">
  <link rel="stylesheet" href="c.css">
  <!-- Chatbot IA -->
  <link rel="stylesheet" href="../backoffice/collabcrud/chatbot.css">
</head>
<body>
  <header>
    <div class="container">
      <div style="display: flex; align-items: center; gap: 10px;">
        <img src="assests/logo.png" alt="Logo GameHub Pro" class="header-logo">
        <h1 class="logo">GameHub Pro</h1>
      </div>
      <nav>
        <ul>
          <li><a href="index.php" class="super-button">Home</a></li>
          <li><a href="index.php#new-games" class="super-button">Recent Games</a></li>
          <li><a href="collaborations.php" class="super-button">Collaborations</a></li>
          <li><a href="index.php#about" class="super-button">About</a></li>
        </ul>
      </nav>
      
      <button id="sidebar-toggle" class="sidebar-toggle">☰</button>
    </div>
  </header>

  <aside id="sidebar" class="sidebar">
    <nav>
      <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="index.php#new-games">Recent Games</a></li>
        <li><a href="collaborations.php"> Collaborations</a></li>
        <li><a href="index.php#about">About</a></li>
      </ul>
    </nav>
  </aside>

  <main id="main-content" class="main-content">
    <!-- Images décoratives flottantes en arrière-plan -->
    <div class="decorative-images">
      <img src="assests/logo.png" alt="Decor" class="decor-img decor-img-1">
      <img src="assests/game5.png" alt="Decor" class="decor-img decor-img-2">
      <img src="assests/logo.png" alt="Decor" class="decor-img decor-img-3">
    </div>

    <div class="animated-strip">
      <div class="strip-content">
        <img src="assests/nim.jpg" />
        <img src="assests/rambling.jpg" />
        <img src="assests/house.jpg" />
        <img src="assests/planet.jpg"  />
        <img src="assests/girl.jpg"  />

        <img src="assests/nim.jpg" />
        <img src="assests/rambling.jpg" />
        <img src="assests/3.png" />
        <img src="assests/planet.jpg"  />
        <img src="assests/1.png"  />
        <img src="assests/nim.jpg" />
        <img src="assests/rambling.jpg" />
        <img src="assests/house.jpg" />
        <img src="assests/planet.jpg"  />
        <img src="assests/girl.jpg"  />
        <img src="assests/nim.jpg" />
        <img src="assests/rambling.jpg" />
        <img src="assests/house.jpg" />
        <img src="assests/planet.jpg"  />
        <img src="assests/girl.jpg"  />
        <img src="assests/nim.jpg" />
        <img src="assests/1.png" />
        <img src="assests/house.jpg" />
        <img src="assests/5.png"  />
        <img src="assests/girl.jpg"  />
      </div>
    </div>

    <!-- DÉTAIL DU JEU -->
    <div class="collabs-container">
      <div class="collabs-header">
        <h1><?= htmlspecialchars($project['nom']); ?></h1>
        <a href="index.php#new-games" class="super-button">
          ← Back to Games
        </a>
      </div>

      <div class="collab-card" style="max-width: 1200px; margin: 0 auto;">
        <?php if (!empty($trailer)): ?>
          <div class="card-image-wrapper" style="height: 500px; margin-bottom: 2rem;">
            <iframe src="<?= htmlspecialchars($trailer); ?>" frameborder="0" allowfullscreen style="width: 100%; height: 100%; border-radius: 12px;"></iframe>
          </div>
        <?php else: ?>
          <div class="card-image-wrapper" style="height: 500px; margin-bottom: 2rem;">
            <img src="<?= htmlspecialchars($project['image']); ?>" alt="<?= htmlspecialchars($project['nom']); ?>" class="collab-image">
          </div>
        <?php endif; ?>

        <div class="detail-info" style="background: transparent; border: none; box-shadow: none; padding: 0;">
          <p class="description" style="font-size: 1.1rem; line-height: 1.8; margin-bottom: 2rem;">
            <?= nl2br(htmlspecialchars($project['description'] ?? 'No description provided.')); ?>
          </p>

          <div class="collab-info" style="margin-bottom: 2rem; flex-wrap: wrap;">
            <span class="statut ouvert">
              <?= htmlspecialchars($category); ?>
            </span>
            <span class="members-info">
              🎮 <?= htmlspecialchars($age); ?> • <?= htmlspecialchars($location); ?>
            </span>
          </div>

          <div class="game-stats" style="background: rgba(0, 0, 0, 0.4); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 1px solid rgba(0, 255, 136, 0.2);">
            <p style="color: #00ff88; margin-bottom: 0.8rem;"><strong style="color: #00ffea;">Publication Date:</strong> <span style="color: #ccc;"><?= htmlspecialchars($project['date_creation'] ?? '--'); ?></span></p>
            <p style="color: #00ff88; margin-bottom: 0.8rem;"><strong style="color: #00ffea;">Recommended Age:</strong> <span style="color: #ccc;"><?= htmlspecialchars($age); ?></span></p>
            <p style="color: #00ff88; margin-bottom: 0.8rem;"><strong style="color: #00ffea;">Development Location:</strong> <span style="color: #ccc;"><?= htmlspecialchars($location); ?></span></p>
            <p style="color: #00ff88; margin-bottom: 0.8rem;"><strong style="color: #00ffea;">Category:</strong> <span style="color: #ccc;"><?= htmlspecialchars($category); ?></span></p>
            <p style="color: #00ff88; margin-bottom: 0.8rem;"><strong style="color: #00ffea;">Platforms:</strong>
              <span style="color: #ccc;">
                <?= count($project['plateformes']) ? htmlspecialchars(implode(', ', $project['plateformes'])) : 'Not specified'; ?>
              </span>
            </p>
            <p style="color: #00ff88; margin-bottom: 0.8rem;"><strong style="color: #00ffea;">Tags:</strong>
              <span style="color: #ccc;">
                <?= count($project['tags']) ? htmlspecialchars(implode(', ', $project['tags'])) : 'None'; ?>
              </span>
            </p>
          </div>

          <div style="background: rgba(0, 0, 0, 0.4); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 1px solid rgba(0, 255, 136, 0.2);">
            <h3 style="color: #00ff88; margin-bottom: 1rem; text-shadow: 0 0 10px rgba(0, 255, 136, 0.5);">Developer</h3>
            <p style="color: #00ff88; margin-bottom: 0.8rem;"><strong style="color: #00ffea;">Studio:</strong> <span style="color: #ccc;"><?= htmlspecialchars($project['developpeur']); ?></span></p>
            <p style="color: #00ff88;"><strong style="color: #00ffea;">Internal ID:</strong> <span style="color: #ccc;">#<?= htmlspecialchars($project['developpeur_id']); ?></span></p>
          </div>

          <?php if (!empty($project['screenshots'])): ?>
            <div style="margin: 2rem 0;">
              <h3 style="color: #00ff88; margin-bottom: 1.5rem; text-shadow: 0 0 10px rgba(0, 255, 136, 0.5);">Screenshots</h3>
              <div class="screens-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1.5rem;">
                <?php foreach ($project['screenshots'] as $screen): ?>
                  <div class="card-image-wrapper" style="height: 200px;">
                    <img src="<?= htmlspecialchars($screen); ?>" alt="Screenshot" class="collab-image">
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>

          <div class="card-actions" style="justify-content: center; margin-top: 2rem;">
            <?php if (!empty($project['lien_telechargement'])): ?>
              <a href="<?= htmlspecialchars($download); ?>" class="btn-view" target="_blank" style="background: rgba(0, 255, 136, 0.2); color: #00ff88; border-color: rgba(0, 255, 136, 0.5);">
                Download / Play
              </a>
            <?php endif; ?>
            <a href="index.php#new-games" class="btn-view">
              Back to Games
            </a>
          </div>
        </div>
      </div>
    </div>
  </main>

  <footer class="footer">
    <div class="footer-content">
      <div class="footer-section about">
        <h3>GameHub Pro</h3>
        <p>The platform that connects independent developers with players from around the world.</p>
      </div>
      <div class="footer-section links">
        <h3>Quick Links</h3>
        <ul>
          <li><a href="index.php">Home</a></li>
          <li><a href="index.php#new-games">Recent Games</a></li>
          <li><a href="#" class="dashboard-link" id="dashboardFooterBtn">Dashboard</a></li>
          <li><a href="collaborations.php"> Collaborations</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <p>&copy; 2025 GameHub Pro | All rights reserved | Tunis, Tunisia</p>
    </div>
  </footer>

  <!-- Chatbot HTML -->
  <?php include '../backoffice/collabcrud/chatbot.html'; ?>
  
  <script src="collaborations.js"></script>
  <script src="../backoffice/collabcrud/chatbot.js"></script>
  <script>
  // Dashboard button management in footer
  document.addEventListener('DOMContentLoaded', function() {
    const dashboardFooterBtn = document.getElementById('dashboardFooterBtn');
    
    if (dashboardFooterBtn) {
      dashboardFooterBtn.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Display alert indicating admin login
        alert('You are logged in as administrator.\n\nTo access the Dashboard, please enter the access code.');
        
        // Request the code
        const code = prompt('Enter the Dashboard access code:');
        
        // Verify the code
        if (code === '0000') {
          // Correct code, redirect to collaboration dashboard
          window.location.href = '../backoffice/projectscrud/projectlist.php';
        } else if (code === null) {
          // User cancelled
          return;
        } else {
          // Incorrect code
          alert('Incorrect code. Access denied.');
        }
      });
    }
  });
  </script>

</body>
</html>