<?php
/**
 * Page de partage Facebook pour un avis
 * Affiche un avis avec les meta tags Open Graph pour le partage Facebook
 */
require_once __DIR__ . '/../models/config.php';

// Récupérer l'ID de l'avis depuis l'URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: avis.php');
    exit;
}

// Récupérer l'avis depuis la base de données
$sql = "SELECT * FROM feedback WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$feedback = $res->fetch_assoc();
$stmt->close();

if (!$feedback) {
    header('Location: avis.php');
    exit;
}

// Configuration de l'URL de base (pour XAMPP dans htdocs)
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
$scriptPath = dirname($_SERVER['PHP_SELF']); // Ex: /feeeed_backkkkkkkkk/views

// Extraire le chemin de base du projet (ex: /feeeed_backkkkkkkkk)
$basePath = str_replace('/views', '', $scriptPath);

// Construire l'URL de partage
$shareUrl = $baseUrl . $scriptPath . '/share.php?id=' . $id;
// URL du site (pour le lien retour)
$siteUrl = $baseUrl . $basePath . '/views/avis.php';
// URL de l'image (logo)
$imageUrl = $baseUrl . $basePath . '/public/assets/logo.png';

// Préparer les données pour le partage
$title = htmlspecialchars($feedback['pseudo']) . " a donné " . $feedback['rating'] . "/5 ⭐ pour " . htmlspecialchars($feedback['game']);
$description = htmlspecialchars(mb_substr($feedback['message'], 0, 200)) . (mb_strlen($feedback['message']) > 200 ? '...' : '');

// Générer une image dynamique avec la note (optionnel - on utilise le logo pour l'instant)
$ratingStars = str_repeat('★', (int)$feedback['rating']) . str_repeat('☆', 5 - (int)$feedback['rating']);
?>
<!DOCTYPE html>
<html lang="fr" prefix="og: http://ogp.me/ns#">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $title ?> - Feedback Games</title>
  
  <!-- Meta tags Open Graph pour Facebook -->
  <meta property="og:type" content="article" />
  <meta property="og:title" content="<?= $title ?>" />
  <meta property="og:description" content="<?= $description ?>" />
  <meta property="og:image" content="<?= $imageUrl ?>" />
  <meta property="og:image:width" content="1200" />
  <meta property="og:image:height" content="630" />
  <meta property="og:url" content="<?= $shareUrl ?>" />
  <meta property="og:site_name" content="Feedback Games" />
  <meta property="og:locale" content="fr_FR" />
  
  <!-- Meta tags Twitter (bonus) -->
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="<?= $title ?>" />
  <meta name="twitter:description" content="<?= $description ?>" />
  <meta name="twitter:image" content="<?= $imageUrl ?>" />
  
  <link rel="stylesheet" href="/feeeed_backkkkkkkkk/public/assets/style.css">
  <style>
    .share-container {
      max-width: 800px;
      margin: 100px auto;
      padding: 40px;
      background: linear-gradient(135deg, #1a1f3a 0%, #0a0e27 100%);
      border-radius: 15px;
      box-shadow: 0 8px 32px rgba(0,255,136,0.2);
      border: 2px solid #00ff88;
    }
    .share-header {
      text-align: center;
      margin-bottom: 30px;
    }
    .share-header h1 {
      color: #00ff88;
      font-size: 2em;
      margin-bottom: 10px;
    }
    .share-feedback {
      background: rgba(0,255,136,0.1);
      padding: 30px;
      border-radius: 10px;
      margin: 20px 0;
      border-left: 4px solid #00ff88;
    }
    .share-feedback h2 {
      color: #00ff88;
      margin-bottom: 15px;
    }
    .share-rating {
      font-size: 1.5em;
      color: #FFD700;
      margin: 15px 0;
    }
    .share-message {
      color: #e0e0e0;
      line-height: 1.8;
      font-size: 1.1em;
      margin: 20px 0;
    }
    .share-meta {
      color: #999;
      font-size: 0.9em;
      margin-top: 20px;
      padding-top: 20px;
      border-top: 1px solid #2a2f4a;
    }
    .share-actions {
      text-align: center;
      margin-top: 30px;
    }
    .btn-share-facebook {
      display: inline-block;
      background: #1877F2;
      color: white;
      padding: 15px 30px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: bold;
      font-size: 1.1em;
      margin: 10px;
      transition: all 0.3s;
    }
    .btn-share-facebook:hover {
      background: #166FE5;
      transform: translateY(-2px);
      box-shadow: 0 4px 15px rgba(24,119,242,0.4);
    }
    .btn-back {
      display: inline-block;
      background: #00ff88;
      color: #000;
      padding: 15px 30px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: bold;
      font-size: 1.1em;
      margin: 10px;
      transition: all 0.3s;
    }
    .btn-back:hover {
      background: #00ccff;
      transform: translateY(-2px);
    }
  </style>
</head>
<body>
  <div class="share-container">
    <div class="share-header">
      <h1>🎮 Avis Partagé - Feedback Games</h1>
      <p style="color: #999;">Cet avis a été partagé depuis Feedback Games</p>
    </div>
    
    <div class="share-feedback">
      <h2><?= htmlspecialchars($feedback['pseudo']) ?> 🎮</h2>
      <p style="color: #00ccff; font-size: 1.2em; margin: 10px 0;">
        Jeu : <strong><?= htmlspecialchars($feedback['game']) ?></strong>
      </p>
      
      <div class="share-rating">
        Note : <?= $ratingStars ?> (<?= (int)$feedback['rating'] ?>/5)
      </div>
      
      <div class="share-message">
        <?= nl2br(htmlspecialchars($feedback['message'])) ?>
      </div>
      
      <div class="share-meta">
        <p>Publié le <?= htmlspecialchars($feedback['created_at']) ?></p>
        <p>Partagé depuis <a href="<?= $siteUrl ?>/views/avis.php" style="color: #00ff88;">Feedback Games</a></p>
      </div>
    </div>
    
    <div class="share-actions">
      <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($shareUrl) ?>" 
         target="_blank" 
         class="btn-share-facebook">
        📘 Partager sur Facebook
      </a>
      <a href="<?= $siteUrl ?>" class="btn-back">← Retour aux avis</a>
    </div>
  </div>
  
  <script>
    // Redirection automatique vers Facebook si paramètre fb=1
    if (window.location.search.includes('fb=1')) {
      const shareUrl = encodeURIComponent(window.location.href);
      window.location.href = 'https://www.facebook.com/sharer/sharer.php?u=' + shareUrl;
    }
  </script>
</body>
</html>

