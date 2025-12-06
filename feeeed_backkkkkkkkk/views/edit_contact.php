<?php
// edit_contact.php
require_once __DIR__ . '/../models/config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: admin.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '' || $email === '' || $message === '') {
        $error = "Tous les champs sont requis.";
    } else {
        $sql = "UPDATE contact SET name = ?, email = ?, message = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $name, $email, $message, $id);
        $stmt->execute();
        $stmt->close();
        header('Location: admin.php');
        exit;
    }
}

// récupération du message
$sql = "SELECT * FROM contact WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$contact = $res->fetch_assoc();
$stmt->close();

if (!$contact) {
    header('Location: admin.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Modifier le message #<?= $contact['id'] ?> - Feedback Games</title>
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
        <h2>Modifier le message #<?= $contact['id'] ?></h2>
        <p>Modifie les informations de ce message de contact.</p>
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
            <label for="name">Nom :</label>
            <input type="text" id="name" name="name" required value="<?= htmlspecialchars($contact['name']) ?>">

            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required value="<?= htmlspecialchars($contact['email']) ?>">

            <label for="message">Message :</label>
            <textarea id="message" name="message" rows="4" required><?= htmlspecialchars($contact['message']) ?></textarea>

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
