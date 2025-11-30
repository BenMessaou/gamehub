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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameHub Pro - Independent Games Platform</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700;900&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="c.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.0-2/css/all.min.css">
</head>
<body>

    <!-- HEADER -->
    <header>
        <div class="header-left">
            <div class="logo1">
                <img src="assests/logo.png" alt="Logo GameHub Pro">
            </div>
            <div class="logo">GameHub Pro</div>
        </div>
        <nav class="header-nav">
            <ul>
                <li><a href="#home">Home</a></li>
                <li><a href="#new-games">Recent Games</a></li>
                <li><a href="collaborations.php">🤝 Collaborations</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#team">Team</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
        </nav>
        <div class="header-right">
            <div class="burger-container">
                <div class="burger"></div>
            </div>
        </div>
        <a href="#" class="dashboard-btn" id="dashboardBtn">Dashboard</a>
    </header>

    <!-- HERO SECTION -->
    <section id="home" class="hero-section">
        <div class="hero-content">
            <h1>Welcome to <span>GameHub Pro</span></h1>
            <p>Discover, play and share the best independent games created by passionate developers from around the world.</p>
            <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                <a href="<?= $primaryDetailLink; ?>" class="cta-btn">
                    <?= $primaryProjectId ? 'View Featured Project' : 'View Games'; ?>
                </a>
                <a href="collaborations.php" class="cta-btn" style="background: linear-gradient(120deg, #00ffea, #0099cc);">
                    🤝 Collaborations
                </a>
            </div>
        </div>
        <div class="hero-image">
            <img src="assests/logo.png" alt="Featured Game">
        </div>
    </section>

    <!-- NEW GAMES SECTION -->
    <section id="new-games" class="games-section">
        <div class="section-header">
            <div class="section-header-top">
                <h2>Recent Games</h2>
                <a href="addgame.html" class="add-game-btn"><span>Add your game</span></a>
            </div>
            <p>The latest gems added to the platform</p>
        </div>
        <div class="games-grid">
            <?php if (count($featuredProjects) === 0): ?>
                <p class="empty-state">No projects have been published yet. Come back soon!</p>
            <?php else: ?>
                <?php foreach ($featuredProjects as $project): ?>
                    <?php
                        $image = !empty($project['image']) ? $project['image'] : $placeholderImage;
                        $age = isset($project['age_recommande']) && $project['age_recommande'] !== '' ? $project['age_recommande'] . '+' : '--';
                        $location = $project['lieu'] ?? 'Location not specified';
                        $dateCreation = $project['date_creation'] ?? '--';
                        $category = $project['categorie'] ?? 'Unknown category';
                    ?>
                    <article class="game-card">
                        <img src="<?= htmlspecialchars($image); ?>" alt="<?= htmlspecialchars($project['nom'] ?? 'Jeu'); ?>">
                        <div class="game-info">
                            <h3><?= htmlspecialchars($project['nom'] ?? 'Unnamed Project'); ?></h3>
                            <p class="category"><?= htmlspecialchars($category); ?></p>
                            <p class="age"><?= htmlspecialchars($age); ?></p>
                            <p class="location"><?= htmlspecialchars($location); ?></p>
                            <p class="date"><?= htmlspecialchars($dateCreation); ?></p>
                        </div>
                        <a href="detail.php?id=<?= urlencode($project['id']); ?>" class="play-btn">View</a>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- ABOUT SECTION -->
    <section id="about" class="about-section">
        <div class="about-content">
            <h2>About GameHub Pro</h2>
            <p>GameHub Pro is a platform dedicated to independent games. We believe that every developer has a unique story to tell through their creations.</p>
            <ul>
                <li>Free access for all players</li>
                <li>Simplified submission for developers</li>
                <li>Human validation of each game</li>
                <li>Active and passionate community</li>
            </ul>
            <a href="addgame.html" class="secondary-btn">Submit Your Game</a>
        </div>
        <div class="about-image">
            <img src="images/about-illustration.png" alt="About">
        </div>
    </section>

    <!-- TEAM SECTION -->
    <section id="team" class="team-section">
        <h2>Our Team</h2>
        <div class="team-grid">
            <div class="team-member">
                <img src="images/team1.jpg" alt="Member 1">
                <h3>Alexandre Dupont</h3>
                <p>Founder & CEO</p>
            </div>
            <div class="team-member">
                <img src="images/team2.jpg" alt="Member 2">
                <h3>Sophie Martin</h3>
                <p>Community Manager</p>
            </div>
            <div class="team-member">
                <img src="images/team3.jpg" alt="Member 3">
                <h3>Karim Ben Salah</h3>
                <p>Lead Developer</p>
            </div>
            <div class="team-member">
                <img src="images/team4.jpg" alt="Member 4">
                <h3>Leila Ammar</h3>
                <p>UI/UX Designer</p>
            </div>
        </div>
    </section>

    <!-- CONTACT SECTION -->
    <section id="contact" class="contact-section">
        <h2>Contact Us</h2>
        <form class="contact-form">
            <input type="text" placeholder="Your name" required>
            <input type="email" placeholder="Your email" required>
            <textarea placeholder="Your message" rows="5" required></textarea>
            <button type="submit" class="submit-btn">Send</button>
        </form>
    </section>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section about">
                <h3>GameHub Pro</h3>
                <p>The platform that connects independent developers with players from around the world.</p>
            </div>
            <div class="footer-section links">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="#home">Home</a></li>
                    <li><a href="#new-games">Recent Games</a></li>
                    <li><a href="collaborations.php">🤝 Collaborations</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#team">Team</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </div>
            <div class="footer-section social">
                <h3>Follow Us</h3>
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
            <p>&copy; 2025 GameHub Pro | All rights reserved | Tunis, Tunisia</p>
        </div>
    </footer>
<script src="c.js"></script>
<script>
// Dashboard button management with code verification
document.addEventListener('DOMContentLoaded', function() {
    const dashboardBtn = document.getElementById('dashboardBtn');
    
    if (dashboardBtn) {
        dashboardBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Display alert indicating admin login
            alert('You are logged in as administrator.\n\nTo access the Dashboard, please enter the access code.');
            
            // Request the code
            const code = prompt('Enter the Dashboard access code:');
            
            // Verify the code
            if (code === '0000') {
                // Correct code, redirect to dashboard
                window.location.href = '../backoffice/projectscrud/projectlist.php';
            } else if (code === null) {
                // User cancelled
                return;
            } else {
                // Incorrect code
                alert('Incorrect code. Access denied.');
            }
        });
    }
});
</script>

</body>
</html>

