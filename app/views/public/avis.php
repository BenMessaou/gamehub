<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Feedback Games - Avis</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="/feedback-games/public/assets/style.css">
</head>
<body>
  <header>
    <div class="container">
      <div class="logo">🎮 Feedback Games</div>
      <nav>
        <ul>
          <li><a href="/feedback-games/public/index.php?url=home" class="super-button">Accueil</a></li>
          <li><a href="/feedback-games/public/index.php?url=avis" class="super-button active">Avis</a></li>
        </ul>
      </nav>
    </div>
  </header>

  <section class="hero">
    <div class="container">
      <h2>Donne ton avis</h2>
      <p>Partage ton expérience sur un jeu.</p>
    </div>
  </section>

  <section class="deals">
    <div class="container">
      <h3>💬 Laisser un avis</h3>
      <form action="/feedback-games/public/index.php?url=avis/add" method="POST">
        <label>Pseudo</label>
        <input type="text" name="pseudo" required>

        <label>Nom du jeu</label>
        <input type="text" name="game" required>

        <label>Note</label>
        <select name="rating" required>
          <option value="5">5</option>
          <option value="4">4</option>
          <option value="3">3</option>
          <option value="2">2</option>
          <option value="1">1</option>
        </select>

        <label>Message</label>
        <textarea name="message" rows="4" required></textarea>

        <button type="submit" class="shop-now-btn">Envoyer</button>
      </form>

      <h3 style="margin-top:2rem">Avis récents</h3>

      <?php if (empty($feedbacks)): ?>
        <p>Aucun avis pour le moment.</p>
      <?php else: ?>
        <?php foreach ($feedbacks as $f): ?>
          <div class="feedback-item">
            <h4><?= htmlspecialchars($f['pseudo']) ?> — <?= htmlspecialchars($f['game']) ?></h4>
            <p><strong>Note :</strong> <?= (int)$f['rating'] ?>/5</p>
            <p><?= nl2br(htmlspecialchars($f['message'])) ?></p>
            <small>Posté le <?= htmlspecialchars($f['created_at']) ?></small>
            <p><a href="/feedback-games/public/index.php?url=avis/delete/<?= (int)$f['id'] ?>" onclick="return confirm('Supprimer ?')">Supprimer</a></p>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>

    </div>
  </section>
</body>
</html>
