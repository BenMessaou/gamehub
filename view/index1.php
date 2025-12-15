<?php
// public/index.php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Feedback Games - Accueil</title>

  <!-- Meta tags Open Graph pour partage Facebook du site -->
  <?php
  $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
  $currentPath = $_SERVER['PHP_SELF'];
  $basePath = str_replace('/views', '', dirname($currentPath));
  $siteUrl = $baseUrl . $basePath . '/views/index1.php';
  $imageUrl = $baseUrl . $basePath . '/public/assets/logo.png';
  ?>
  <meta property="og:type" content="website" />
  <meta property="og:title" content="🎮 Feedback Games - Réactions des Streamers" />
  <meta property="og:description" content="Découvre les réactions et avis des streamers avant de jouer. Partage ton expérience et lis les commentaires des autres joueurs !" />
  <meta property="og:image" content="<?= $imageUrl ?>" />
  <meta property="og:url" content="<?= $siteUrl ?>" />
  <meta property="og:site_name" content="Feedback Games" />
  <meta property="og:locale" content="fr_FR" />

  <!-- CHEMIN CORRECT vers le CSS -->
  <link rel="stylesheet" href="public/assets/style.css">

</head>
<body>
  <header>
    <div class="container">
        <h1 class="logo">gamehub</h1>
        <img src="logo.png" class="logo1" alt="">
        <nav>
            <ul>
                <li><a href="index.php" class="super-button">Projects</a></li>
                <li><a href="#deals" class="super-button">Events
                </a></li>
                <li><a href="../shop.php" class="super-button">Shop </a></li>
                <li><a href="../article/list.php" class="super-button">Article</a></li><li><a class="super-button" href="../index1.php">feedback</a></li>
                <li><a class="super-button" href="profile.php">Profile</a></li>
                <li><a href="avis.php" class="super-button">Avis </a></li>
            </ul>
        </nav>
    </div>
</header>

  <main>
    <section class="hero">
      <div class="container">
        <h2>Réactions des Streamers</h2>
        <p>Découvre les réactions et avis avant de jouer — regarde les streams et réactions.</p>

        <div class="search-bar" style="margin-top:1.5rem;">
          <input id="game-search" type="text" placeholder="Rechercher un jeu..." />
          <button onclick="rechercherJeu()">🔍 Rechercher</button>
        </div>
      </div>
    </section>

    <section class="deals">
      <div class="container">
        <h3>🎙️ Réactions des Streamers</h3>

        <div id="deal-cards-container" class="deal-cards" style="margin-top:1.5rem;">
          <!-- Les cartes seront générées dynamiquement par JavaScript -->
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
    // Données des jeux disponibles
    const games = [
      {
        name: 'Fortnite',
        image: 'public/assets/fortnite.jpg',
        rating: 4.5,
        ratingStars: '⭐⭐⭐⭐☆',
        streamer: 'TwitchStreamer1',
        comment: '"C\'est fou ! Je n\'ai jamais vu ça !"',
        link: 'https://www.twitch.tv/',
        platform: 'Twitch'
      },
      {
        name: 'Minecraft',
        image: 'public/assets/minecraft.jpg',
        rating: 5,
        ratingStars: '⭐⭐⭐⭐⭐',
        streamer: 'BlockMaster',
        comment: '"Un chef-d\'œuvre intemporel."',
        link: 'https://kick.com/',
        platform: 'Kick'
      },
      {
        name: 'Call of Duty',
        image: 'public/assets/cod.jpg',
        rating: 4,
        ratingStars: '⭐⭐⭐⭐',
        streamer: 'WarriorGamer',
        comment: '"Le gameplay est intense dès la première minute !"',
        link: 'https://www.youtube.com/',
        platform: 'YouTube'
      },
      {
        name: 'Valorant',
        image: 'public/assets/valorant.jpg',
        rating: 4.5,
        ratingStars: '⭐⭐⭐⭐☆',
        streamer: 'ProShooter',
        comment: '"Un FPS tactique exceptionnel avec des mécaniques uniques !"',
        link: 'https://www.twitch.tv/',
        platform: 'Twitch'
      },
      {
        name: 'League of Legends',
        image: 'public/assets/lol.jpg',
        rating: 4.8,
        ratingStars: '⭐⭐⭐⭐⭐',
        streamer: 'LoLMaster',
        comment: '"Le MOBA de référence, toujours aussi addictif après toutes ces années !"',
        link: 'https://www.twitch.tv/',
        platform: 'Twitch'
      },
      {
        name: 'Apex Legends',
        image: '/public/assets/apex.jpg',
        rating: 4.3,
        ratingStars: '⭐⭐⭐⭐☆',
        streamer: 'ApexPro',
        comment: '"Le meilleur battle royale avec des personnages uniques et un gameplay fluide !"',
        link: 'https://www.twitch.tv/',
        platform: 'Twitch'
      },
      {
        name: 'GTA V',
        image: 'public/assets/gta5.jpg',
        rating: 5,
        ratingStars: '⭐⭐⭐⭐⭐',
        streamer: 'GTAStreamer',
        comment: '"Un monde ouvert incroyable, toujours d\'actualité même après des années !"',
        link: 'https://www.youtube.com/',
        platform: 'YouTube'
      },
      {
        name: 'Among Us',
        image: 'public/assets/amongus.jpg',
        rating: 4.2,
        ratingStars: '⭐⭐⭐⭐☆',
        streamer: 'ImpostorKing',
        comment: '"Un jeu de déduction social super amusant à jouer entre amis !"',
        link: 'https://www.twitch.tv/',
        platform: 'Twitch'
      },
      {
        name: 'Rocket League',
        image: 'public/assets/rocketleague.jpg',
        rating: 4.6,
        ratingStars: '⭐⭐⭐⭐⭐',
        streamer: 'RocketPro',
        comment: '"Football + Voitures = Génialité pure ! Un concept unique et addictif !"',
        link: 'https://www.twitch.tv/',
        platform: 'Twitch'
      },
      {
        name: 'Counter-Strike 2',
        image: 'public/assets/cs2.jpg',
        rating: 4.7,
        ratingStars: '⭐⭐⭐⭐⭐',
        streamer: 'CS2Elite',
        comment: '"Le FPS compétitif par excellence, toujours au top du genre !"',
        link: 'https://www.twitch.tv/',
        platform: 'Twitch'
      },
      {
        name: 'FIFA 24',
        image: 'public/assets/fifa24.jpg',
        rating: 4.4,
        ratingStars: '⭐⭐⭐⭐☆',
        streamer: 'FifaMaster',
        comment: '"Le meilleur simulateur de football, avec des graphismes impressionnants !"',
        link: 'https://www.youtube.com/',
        platform: 'YouTube'
      }
    ];

    // Fonction pour créer une carte de jeu
    function createGameCard(game) {
      return `
        <div class="card">
          <img src="${game.image}" alt="${game.name} Reaction"
               onerror="this.onerror=null;this.src='https://placehold.co/600x338?text=${encodeURIComponent(game.name)}+Reaction'">
          <h4>${game.name}</h4>
          <p class="rating">${game.ratingStars} Note : ${game.rating}/5</p>
          <p class="streamer">Par : <em>${game.streamer}</em></p>
          <div class="comments">
            <h5>Avis du streamer :</h5>
            <p>${game.comment} - <strong>@${game.streamer}</strong></p>
          </div>
          <a class="watch-link" href="${game.link}" target="_blank" rel="noopener">👉 Regarder la réaction sur ${game.platform}</a>
        </div>
      `;
    }

    // Fonction pour créer une carte "jeu non disponible"
    function createUnavailableCard(gameName) {
      return `
        <div class="card" style="opacity: 0.8;">
          <div style="background: #1a1f3a; padding: 40px; text-align: center; border-radius: 8px; min-height: 300px; display: flex; flex-direction: column; justify-content: center; align-items: center;">
            <h4 style="color: #ff9800; font-size: 2em; margin-bottom: 20px;">⚠️</h4>
            <h4 style="color: #e0e0e0; margin-bottom: 15px;">${gameName}</h4>
            <p style="color: #999; font-size: 1.1em;">Ce jeu n'est pas disponible à ce moment.</p>
          </div>
        </div>
      `;
    }

    // Fonction pour afficher tous les jeux
    function displayAllGames() {
      const container = document.getElementById('deal-cards-container');
      container.innerHTML = games.map(game => createGameCard(game)).join('');
    }

    // Fonction de recherche
    function rechercherJeu() {
      const q = document.getElementById('game-search').value.trim().toLowerCase();
      const container = document.getElementById('deal-cards-container');
      
      if (!q) {
        // Si la recherche est vide, afficher tous les jeux
        displayAllGames();
        return;
      }

      // Rechercher le jeu (insensible à la casse)
      const foundGame = games.find(game => 
        game.name.toLowerCase().includes(q) || 
        q.includes(game.name.toLowerCase())
      );

      if (foundGame) {
        // Afficher la carte du jeu trouvé
        container.innerHTML = createGameCard(foundGame);
      } else {
        // Afficher la carte "jeu non disponible"
        container.innerHTML = createUnavailableCard(q.charAt(0).toUpperCase() + q.slice(1));
      }
    }

    // Afficher tous les jeux au chargement de la page
    displayAllGames();

    // Écouter la touche Entrée
    document.getElementById('game-search').addEventListener('keypress', function(e){
      if (e.key === 'Enter') { 
        e.preventDefault(); 
        rechercherJeu(); 
      }
    });

    // Rechercher aussi quand on tape (optionnel - recherche en temps réel)
    document.getElementById('game-search').addEventListener('input', function(){
      rechercherJeu();
    });
    
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
