<?php
// avis.php
require_once __DIR__ . '/../model/config.php';


// Si redirection après contact
$contactStatus = isset($_GET['contact']) ? $_GET['contact'] : null;
$contactMsg = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';

// Récupère les avis depuis la BDD (les plus récents d'abord)
$sql = "SELECT * FROM feedback ORDER BY created_at DESC";
$stmt = $conn->query($sql);
$feedbacks = $stmt ? $stmt->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Feedback Games - Avis</title>
  
  <!-- Meta tags Open Graph pour partage Facebook -->
  <?php
  $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
  $currentPath = $_SERVER['PHP_SELF'];
  $basePath = str_replace('/views', '', dirname($currentPath));
  $pageUrl = $baseUrl . $currentPath;
  $imageUrl = $baseUrl . $basePath . '/public/assets/logo.png';
  ?>
  <meta property="og:type" content="website" />
  <meta property="og:title" content="Feedback Games - Donne ton avis sur tes jeux préférés" />
  <meta property="og:description" content="Partage ton expérience, lis les commentaires des autres joueurs, ou contacte-nous sur Feedback Games !" />
  <meta property="og:image" content="<?= $imageUrl ?>" />
  <meta property="og:url" content="<?= $pageUrl ?>" />
  <meta property="og:site_name" content="Feedback Games" />
  
  <link rel="stylesheet" href="public/assets/style.css">
  <style>
    /* Styles pour les formulaires professionnels */
    .form-container {
      background: linear-gradient(135deg, rgba(26, 31, 58, 0.95) 0%, rgba(10, 14, 39, 0.95) 100%);
      border: 2px solid #00ff88;
      border-radius: 15px;
      padding: 40px;
      margin: 30px 0;
      box-shadow: 0 8px 32px rgba(0, 255, 136, 0.2);
      backdrop-filter: blur(10px);
    }

    .form-container h3 {
      color: #00ff88;
      font-size: 1.8em;
      margin-bottom: 25px;
      text-align: center;
      text-shadow: 0 0 10px rgba(0, 255, 136, 0.5);
      border-bottom: 2px solid #00ff88;
      padding-bottom: 15px;
    }

    .form-container p {
      color: #e0e0e0;
      margin-bottom: 20px;
      text-align: center;
      font-size: 1.05em;
    }

    .form-group {
      margin-bottom: 25px;
    }

    .form-container label {
      display: block;
      color: #00ff88;
      font-weight: bold;
      margin-bottom: 8px;
      font-size: 1em;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .form-container input[type="text"],
    .form-container input[type="email"],
    .form-container textarea {
      width: 100%;
      padding: 15px;
      background: rgba(10, 14, 39, 0.8);
      border: 2px solid #00ff88;
      border-radius: 8px;
      color: #e0e0e0;
      font-size: 1em;
      transition: all 0.3s ease;
      box-sizing: border-box;
    }

    .form-container input[type="text"]:focus,
    .form-container input[type="email"]:focus,
    .form-container textarea:focus {
      outline: none;
      border-color: #00ccff;
      box-shadow: 0 0 20px rgba(0, 204, 255, 0.4);
      background: rgba(10, 14, 39, 0.95);
    }

    .form-container input[type="text"]::placeholder,
    .form-container input[type="email"]::placeholder,
    .form-container textarea::placeholder {
      color: #666;
    }

    .form-container textarea {
      resize: vertical;
      min-height: 120px;
      font-family: inherit;
    }

    /* Amélioration du système d'étoiles */
    .star-rating {
      display: flex;
      flex-direction: row-reverse;
      justify-content: flex-end;
      gap: 10px;
      margin: 15px 0;
    }

    .star-rating input[type="radio"] {
      display: none;
    }

    .star-rating label {
      font-size: 2.5em;
      color: #333;
      cursor: pointer;
      transition: all 0.2s;
      text-transform: none;
      letter-spacing: 0;
      margin: 0;
      padding: 0;
    }

    .star-rating label:hover,
    .star-rating label:hover ~ label {
      color: #FFD700;
      transform: scale(1.1);
    }

    .star-rating input[type="radio"]:checked ~ label {
      color: #FFD700;
    }

    /* Boutons améliorés */
    .form-container .shop-now-btn,
    .form-container button[type="submit"] {
      width: 100%;
      padding: 15px 30px;
      background: linear-gradient(135deg, #00ff88 0%, #00ccff 100%);
      color: #000;
      border: none;
      border-radius: 8px;
      font-size: 1.1em;
      font-weight: bold;
      cursor: pointer;
      transition: all 0.3s ease;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-top: 10px;
    }

    .form-container .shop-now-btn:hover,
    .form-container button[type="submit"]:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(0, 255, 136, 0.4);
      background: linear-gradient(135deg, #00ccff 0%, #00ff88 100%);
    }

    /* Messages de statut améliorés */
    .alert {
      padding: 15px 20px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-weight: bold;
      text-align: center;
      animation: slideIn 0.3s ease;
    }

    .alert-success {
      background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
      color: #fff;
      border: 2px solid #4caf50;
    }

    .alert-error {
      background: linear-gradient(135deg, #f44336 0%, #d32f2f 100%);
      color: #fff;
      border: 2px solid #f44336;
    }

    @keyframes slideIn {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Alertes personnalisées */
    .custom-alert {
      position: fixed;
      top: 100px;
      right: 20px;
      background: linear-gradient(135deg, rgba(26, 31, 58, 0.98) 0%, rgba(10, 14, 39, 0.98) 100%);
      border: 2px solid #ff9800;
      border-radius: 12px;
      padding: 20px 25px;
      min-width: 350px;
      max-width: 500px;
      box-shadow: 0 8px 32px rgba(255, 152, 0, 0.4);
      z-index: 10000;
      display: flex;
      align-items: center;
      gap: 15px;
      animation: slideInRight 0.3s ease;
      backdrop-filter: blur(10px);
    }

    .custom-alert.success {
      border-color: #00ff88;
      box-shadow: 0 8px 32px rgba(0, 255, 136, 0.4);
    }

    .custom-alert.error {
      border-color: #ff4757;
      box-shadow: 0 8px 32px rgba(255, 71, 87, 0.4);
    }

    .custom-alert-icon {
      font-size: 2em;
      flex-shrink: 0;
    }

    .custom-alert-content {
      flex: 1;
    }

    .custom-alert-title {
      color: #00ff88;
      font-weight: bold;
      font-size: 1.1em;
      margin-bottom: 5px;
    }

    .custom-alert.error .custom-alert-title {
      color: #ff4757;
    }

    .custom-alert.success .custom-alert-title {
      color: #00ff88;
    }

    .custom-alert-message {
      color: #e0e0e0;
      font-size: 0.95em;
      line-height: 1.5;
    }

    .custom-alert-close {
      background: transparent;
      border: none;
      color: #999;
      font-size: 1.5em;
      cursor: pointer;
      padding: 0;
      width: 30px;
      height: 30px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      transition: all 0.3s;
      flex-shrink: 0;
    }

    .custom-alert-close:hover {
      background: rgba(255, 255, 255, 0.1);
      color: #fff;
      transform: rotate(90deg);
    }

    @keyframes slideInRight {
      from {
        opacity: 0;
        transform: translateX(100%);
      }
      to {
        opacity: 1;
        transform: translateX(0);
      }
    }

    @keyframes slideOutRight {
      from {
        opacity: 1;
        transform: translateX(0);
      }
      to {
        opacity: 0;
        transform: translateX(100%);
      }
    }

    .custom-alert.hiding {
      animation: slideOutRight 0.3s ease forwards;
    }

    /* Layout en deux colonnes sur grand écran */
    .forms-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 30px;
      margin-top: 30px;
    }

    @media (min-width: 968px) {
      .forms-grid {
        grid-template-columns: 1fr 1fr;
      }
    }

    /* Section feedback améliorée */
    .feedback-section {
      margin-bottom: 40px;
    }

    .contact-section {
      margin-top: 40px;
    }
  </style>

</head>
<body>
  <!-- Header -->
  <header>
    <div class="container header-inner">
      <div class="logo">
        <img src="public/assets/logo.png" alt="Logo Feedback Games" class="logo-img">
        <span>🎮 Feedback Games</span>
      </div>
      <nav>
        <ul>
          <li><a href="index.php" class="super-button">Accueil <span class="arrow"></span></a></li>
          <li><a href="avis.php" class="super-button active">Avis <span class="arrow"></span></a></li>
          <li><a href="frontoffice/profile.php" class="super-button">profile <span class="arrow"></span></a></li>
        </ul>
      </nav>
    </div>
  </header>

  <!-- Hero -->
  <section class="hero">
    <div class="container">
      <h2>Donne ton avis sur tes jeux préférés</h2>
      <p>Partage ton expérience, lis les commentaires des autres joueurs, ou contacte-nous.</p>
    </div>
  </section>

  <!-- Feedback & Contact -->
  <section class="deals">
    <div class="container">
      <div class="forms-grid">
        <!-- Formulaire Avis -->
        <div class="form-container">
          <h3>💬 Donne ton Avis</h3>
          <p>Partage ton expérience de jeu avec la communauté</p>

          <form id="feedback-form" action="add.php" method="POST" novalidate>
            <div class="form-group">
              <label for="pseudo">Pseudo</label>
              <input type="text" id="pseudo" name="pseudo" placeholder="Ton pseudo de joueur">
            </div>

            <div class="form-group">
              <label for="email">Email</label>
              <input type="email" id="email" name="email" placeholder="ton.email@exemple.com">
            </div>

            <div class="form-group">
              <label for="game">Nom du jeu</label>
              <input type="text" id="game" name="game" placeholder="Ex: Fortnite, Minecraft...">
            </div>

            <div class="form-group">
              <label>Note</label>
              <div class="star-rating">
                <input type="radio" id="star5" name="rating" value="5" />
                <label for="star5" title="5 étoiles">★</label>
                <input type="radio" id="star4" name="rating" value="4" />
                <label for="star4" title="4 étoiles">★</label>
                <input type="radio" id="star3" name="rating" value="3" />
                <label for="star3" title="3 étoiles">★</label>
                <input type="radio" id="star2" name="rating" value="2" />
                <label for="star2" title="2 étoiles">★</label>
                <input type="radio" id="star1" name="rating" value="1" />
                <label for="star1" title="1 étoile">★</label>
              </div>
            </div>

            <div class="form-group">
              <label for="message">Ton avis</label>
              <textarea id="message" name="message" rows="5" placeholder="Décris ton expérience avec ce jeu..."></textarea>
            </div>

            <button type="submit" class="shop-now-btn">📤 Envoyer mon Avis</button>
          </form>
        </div>

        <!-- Formulaire Contact -->
        <div class="form-container">
          <h3>📝 Contact</h3>
          <p>Tu peux nous écrire pour proposer des jeux, signaler un bug ou donner ton avis général</p>

          <?php if ($contactStatus === 'ok'): ?>
            <div class="alert alert-success">
              ✅ Merci — ton message a bien été envoyé.
            </div>
          <?php elseif ($contactStatus === 'err'): ?>
            <div class="alert alert-error">
              ❌ Erreur lors de l'envoi : <?= $contactMsg ?>
            </div>
          <?php endif; ?>

          <form id="contact-form" action="contact.php" method="POST" novalidate>
            <div class="form-group">
              <label for="name">Nom</label>
              <input type="text" id="name" name="name" placeholder="Ton nom complet">
            </div>

            <div class="form-group">
              <label for="email-contact">Email</label>
              <input type="email" id="email-contact" name="email" placeholder="ton.email@exemple.com">
            </div>

            <div class="form-group">
              <label for="message-contact">Message</label>
              <textarea id="message-contact" name="message-contact" rows="5" placeholder="Écris ton message ici..."></textarea>
            </div>

            <button type="submit" class="shop-now-btn">📧 Envoyer Message</button>
          </form>
        </div>
      </div>

      <!-- Section Avis récents -->
      <div class="feedback-section" style="margin-top: 50px;">
        <h3 style="color: #00ff88; font-size: 1.8em; margin-bottom: 25px; text-align: center; border-bottom: 2px solid #00ff88; padding-bottom: 15px;">⭐ Avis récents</h3>

        <div id="feedback-list">
          <?php if (count($feedbacks) === 0): ?>
            <p style="color:#ccc">Aucun avis pour le moment.</p>
          <?php else: ?>
            <?php foreach($feedbacks as $f): 
              // Préparer le texte formaté pour le partage
              $shareText = htmlspecialchars($f['pseudo']) . " 🎮 a donné " . (int)$f['rating'] . "/5 ⭐ pour " . htmlspecialchars($f['game']) . "\n\n";
              $shareText .= "Note : " . str_repeat('★', (int)$f['rating']) . str_repeat('☆', 5 - (int)$f['rating']) . " (" . (int)$f['rating'] . "/5)\n\n";
              $shareText .= htmlspecialchars($f['message']) . "\n\n";
              $shareText .= "👉 Voir plus sur Feedback Games";
              $shareTextEncoded = htmlspecialchars(json_encode($shareText));
            ?>
              <div class="feedback-item">
                <h4><?= htmlspecialchars($f['pseudo']) ?> 🎮 (<?= htmlspecialchars($f['game']) ?>)</h4>
                <p><strong>Note : </strong>
                  <?= str_repeat('★', (int)$f['rating']) . str_repeat('☆', 5 - (int)$f['rating']) ?> (<?= (int)$f['rating'] ?>/5)
                </p>
                <p><?= nl2br(htmlspecialchars($f['message'])) ?></p>
                <small style="color:#999">le <?= htmlspecialchars($f['created_at']) ?></small>
                <p style="margin-top: 15px;">
                  <a href="javascript:void(0)" 
                     onclick="shareOnFacebook(<?= (int)$f['id'] ?>, <?= $shareTextEncoded ?>)" 
                     class="btn-share-fb"
                     style="display: inline-block; background: #1877F2; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-weight: bold; margin-right: 10px; transition: all 0.3s;">
                    📘 Partager sur Facebook
                  </a>
                  <a href="delete.php?id=<?= (int)$f['id'] ?>" onclick="return confirm('Supprimer cet avis ?')" style="color: #ff6b6b;">Supprimer</a>
                </p>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer>
    <div class="container">
      <p>© 2025 Feedback Games - Créé par Mohamed Amine Nasri</p>
      <p style="margin-top: 15px;">
        <a href="javascript:void(0)" onclick="shareSiteOnFacebook()" style="
          display: inline-block;
          background: #1877F2;
          color: white;
          padding: 10px 20px;
          border-radius: 6px;
          text-decoration: none;
          font-weight: bold;
          transition: all 0.3s;
        " onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 15px rgba(24,119,242,0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
          📘 Partager Feedback Games sur Facebook
        </a>
      </p>
    </div>
  </footer>

 <script src="/feeeed_backkkkkkkkk/public/assets/validation.js"></script>
 <script src="/feeeed_backkkkkkkkk/public/assets/avis_dynamic.js"></script>
 <script src="/feeeed_backkkkkkkkk/public/assets/facebook_share.js"></script>
 <script>
   // Fonction pour partager le site sur Facebook
   function shareSiteOnFacebook() {
     const baseUrl = window.location.origin;
     const currentPath = window.location.pathname;
     
     // Extraire le chemin de base
     let basePath = '';
     if (currentPath.includes('/views/')) {
       basePath = currentPath.substring(0, currentPath.indexOf('/views/'));
     } else if (currentPath.includes('/feeeed_backkkkkkkkk')) {
       const projectIndex = currentPath.indexOf('/feeeed_backkkkkkkkk');
       basePath = currentPath.substring(0, projectIndex + '/feeeed_backkkkkkkkk'.length);
     } else {
       basePath = currentPath.substring(0, currentPath.lastIndexOf('/'));
     }
     
     const siteUrl = baseUrl + basePath + '/views/index.php';
     const facebookShareUrl = 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(siteUrl);
     
     const width = 700;
     const height = 600;
     const left = (screen.width - width) / 2;
     const top = (screen.height - height) / 2;
     
     window.open(
       facebookShareUrl,
       'Partager Feedback Games sur Facebook',
       `width=${width},height=${height},left=${left},top=${top},scrollbars=yes,resizable=yes`
     );
   }
 </script>
 <style>
   .btn-share-fb:hover {
     background: #166FE5 !important;
     transform: translateY(-2px);
     box-shadow: 0 4px 15px rgba(24,119,242,0.4);
   }
   #fb-share-modal textarea:focus {
     outline: 2px solid #00ccff;
   }
 </style>
</body>
</html>
