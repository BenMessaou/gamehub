<?php
require_once __DIR__ . '/../../controller/EventController.php';

$eventC = new EventController();
$event = null;

// Récupération de l'ID
$eventId = $_GET['id'] ?? null;

if ($eventId) {
    $event = $eventC->showEvent($eventId);
}

if (!$event || $event['status'] !== 'active') {
    header('Location: eventsp.php');
    exit;
}

$startDate = new DateTime($event['start_date']);
$endDate = new DateTime($event['end_date']);
$remaining = $event['capacity'] - $event['reserved_count'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($event['title']) ?> – Détails</title>
  
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #0f0f17 0%, #1b1b29 100%);
      color: #fff;
      min-height: 100vh;
    }
    
    header {
      background: rgba(27, 27, 41, 0.9);
      padding: 20px 40px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    }
    
    header nav {
      display: flex;
      gap: 20px;
      margin-top: 15px;
    }
    
    header nav a {
      color: #fff;
      text-decoration: none;
      padding: 8px 16px;
      border-radius: 20px;
      transition: all 0.3s;
      border: 1px solid rgba(124, 0, 255, 0.3);
    }
    
    header nav a:hover {
      background: linear-gradient(120deg, #ff00c7, #7c00ff);
      transform: translateY(-2px);
    }
    
    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 40px 20px;
    }
    
    .event-details {
      background: rgba(27, 27, 41, 0.8);
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 8px 40px rgba(0,0,0,0.4);
      border: 1px solid rgba(124, 0, 255, 0.2);
    }
    
    .event-banner {
      width: 100%;
      max-height: 500px;
      object-fit: cover;
      background: linear-gradient(120deg, #ff00c7, #7c00ff);
    }
    
    .event-content {
      padding: 40px;
    }
    
    .event-title {
      font-size: 2.5rem;
      margin-bottom: 20px;
      background: linear-gradient(120deg, #ff00c7, #7c00ff, #00ffea);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
    
    .event-info {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin: 30px 0;
    }
    
    .info-item {
      padding: 20px;
      background: rgba(15, 15, 23, 0.6);
      border-radius: 10px;
      border: 1px solid rgba(124, 0, 255, 0.2);
    }
    
    .info-item strong {
      display: block;
      color: #7c00ff;
      margin-bottom: 8px;
      font-size: 0.9rem;
      text-transform: uppercase;
    }
    
    .info-item p {
      color: #fff;
      font-size: 1.1rem;
    }
    
    .event-description {
      margin: 30px 0;
      padding: 30px;
      background: rgba(15, 15, 23, 0.6);
      border-radius: 10px;
      border-left: 4px solid #7c00ff;
      line-height: 1.8;
      color: #ccc;
    }
    
    .action-buttons {
      display: flex;
      gap: 15px;
      margin-top: 30px;
      flex-wrap: wrap;
    }
    
    .btn {
      padding: 15px 30px;
      border-radius: 25px;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s;
      border: none;
      cursor: pointer;
      font-size: 1rem;
    }
    
    .btn-primary {
      background: linear-gradient(120deg, #ff00c7, #7c00ff);
      color: #fff;
    }
    
    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 15px rgba(124, 0, 255, 0.5);
    }
    
    .btn-secondary {
      background: transparent;
      color: #fff;
      border: 2px solid #7c00ff;
    }
    
    .btn-secondary:hover {
      background: rgba(124, 0, 255, 0.2);
    }
    
    .badge {
      display: inline-block;
      padding: 6px 16px;
      border-radius: 20px;
      font-size: 0.9rem;
      margin-left: 10px;
      font-weight: 600;
    }
    
    .badge-online {
      background: linear-gradient(120deg, #00ffea, #7c00ff);
      color: #fff;
    }
    
    .badge-offline {
      background: #6c757d;
      color: #fff;
    }
    
    .badge-status {
      background: linear-gradient(120deg, #ff00c7, #7c00ff);
      color: #fff;
    }
    
    .capacity-info {
      padding: 20px;
      background: rgba(124, 0, 255, 0.1);
      border-radius: 10px;
      margin: 20px 0;
      border: 1px solid rgba(124, 0, 255, 0.3);
    }
    
    .capacity-bar {
      width: 100%;
      height: 30px;
      background: rgba(15, 15, 23, 0.6);
      border-radius: 15px;
      overflow: hidden;
      margin-top: 10px;
    }
    
    .capacity-fill {
      height: 100%;
      background: linear-gradient(120deg, #ff00c7, #7c00ff);
      transition: width 0.3s;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-weight: 600;
      font-size: 0.9rem;
    }
  </style>
</head>
<body>
<header>
  <h1>🎮 Détails de l'Événement</h1>
  <nav>
    <a href="eventsp.php">← Retour aux événements</a>
    <a href="index.php">Accueil</a>
  </nav>
</header>

<div class="container">
  <div class="event-details">
    <?php if (!empty($event['banner'])): ?>
      <img src="<?= htmlspecialchars($event['banner']) ?>" alt="<?= htmlspecialchars($event['title']) ?>" class="event-banner" />
    <?php else: ?>
      <div style="width: 100%; height: 400px; background: linear-gradient(120deg, #ff00c7, #7c00ff); display: flex; align-items: center; justify-content: center; font-size: 5rem;">
        🎮
      </div>
    <?php endif; ?>
    
    <div class="event-content">
      <h1 class="event-title">
        <?= htmlspecialchars($event['title']) ?>
        <?php if ($event['is_online']): ?>
          <span class="badge badge-online">🌐 En ligne</span>
        <?php else: ?>
          <span class="badge badge-offline">📍 Présentiel</span>
        <?php endif; ?>
        <span class="badge badge-status"><?= strtoupper($event['status']) ?></span>
      </h1>
      
      <div class="event-info">
        <div class="info-item">
          <strong>📍 Lieu / Lien</strong>
          <p>
            <?php if ($event['is_online']): ?>
              <a href="<?= htmlspecialchars($event['location']) ?>" target="_blank" style="color: #00ffea;">
                <?= htmlspecialchars($event['location']) ?>
              </a>
            <?php else: ?>
              <?= htmlspecialchars($event['location']) ?>
            <?php endif; ?>
          </p>
        </div>
        
        <div class="info-item">
          <strong>📅 Date de début</strong>
          <p><?= $startDate->format('d/m/Y à H:i') ?></p>
        </div>
        
        <div class="info-item">
          <strong>📅 Date de fin</strong>
          <p><?= $endDate->format('d/m/Y à H:i') ?></p>
        </div>
        
        <div class="info-item">
          <strong>⏱️ Durée</strong>
          <p><?= $startDate->diff($endDate)->format('%h heures %i minutes') ?></p>
        </div>
      </div>
      
      <?php if (!empty($event['description'])): ?>
        <div class="event-description">
          <h3 style="margin-bottom: 15px; color: #7c00ff;">Description</h3>
          <p><?= nl2br(htmlspecialchars($event['description'])) ?></p>
        </div>
      <?php endif; ?>
      
      <div class="capacity-info">
        <strong style="display: block; margin-bottom: 10px; color: #7c00ff;">🎫 Capacité</strong>
        <p>
          <strong><?= $event['reserved_count'] ?> / <?= $event['capacity'] ?></strong> places réservées
          <?php if ($remaining > 0): ?>
            <span style="color: #00ffea;">(<?= $remaining ?> places disponibles)</span>
          <?php else: ?>
            <span style="color: #ff00c7;">(Complet)</span>
          <?php endif; ?>
        </p>
        <div class="capacity-bar">
          <div class="capacity-fill" style="width: <?= ($event['reserved_count'] / $event['capacity']) * 100 ?>%">
            <?= round(($event['reserved_count'] / $event['capacity']) * 100) ?>%
          </div>
        </div>
      </div>
      
      <div class="action-buttons">
        <?php if ($remaining > 0): ?>
          <button class="btn btn-primary">Réserver une Place</button>
        <?php else: ?>
          <button class="btn btn-primary" disabled style="opacity: 0.5; cursor: not-allowed;">Complet</button>
        <?php endif; ?>
        <button class="btn btn-secondary">Activer les Rappels</button>
        <a href="eventsp.php" class="btn btn-secondary">Retour à la liste</a>
      </div>
    </div>
  </div>
</div>

</body>
</html>

