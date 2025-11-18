<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Feedback Games - Accueil</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <!-- Header -->
  <header>
    <div class="container">
      <div class="logo">🎮 Feedback Games</div>
      <nav>
        <ul>
          <li><a href="index.php" class="super-button active">Accueil <span class="arrow">➡️</span></a></li>
          <li><a href="avis.php" class="super-button">Avis <span class="arrow">➡️</span></a></li>
        </ul>
      </nav>
    </div>
  </header>

  <!-- Section Hero -->
  <section class="hero">
    <div class="container">
      <h2>Donne ton avis sur tes jeux préférés</h2>
      <p>Découvre les opinions des joueurs et les réactions des streamers avant de télécharger un jeu.</p>
      
      <div class="search-bar">
        <input type="text" id="game-search" placeholder="Rechercher un jeu...">
        <button type="button">🔍 Rechercher</button>
      </div>
    </div>
  </section>

  <!-- Section Streamers -->
  <section class="deals">
    <div class="container">
      <h3>🎙️ Réactions des Streamers</h3>
      <div class="deal-cards">

        <!-- Fortnite -->
        <div class="card">
          <img src="https://placehold.co/300x169?text=Fortnite+Reaction" alt="Streamer réagissant à Fortnite">
          <h4>Fortnite</h4>
          <p class="rating">⭐⭐⭐⭐☆ Note : 4.5/5</p>
          <p class="streamer">Par : <em>TwitchStreamer1</em></p>
          <div class="comments">
            <h5>Avis du streamer :</h5>
            <p>"C'est fou ! Je n'ai jamais vu ça !" - <strong>@TwitchStreamer1</strong></p>
          </div>
        </div>

        <!-- Minecraft -->
        <div class="card">
          <img src="https://placehold.co/300x169?text=Minecraft+Reaction" alt="Streamer réagissant à Minecraft">
          <h4>Minecraft</h4>
          <p class="rating">⭐⭐⭐⭐⭐ Note : 5/5</p>
          <p class="streamer">Par : <em>BlockMaster</em></p>
          <div class="comments">
            <h5>Avis du streamer :</h5>
            <p>"Un chef-d'œuvre intemporel." - <strong>@BlockMaster</strong></p>
          </div>
        </div>

        <!-- Call of Duty -->
        <div class="card">
          <img src="https://placehold.co/300x169?text=COD+Reaction" alt="Streamer réagissant à Call of Duty">
          <h4>Call of Duty</h4>
          <p class="rating">⭐⭐⭐⭐ Note : 4/5</p>
          <p class="streamer">Par : <em>WarriorGamer</em></p>
          <div class="comments">
            <h5>Avis du streamer :</h5>
            <p>"Le gameplay est intense dès la première minute !" - <strong>@WarriorGamer</strong></p>
          </div>
        </div>

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
