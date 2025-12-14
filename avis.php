<?php
// avis.php
require 'config.php';

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
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <!-- Header -->
  <header>
    <div class="container">
      <div class="logo">🎮 Feedback Games</div>
      <nav>
        <ul>
          <li><a href="index.php" class="super-button">Accueil <span class="arrow">➡️</span></a></li>
          <li><a href="avis.php" class="super-button active">Avis <span class="arrow">➡️</span></a></li>
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
      <div class="feedback-section">
        <h3>💬 Donne ton Avis</h3>

        <!-- FORM soumet à add.php -->
        <form id="feedback-form" action="add.php" method="POST">
          <label for="pseudo">Pseudo :</label>
          <input type="text" id="pseudo" name="pseudo" required>

          <label for="game">Nom du jeu :</label>
          <input type="text" id="game" name="game" required>

          <label>Note :</label>
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

          <label for="message">Ton avis :</label>
          <textarea id="message" name="message" rows="4" required></textarea>

          <button type="submit" class="shop-now-btn">Envoyer Avis</button>
        </form>

        <h3 style="margin-top: 2rem; color: #00ff88;">Avis récents :</h3>

        <div id="feedback-list">
          <?php if (count($feedbacks) === 0): ?>
            <p style="color:#ccc">Aucun avis pour le moment.</p>
          <?php else: ?>
            <?php foreach($feedbacks as $f): ?>
              <div class="feedback-item">
                <h4><?= htmlspecialchars($f['pseudo']) ?> 🎮 (<?= htmlspecialchars($f['game']) ?>)</h4>
                <p><strong>Note : </strong>
                  <?= str_repeat('★', (int)$f['rating']) . str_repeat('☆', 5 - (int)$f['rating']) ?> (<?= (int)$f['rating'] ?>/5)
                </p>
                <p><?= nl2br(htmlspecialchars($f['message'])) ?></p>
                <small style="color:#999">le <?= htmlspecialchars($f['created_at']) ?></small>
                <p>
                  <a href="delete.php?id=<?= (int)$f['id'] ?>" onclick="return confirm('Supprimer cet avis ?')">Supprimer</a>
                </p>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>

      <!-- Contact -->
      <div class="contact-section">
        <h3>📝 Contact</h3>
        <?php if ($contactStatus === 'ok'): ?>
          <div style="background:#062; color:#dff; padding:10px; border-radius:6px; margin-bottom:10px;">
            Merci — ton message a bien été envoyé.
          </div>
        <?php elseif ($contactStatus === 'err'): ?>
          <div style="background:#600; color:#ffd; padding:10px; border-radius:6px; margin-bottom:10px;">
            Erreur lors de l'envoi : <?= $contactMsg ?>
          </div>
        <?php endif; ?>

        <p>Tu peux nous écrire pour proposer des jeux, signaler un bug ou donner ton avis général.</p>
        <form id="contact-form" action="contact.php" method="POST">
          <label for="name">Nom :</label>
          <input type="text" id="name" name="name" required>

          <label for="email">Email :</label>
          <input type="email" id="email" name="email" required>

          <label for="message-contact">Message :</label>
          <textarea id="message-contact" name="message-contact" rows="4" required></textarea>

          <button type="submit" class="shop-now-btn">Envoyer Message</button>
        </form>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer>
    <div class="container">
      <p>© 2025 Feedback Games - Créé par Mohamed Amine Nasri</p>
    </div>
  </footer>

  <script src="assets/main.js"></script>
</body>
</html>
