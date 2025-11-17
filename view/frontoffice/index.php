<?php
include_once __DIR__ . '/../../controller/ProjectController.php';

$projectC = new ProjectController();
$projects = $projectC->listProjects();

if ($projects instanceof PDOStatement) {
    $projects = $projects->fetchAll(PDO::FETCH_ASSOC);
}

if (!is_array($projects)) {
    $projects = [];
}

$featuredProjects = array_slice($projects, 0, 5);
$primaryProjectId = $featuredProjects[0]['id'] ?? null;
$primaryDetailLink = $primaryProjectId ? 'detail.php?id=' . urlencode($primaryProjectId) : '#new-games';
$placeholderImage = 'assests/game1.png';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameHub Pro - Plateforme de jeux indépendants</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700;900&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="c.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.0-2/css/all.min.css">
</head>
<body>

    <!-- HEADER -->
    <header>
        <div class="logo1">
            <img src="assests/logo.png" alt="Logo GameHub Pro">
        </div>
        <div class="logo">GameHub Pro</div>
        <nav>
            <ul>
                <li><a href="#home">Accueil</a></li>
                <li><a href="#new-games">Jeux récents</a></li>
                <li><a href="#about">À propos</a></li>
                <li><a href="#team">Équipe</a></li>
                <li><a href="#contact">Contact</a></li>
                <li><a href="addgame.html" class="add-game-btn"><span>Add your game</span></a></li>
            </ul>
        </nav>
        <div class="burger-container">
            <div class="burger"></div>
        </div>
    </header>

    <!-- HERO SECTION -->
    <section id="home" class="hero-section">
        <div class="hero-content">
            <h1>Bienvenue sur <span>GameHub Pro</span></h1>
            <p>Découvrez, jouez et partagez les meilleurs jeux indépendants créés par des développeurs passionnés du monde entier.</p>
            <a href="<?= $primaryDetailLink; ?>" class="cta-btn">
                <?= $primaryProjectId ? 'Voir le projet mis en avant' : 'Voir les jeux'; ?>
            </a>
        </div>
        <div class="hero-image">
            <img src="assests/logo.png" alt="Jeu en vedette">
        </div>
    </section>

    <!-- NEW GAMES SECTION -->
    <section id="new-games" class="games-section">
        <div class="section-header">
            <h2>Jeux récents</h2>
            <p>Les dernières pépites ajoutées à la plateforme</p>
        </div>
        <div class="games-grid">
            <?php if (count($featuredProjects) === 0): ?>
                <p class="empty-state">Aucun projet n'a encore été publié. Revenez vite&nbsp;!</p>
            <?php else: ?>
                <?php foreach ($featuredProjects as $project): ?>
                    <?php
                        $image = !empty($project['image']) ? $project['image'] : $placeholderImage;
                        $age = isset($project['age_recommande']) && $project['age_recommande'] !== '' ? $project['age_recommande'] . '+' : '--';
                        $location = $project['lieu'] ?? 'Lieu non renseigné';
                        $dateCreation = $project['date_creation'] ?? '--';
                        $category = $project['categorie'] ?? 'Catégorie inconnue';
                    ?>
                    <article class="game-card">
                        <img src="<?= htmlspecialchars($image); ?>" alt="<?= htmlspecialchars($project['nom'] ?? 'Jeu'); ?>">
                        <div class="game-info">
                            <h3><?= htmlspecialchars($project['nom'] ?? 'Projet sans nom'); ?></h3>
                            <p class="category"><?= htmlspecialchars($category); ?></p>
                            <p class="age"><?= htmlspecialchars($age); ?></p>
                            <p class="location"><?= htmlspecialchars($location); ?></p>
                            <p class="date"><?= htmlspecialchars($dateCreation); ?></p>
                        </div>
                        <a href="detail.php?id=<?= urlencode($project['id']); ?>" class="play-btn">Voir</a>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="view-more">
            <a href="../backoffice/projectlist.php" class="secondary-btn">Voir tous les jeux</a>
        </div>
    </section>

    <!-- ABOUT SECTION -->
    <section id="about" class="about-section">
        <div class="about-content">
            <h2>À propos de GameHub Pro</h2>
            <p>GameHub Pro est une plateforme dédiée aux jeux indépendants. Nous croyons que chaque développeur a une histoire unique à raconter à travers ses créations.</p>
            <ul>
                <li>Accès gratuit pour tous les joueurs</li>
                <li>Soumission simplifiée pour les développeurs</li>
                <li>Validation humaine de chaque jeu</li>
                <li>Communauté active et passionnée</li>
            </ul>
            <a href="addgame.html" class="secondary-btn">Soumettre votre jeu</a>
        </div>
        <div class="about-image">
            <img src="images/about-illustration.png" alt="À propos">
        </div>
    </section>

    <!-- TEAM SECTION -->
    <section id="team" class="team-section">
        <h2>Notre équipe</h2>
        <div class="team-grid">
            <div class="team-member">
                <img src="images/team1.jpg" alt="Membre 1">
                <h3>Alexandre Dupont</h3>
                <p>Fondateur & CEO</p>
            </div>
            <div class="team-member">
                <img src="images/team2.jpg" alt="Membre 2">
                <h3>Sophie Martin</h3>
                <p>Responsable Community</p>
            </div>
            <div class="team-member">
                <img src="images/team3.jpg" alt="Membre 3">
                <h3>Karim Ben Salah</h3>
                <p>Développeur Principal</p>
            </div>
            <div class="team-member">
                <img src="images/team4.jpg" alt="Membre 4">
                <h3>Leila Ammar</h3>
                <p>Designer UI/UX</p>
            </div>
        </div>
    </section>

    <!-- CONTACT SECTION -->
    <section id="contact" class="contact-section">
        <h2>Contactez-nous</h2>
        <form class="contact-form">
            <input type="text" placeholder="Votre nom" required>
            <input type="email" placeholder="Votre email" required>
            <textarea placeholder="Votre message" rows="5" required></textarea>
            <button type="submit" class="submit-btn">Envoyer</button>
        </form>
    </section>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section about">
                <h3>GameHub Pro</h3>
                <p>La plateforme qui connecte les développeurs indépendants aux joueurs du monde entier.</p>
            </div>
            <div class="footer-section links">
                <h3>Liens rapides</h3>
                <ul>
                    <li><a href="#home">Accueil</a></li>
                    <li><a href="#new-games">Jeux récents</a></li>
                    <li><a href="#about">À propos</a></li>
                    <li><a href="#team">Équipe</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </div>
            <div class="footer-section social">
                <h3>Suivez-nous</h3>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-discord"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 GameHub Pro | Tous droits réservés | Tunis, Tunisie</p>
        </div>
    </footer>
<script src="c.js"></script>

</body>
</html>

