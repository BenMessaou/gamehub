<?php
// edit_feedback.php
require_once __DIR__ . '/../model/config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: admin.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // récupération du POST et validation simple
    $pseudo  = trim($_POST['pseudo'] ?? '');
    $game    = trim($_POST['game'] ?? '');
    $rating  = (int)($_POST['rating'] ?? 0);
    $message = trim($_POST['message'] ?? '');

    if ($pseudo === '' || $game === '' || $rating < 1 || $rating > 5 || $message === '') {
        $error = "Tous les champs sont requis et la note doit être entre 1 et 5.";
    } else {
        $sql = "UPDATE feedback SET pseudo = ?, game = ?, rating = ?, message = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssisi", $pseudo, $game, $rating, $message, $id);
        $stmt->execute();
        $stmt->close();
        header('Location: admin.php');
        exit;
    }
}

// lecture de l'avis pour préremplir le formulaire
$sql = "SELECT * FROM feedback WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$feedback = $res->fetch_assoc();
$stmt->close();

if (!$feedback) {
    header('Location: admin.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Modifier l'avis #<?= $feedback['id'] ?> - Feedback Games</title>
  <link rel="stylesheet" href="/feeeed_backkkkkkkkk/public/assets/style.css">
</head>
<body>
  <header>
    <div class="container header-inner">
      <div class="logo">
        <img src="/feeeed_backkkkkkkkk/public/assets/logo.png" alt="Logo Feedback Games" class="logo-img">
        <span>🎮 Feedback Games</span>
      </div>

      <nav>
        <ul>
          <li><a href="index.php" class="super-button">Accueil <span class="arrow">➡️</span></a></li>
          <li><a href="avis.php" class="super-button">Avis <span class="arrow">➡️</span></a></li>
          <li><a href="admin.php" class="super-button active">Dashboard <span class="arrow">➡️</span></a></li>
        </ul>
      </nav>
    </div>
  </header>

  <main>
    <section class="hero">
      <div class="container">
        <h2>Modifier l'avis #<?= $feedback['id'] ?></h2>
        <p>Modifie les informations de cet avis.</p>
      </div>
    </section>

    <section class="deals">
      <div class="container">
        <div class="feedback-section">
          <?php if (!empty($error)): ?>
            <div style="background: #f44336; color: #fff; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
              <?= htmlspecialchars($error) ?>
            </div>
          <?php endif; ?>

          <form method="post" style="max-width: 600px; margin: 0 auto;">
            <label for="pseudo">Pseudo :</label>
            <input type="text" id="pseudo" name="pseudo" required value="<?= htmlspecialchars($feedback['pseudo']) ?>">

            <label for="game">Nom du jeu :</label>
            <input type="text" id="game" name="game" required value="<?= htmlspecialchars($feedback['game']) ?>">

            <label>Note :</label>
            <div class="star-rating">
              <?php for ($i = 5; $i >= 1; $i--): 
                $checked = ((int)$feedback['rating'] == $i) ? 'checked' : '';
              ?>
                <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" <?= $checked ?> required />
                <label for="star<?= $i ?>" title="<?= $i ?> étoile<?= $i > 1 ? 's' : '' ?>">★</label>
              <?php endfor; ?>
            </div>

            <label for="message">Avis :</label>
            <textarea id="message" name="message" rows="4" required><?= htmlspecialchars($feedback['message']) ?></textarea>

            <div style="display: flex; gap: 15px; margin-top: 20px;">
              <button type="submit" class="super-button" style="text-decoration: none; display: inline-block; text-align: center; padding: 12px 24px; border: none; cursor: pointer;">Sauvegarder</button>
              <a href="admin.php" class="super-button" style="text-decoration: none; display: inline-block; text-align: center; padding: 12px 24px;">Annuler</a>
            </div>
          </form>
        </div>
      </div>
    </section>
  </main>

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
</body>
</html>
