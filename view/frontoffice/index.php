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
    <title>GameHub </title>
    <link rel="stylesheet" href="collaborations.css">
    <link rel="stylesheet" href="c.css">
    <!-- Chatbot IA -->
    <link rel="stylesheet" href="../backoffice/collabcrud/chatbot.css">
    <link rel="stylesheet" href="assets/css/index.css">

    <style>
    /* Animation Spot Publicitaire */
    .ad-spot-container {
        position: relative;
        width: 100%;
        max-width: 1200px;
        margin: 2rem auto;
        height: 500px;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0, 255, 136, 0.3);
        border: 2px solid rgba(0, 255, 136, 0.5);
        background: rgba(0, 0, 0, 0.9);
        backdrop-filter: blur(10px);
        animation: slideInDown 0.8s ease-out, borderGlow 3s ease-in-out infinite;
    }

    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translateY(-50px) scale(0.9);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
    .sidebar {
  position: fixed;
  top: 80px;
  left: -250px;
  width: 250px;
  height: calc(100vh - 80px);
  background: rgba(0, 0, 0, 0.9);
  backdrop-filter: blur(10px);
  border-right: 1px solid rgba(0, 255, 136, 0.3);
  transition: left 0.3s ease;
  z-index: 90;
}

.sidebar.show {
  left: 0;
}

.sidebar nav ul {
  flex-direction: column;
  padding: 2rem 0;
}

.sidebar nav ul li {
  margin: 0;
  border-bottom: 1px solid rgba(0, 255, 136, 0.1);
}

.sidebar nav ul li a {
  display: block;
  padding: 1rem 2rem;
  color: #fff;
  text-decoration: none;
  transition: background 0.3s ease;
}

.sidebar nav ul li a:hover {
  background: rgba(0, 255, 136, 0.1);
}

/* Sidebar Toggle */
.sidebar-toggle {
  display: none;
  background: none;
  border: none;
  color: #00ff88;
  font-size: 1.5rem;
  cursor: pointer;
  padding: 0.5rem;
}

    @keyframes borderGlow {
        0%, 100% {
            box-shadow: 0 20px 60px rgba(0, 255, 136, 0.3),
                        0 0 40px rgba(0, 255, 136, 0.2),
                        inset 0 0 40px rgba(0, 255, 136, 0.1);
            border-color: rgba(0, 255, 136, 0.5);
        }
        50% {
            box-shadow: 0 20px 60px rgba(0, 255, 234, 0.5),
                        0 0 60px rgba(0, 255, 234, 0.4),
                        inset 0 0 60px rgba(0, 255, 234, 0.2);
            border-color: rgba(0, 255, 234, 0.7);
        }
    }

    .ad-spot-slider {
        position: relative;
        width: 100%;
        height: 100%;
    }

    .ad-spot-slide {
        position: absolute;
        width: 100%;
        height: 100%;
        opacity: 0;
        transform: scale(0.9) rotateY(20deg);
        transition: all 1s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        justify-content: center;
        perspective: 1000px;
    }

    .ad-spot-slide.active {
        opacity: 1;
        transform: scale(1) rotateY(0deg);
        z-index: 2;
        animation: slideIn 1s ease-out, floatAnimation 6s ease-in-out infinite;
    }

    @keyframes slideIn {
        0% {
            opacity: 0;
            transform: scale(0.9) rotateY(20deg) translateX(-50px);
        }
        100% {
            opacity: 1;
            transform: scale(1) rotateY(0deg) translateX(0);
        }
    }

    @keyframes floatAnimation {
        0%, 100% {
            transform: scale(1) rotateY(0deg) translateY(0);
        }
        50% {
            transform: scale(1.02) rotateY(0deg) translateY(-10px);
        }
    }

    .ad-spot-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        width: 100%;
        height: 100%;
        gap: 2rem;
        padding: 2rem;
        align-items: center;
    }

    .ad-spot-image {
        position: relative;
        width: 100%;
        height: 100%;
        border-radius: 15px;
        overflow: hidden;
        border: 2px solid rgba(0, 255, 136, 0.3);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5),
                    0 0 40px rgba(0, 255, 136, 0.2);
        animation: imageBorderPulse 3s ease-in-out infinite;
    }

    @keyframes imageBorderPulse {
        0%, 100% {
            border-color: rgba(0, 255, 136, 0.3);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5),
                        0 0 40px rgba(0, 255, 136, 0.2);
        }
        50% {
            border-color: rgba(0, 255, 234, 0.6);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5),
                        0 0 60px rgba(0, 255, 234, 0.5);
        }
    }

    .ad-spot-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
        filter: brightness(0.9) contrast(1.1);
    }

    .ad-spot-slide.active .ad-spot-image img {
        animation: zoomIn 8s ease-in-out infinite alternate,
                   imageShimmer 4s ease-in-out infinite;
    }

    @keyframes zoomIn {
        0% {
            transform: scale(1) rotate(0deg);
            filter: brightness(0.9) contrast(1.1) hue-rotate(0deg);
        }
        100% {
            transform: scale(1.15) rotate(1deg);
            filter: brightness(1.1) contrast(1.2) hue-rotate(5deg);
        }
    }

    @keyframes imageShimmer {
        0% {
            filter: brightness(0.9) contrast(1.1) hue-rotate(0deg);
        }
        50% {
            filter: brightness(1.2) contrast(1.3) hue-rotate(10deg);
        }
        100% {
            filter: brightness(0.9) contrast(1.1) hue-rotate(0deg);
        }
    }

    .ad-spot-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(0, 255, 136, 0.1) 0%, rgba(255, 0, 199, 0.1) 100%);
        pointer-events: none;
        animation: overlayShift 4s ease-in-out infinite;
    }

    @keyframes overlayShift {
        0%, 100% {
            background: linear-gradient(135deg, rgba(0, 255, 136, 0.1) 0%, rgba(255, 0, 199, 0.1) 100%);
        }
        50% {
            background: linear-gradient(135deg, rgba(0, 255, 234, 0.15) 0%, rgba(255, 0, 150, 0.15) 100%);
        }
    }

    .ad-spot-image::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: conic-gradient(from 0deg, transparent, rgba(0, 255, 136, 0.1), transparent);
        animation: rotateOverlay 8s linear infinite;
        z-index: 1;
        pointer-events: none;
    }

    @keyframes rotateOverlay {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }

    .ad-spot-info {
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 1.5rem;
        color: #fff;
        z-index: 3;
    }

    .ad-spot-title {
        font-size: 3rem;
        font-weight: 900;
        background: linear-gradient(135deg, #00ff88, #00ffea, #ff00ff, #00ff88);
        background-size: 300% 300%;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-shadow: 0 0 30px rgba(0, 255, 136, 0.5);
        margin: 0;
        animation: glowText 2s ease-in-out infinite alternate,
                   gradientShift 4s ease infinite,
                   titleFloat 3s ease-in-out infinite;
        position: relative;
    }

    .ad-spot-title::before {
        content: attr(data-text);
        position: absolute;
        left: 0;
        top: 0;
        z-index: -1;
        background: linear-gradient(135deg, #00ff88, #00ffea, #ff00ff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        filter: blur(10px);
        opacity: 0.5;
        animation: glowPulse 2s ease-in-out infinite;
    }

    @keyframes glowText {
        0% {
            filter: brightness(1) drop-shadow(0 0 10px rgba(0, 255, 136, 0.5));
        }
        100% {
            filter: brightness(1.4) drop-shadow(0 0 30px rgba(0, 255, 234, 0.8));
        }
    }

    @keyframes gradientShift {
        0% {
            background-position: 0% 50%;
        }
        50% {
            background-position: 100% 50%;
        }
        100% {
            background-position: 0% 50%;
        }
    }

    @keyframes titleFloat {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-5px);
        }
    }

    @keyframes glowPulse {
        0%, 100% {
            opacity: 0.3;
            transform: scale(1);
        }
        50% {
            opacity: 0.6;
            transform: scale(1.05);
        }
    }

    .ad-spot-category {
        display: inline-block;
        padding: 8px 20px;
        background: rgba(0, 255, 136, 0.2);
        color: #00ff88;
        border: 1px solid rgba(0, 255, 136, 0.5);
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
        width: fit-content;
        position: relative;
        overflow: hidden;
        animation: categoryPulse 2s ease-in-out infinite;
        transition: all 0.3s ease;
    }

    .ad-spot-category::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(0, 255, 136, 0.3), transparent);
        animation: categoryShine 3s linear infinite;
    }

    @keyframes categoryPulse {
        0%, 100% {
            box-shadow: 0 0 10px rgba(0, 255, 136, 0.3);
            transform: scale(1);
        }
        50% {
            box-shadow: 0 0 20px rgba(0, 255, 136, 0.6);
            transform: scale(1.05);
        }
    }

    @keyframes categoryShine {
        0% {
            transform: translateX(-100%) translateY(-100%) rotate(45deg);
        }
        100% {
            transform: translateX(100%) translateY(100%) rotate(45deg);
        }
    }

    .ad-spot-description {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #ccc;
        margin: 0;
    }

    .ad-spot-meta {
        display: flex;
        gap: 1.5rem;
        flex-wrap: wrap;
    }

    .ad-spot-meta span {
        color: #00ffea;
        font-size: 0.95rem;
        padding: 5px 12px;
        background: rgba(0, 255, 234, 0.1);
        border: 1px solid rgba(0, 255, 234, 0.3);
        border-radius: 15px;
        transition: all 0.3s ease;
        animation: metaFloat 4s ease-in-out infinite;
    }

    .ad-spot-meta span:nth-child(1) {
        animation-delay: 0s;
    }

    .ad-spot-meta span:nth-child(2) {
        animation-delay: 0.5s;
    }

    .ad-spot-meta span:nth-child(3) {
        animation-delay: 1s;
    }

    @keyframes metaFloat {
        0%, 100% {
            transform: translateY(0);
            box-shadow: 0 0 5px rgba(0, 255, 234, 0.2);
        }
        50% {
            transform: translateY(-3px);
            box-shadow: 0 0 15px rgba(0, 255, 234, 0.4);
        }
    }

    .ad-spot-meta span:hover {
        background: rgba(0, 255, 234, 0.2);
        border-color: rgba(0, 255, 234, 0.6);
        transform: scale(1.1);
    }

    .ad-spot-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        padding: 15px 35px;
        background: linear-gradient(145deg, #0f0f0f, #1c1c1c);
        border: 2px solid rgba(255, 255, 255, 0.2);
        border-radius: 100px;
        color: #fff;
        text-decoration: none;
        font-weight: 700;
        font-size: 1.1rem;
        letter-spacing: 0.5px;
        cursor: pointer;
        overflow: hidden;
        transition: all 0.4s ease-in-out;
        box-shadow: 0 0 20px rgba(0, 255, 255, 0.1);
        backdrop-filter: blur(8px);
        position: relative;
        z-index: 1;
        animation: buttonGlow 3s ease-in-out infinite;
    }

    .ad-spot-btn::before {
        content: "";
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: conic-gradient(from 0deg, #00ffff, #ff00ff, #00ffff);
        animation: rotate 4s linear infinite;
        z-index: -2;
    }

    .ad-spot-btn::after {
        content: "";
        position: absolute;
        inset: 2px;
        background: linear-gradient(145deg, #0f0f0f, #1c1c1c);
        border-radius: inherit;
        z-index: -1;
    }

    @keyframes rotate {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    @keyframes buttonGlow {
        0%, 100% {
            box-shadow: 0 0 20px rgba(0, 255, 255, 0.1),
                        0 0 40px rgba(0, 255, 136, 0.1);
        }
        50% {
            box-shadow: 0 0 30px rgba(0, 255, 255, 0.3),
                        0 0 60px rgba(0, 255, 136, 0.3);
        }
    }

    .ad-spot-btn:hover {
        transform: scale(1.05) translateY(-3px);
        box-shadow: 0 0 40px rgba(0, 255, 255, 0.4),
                    0 0 80px rgba(0, 255, 136, 0.4);
        color: #90EE90;
        border-color: rgba(0, 255, 136, 0.8);
    }

    .ad-spot-btn:hover::after {
        background: linear-gradient(145deg, rgba(0, 255, 136, 0.1), rgba(0, 255, 234, 0.1));
    }

    .ad-spot-btn span {
        position: relative;
        z-index: 1;
    }

    .ad-spot-indicators {
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 10px;
        z-index: 10;
    }

    .ad-spot-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        border: 2px solid rgba(0, 255, 136, 0.5);
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
    }

    .ad-spot-dot::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(0, 255, 136, 0.5);
        transition: all 0.3s ease;
    }

    .ad-spot-dot.active {
        background: #00ff88;
        box-shadow: 0 0 15px rgba(0, 255, 136, 0.8),
                    0 0 30px rgba(0, 255, 136, 0.4);
        transform: scale(1.3);
        animation: dotPulse 2s ease-in-out infinite;
    }

    .ad-spot-dot.active::before {
        width: 20px;
        height: 20px;
        opacity: 0;
        animation: dotRipple 2s ease-out infinite;
    }

    @keyframes dotPulse {
        0%, 100% {
            box-shadow: 0 0 15px rgba(0, 255, 136, 0.8),
                        0 0 30px rgba(0, 255, 136, 0.4);
        }
        50% {
            box-shadow: 0 0 25px rgba(0, 255, 136, 1),
                        0 0 50px rgba(0, 255, 136, 0.6);
        }
    }

    @keyframes dotRipple {
        0% {
            width: 0;
            height: 0;
            opacity: 1;
        }
        100% {
            width: 30px;
            height: 30px;
            opacity: 0;
        }
    }

    .ad-spot-dot:hover {
        transform: scale(1.2);
        background: rgba(0, 255, 136, 0.6);
    }

    .ad-spot-close {
        position: absolute;
        top: 20px;
        right: 20px;
        width: 40px;
        height: 40px;
        background: rgba(0, 0, 0, 0.7);
        border: 2px solid rgba(255, 51, 92, 0.5);
        border-radius: 50%;
        color: #ff335c;
        font-size: 1.5rem;
        cursor: pointer;
        z-index: 10;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: closeButtonFloat 3s ease-in-out infinite;
    }

    @keyframes closeButtonFloat {
        0%, 100% {
            transform: translateY(0) rotate(0deg);
        }
        50% {
            transform: translateY(-5px) rotate(5deg);
        }
    }

    .ad-spot-close:hover {
        background: rgba(255, 51, 92, 0.3);
        box-shadow: 0 0 20px rgba(255, 51, 92, 0.6),
                    0 0 40px rgba(255, 51, 92, 0.3);
        transform: rotate(90deg) scale(1.1);
        border-color: rgba(255, 51, 92, 0.8);
    }

    .ad-spot-container.hidden {
        display: none;
    }

    /* Particules animées */
    @keyframes particleFloat {
        0% {
            transform: translateY(0) translateX(0) scale(1);
            opacity: 0;
        }
        10% {
            opacity: 1;
        }
        90% {
            opacity: 1;
        }
        100% {
            transform: translateY(-100vh) translateX(${Math.random() * 200 - 100}px) scale(0);
            opacity: 0;
        }
    }

    .ad-particle {
        z-index: 1;
    }

    /* Effet de particules sur les slides */
    .ad-spot-slide.active::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle at 20% 30%, rgba(0, 255, 136, 0.1) 0%, transparent 50%),
                    radial-gradient(circle at 80% 70%, rgba(0, 255, 234, 0.1) 0%, transparent 50%);
        pointer-events: none;
        animation: backgroundPulse 4s ease-in-out infinite;
    }

    @keyframes backgroundPulse {
        0%, 100% {
            opacity: 0.5;
        }
        50% {
            opacity: 1;
        }
    }

    @media (max-width: 768px) {
        .ad-spot-container {
            height: 400px;
        }

        .ad-spot-content {
            grid-template-columns: 1fr;
            padding: 1rem;
        }

        .ad-spot-image {
            height: 200px;
        }

        .ad-spot-title {
            font-size: 2rem;
        }
    }
    </style>
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
                <li><a href="../article/list.php" class="super-button">Article</a></li><li><a class="super-button" href="index1.php">feedback</a></li>
                <li><a class="super-button" href="profile.php">Profile</a></li>
               
            </ul>
        </nav>
    </div>
</header>

    <main id="main-content" class="main-content">
       
        <div class="decorative-images">
            <img src="assests/logo.png" alt="Decor" class="decor-img decor-img-1">
            <img src="assests/game5.png" alt="Decor" class="decor-img decor-img-2">
            <img src="assests/logo.png" alt="Decor" class="decor-img decor-img-3">
        </div>

        <div class="animated-strip">
            <div class="strip-content">
                <img src="assests/nim.jpg" />
                <img src="assests/rambling.jpg" />
                <img src="assests/house.jpg" />
                <img src="assests/planet.jpg"  />
                <img src="assests/girl.jpg"  />

                <img src="assests/nim.jpg" />
                <img src="assests/rambling.jpg" />
                <img src="assests/3.png" />
                <img src="assests/planet.jpg"  />
                <img src="assests/1.png"  />
                <img src="assests/nim.jpg" />
                <img src="assests/rambling.jpg" />
                <img src="assests/house.jpg" />
                <img src="assests/planet.jpg"  />
                <img src="assests/girl.jpg"  />
                <img src="assests/nim.jpg" />
                <img src="assests/rambling.jpg" />
                <img src="assests/house.jpg" />
                <img src="assests/planet.jpg"  />
                <img src="assests/girl.jpg"  />
                <img src="assests/nim.jpg" />
                <img src="assests/1.png" />
                <img src="assests/house.jpg" />
                <img src="assests/5.png"  />
                <img src="assests/girl.jpg"  />
            </div>
        </div>

        <!-- NEW GAMES SECTION -->
        <div class="collabs-container">
            <div class="collabs-header">
                                    <li><a href="collaborations.php" class="super-button">Collaborations</a></li>

                <h1>Recent Games</h1>
                <a href="addgame.html" class="super-button">
                    ➕ Add your game
                </a>
            </div>
            
            <?php if (count($featuredProjects) > 0): ?>
                <!-- Animation Spot Publicitaire -->
                <div class="ad-spot-container" id="adSpotContainer">
                    <div class="ad-spot-slider" id="adSpotSlider">
                        <?php foreach ($featuredProjects as $index => $project): ?>
                            <?php
                                $image = !empty($project['image']) ? $project['image'] : $placeholderImage;
                                $age = isset($project['age_recommande']) && $project['age_recommande'] !== '' ? $project['age_recommande'] . '+' : '--';
                                $location = $project['lieu'] ?? 'Location not specified';
                                $dateCreation = $project['date_creation'] ?? '--';
                                $category = $project['categorie'] ?? 'Unknown category';
                            ?>
                            <div class="ad-spot-slide <?= $index === 0 ? 'active' : ''; ?>" data-index="<?= $index; ?>">
                                <div class="ad-spot-content">
                                    <div class="ad-spot-image">
                                        <img src="<?= htmlspecialchars($image); ?>" alt="<?= htmlspecialchars($project['nom'] ?? 'Jeu'); ?>">
                                        <div class="ad-spot-overlay"></div>
                                    </div>
                                    <div class="ad-spot-info">
                                        <h2 class="ad-spot-title"><?= htmlspecialchars($project['nom'] ?? 'Unnamed Project'); ?></h2>
                                        <p class="ad-spot-category"><?= htmlspecialchars($category); ?></p>
                                        <p class="ad-spot-description"><?= htmlspecialchars(substr($project['description'] ?? 'Discover this amazing game!', 0, 150)); ?>...</p>
                                        <a href="detail.php?id=<?= urlencode($project['id']); ?>" class="ad-spot-btn">
                                            <span>Discover Now</span>
                                            <span>→</span>
                                        </a>
                                    </div>
            </div>
        </div>
                        <?php endforeach; ?>
        </div>
                    <div class="ad-spot-indicators">
                        <?php foreach ($featuredProjects as $index => $project): ?>
                            <span class="ad-spot-dot <?= $index === 0 ? 'active' : ''; ?>" data-slide="<?= $index; ?>"></span>
                        <?php endforeach; ?>
            </div>
                    <button class="ad-spot-close" id="adSpotClose">✕</button>
        </div>
            <?php endif; ?>
            
            <?php if (count($featuredProjects) === 0): ?>
                <div class="empty-state">
                    <h3>No projects have been published yet</h3>
                    <p>Come back soon!</p>
                </div>
            <?php else: ?>
                <div class="collabs-grid">
                <?php foreach ($featuredProjects as $project): ?>
                    <?php
                        $image = !empty($project['image']) ? $project['image'] : $placeholderImage;
                        $age = isset($project['age_recommande']) && $project['age_recommande'] !== '' ? $project['age_recommande'] . '+' : '--';
                        $location = $project['lieu'] ?? 'Location not specified';
                        $dateCreation = $project['date_creation'] ?? '--';
                        $category = $project['categorie'] ?? 'Unknown category';
                    ?>
                        <div class="collab-card">
                            <?php if (!empty($image)): ?>
                                <div class="card-image-wrapper">
                                    <img src="<?= htmlspecialchars($image); ?>" alt="<?= htmlspecialchars($project['nom'] ?? 'Jeu'); ?>" class="collab-image">
                                </div>
                            <?php else: ?>
                                <div class="no-image">
                                    <img src="assests/logo.png" alt="Default Game Image" class="default-collab-image">
                                    <div class="no-image-overlay"></div>
                                </div>
                            <?php endif; ?>
                            
                            <h3><?= htmlspecialchars($project['nom'] ?? 'Unnamed Project'); ?></h3>
                            
                            <div class="collab-info">
                                <span class="statut ouvert">
                                    <?= htmlspecialchars($category); ?>
                                </span>
                                <span class="members-info">
                                    🎮 <?= htmlspecialchars($age); ?> • <?= htmlspecialchars($location); ?>
                                </span>
                            </div>
                            
                            <p class="description">
                                <strong>Date:</strong> <?= htmlspecialchars($dateCreation); ?><br>
                                <strong>Location:</strong> <?= htmlspecialchars($location); ?>
                            </p>
                            
                            <div class="card-actions">
                                <a href="detail.php?id=<?= urlencode($project['id']); ?>" class="btn-view">
                                    View
                                </a>
                            </div>
                        </div>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

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

        </main>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section about">
                <h3>GameHub Pro</h3>
                <p>The platform that connects independent developers with players from around the world.</p>
            </div>
            <div class="footer-section links">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="index.php#new-games">Recent Games</a></li>
                    <li><a href="#" class="dashboard-link" id="dashboardFooterBtn">Dashboard</a></li>
                   
                    <li><a href="collaborations.php"> Collaborations</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 GameHub Pro | All rights reserved | Tunis, Tunisia</p>
        </div>
    </footer>

    <!-- Chatbot HTML -->
    <?php include '../backoffice/collabcrud/chatbot.html'; ?>
    
    <script src="collaborations.js"></script>
    <script src="../backoffice/collabcrud/chatbot.js"></script>
<script>
    // Animation Spot Publicitaire
document.addEventListener('DOMContentLoaded', function() {
        const adContainer = document.getElementById('adSpotContainer');
        const slides = document.querySelectorAll('.ad-spot-slide');
        const dots = document.querySelectorAll('.ad-spot-dot');
        const closeBtn = document.getElementById('adSpotClose');
        let currentSlide = 0;
        let slideInterval;
        let isPaused = false;

        // Vérifier si on est sur la section #new-games
        function checkHash() {
            if (window.location.hash === '#new-games' || window.location.hash === '') {
                if (adContainer && !adContainer.classList.contains('hidden')) {
                    startSlideshow();
                }
            }
        }

        // Démarrer le carrousel
        function startSlideshow() {
            if (slides.length === 0) return;
            
            slideInterval = setInterval(() => {
                if (!isPaused) {
                    nextSlide();
                }
            }, 5000); // Change toutes les 5 secondes
        }

        // Slide suivante avec animation améliorée
        function nextSlide() {
            const prevSlide = currentSlide;
            slides[prevSlide].classList.remove('active');
            dots[prevSlide].classList.remove('active');
            
            // Ajouter effet de sortie
            slides[prevSlide].style.transform = 'scale(0.9) rotateY(-20deg) translateX(-50px)';
            slides[prevSlide].style.opacity = '0';
            
            currentSlide = (currentSlide + 1) % slides.length;
            
            // Préparer la nouvelle slide
            slides[currentSlide].style.transform = 'scale(0.9) rotateY(20deg) translateX(50px)';
            slides[currentSlide].style.opacity = '0';
            
            setTimeout(() => {
                slides[currentSlide].classList.add('active');
                dots[currentSlide].classList.add('active');
                slides[currentSlide].style.transform = '';
                slides[currentSlide].style.opacity = '';
            }, 50);
        }

        // Slide précédente avec animation améliorée
        function prevSlide() {
            const prevSlide = currentSlide;
            slides[prevSlide].classList.remove('active');
            dots[prevSlide].classList.remove('active');
            
            // Ajouter effet de sortie
            slides[prevSlide].style.transform = 'scale(0.9) rotateY(20deg) translateX(50px)';
            slides[prevSlide].style.opacity = '0';
            
            currentSlide = (currentSlide - 1 + slides.length) % slides.length;
            
            // Préparer la nouvelle slide
            slides[currentSlide].style.transform = 'scale(0.9) rotateY(-20deg) translateX(-50px)';
            slides[currentSlide].style.opacity = '0';
            
            setTimeout(() => {
                slides[currentSlide].classList.add('active');
                dots[currentSlide].classList.add('active');
                slides[currentSlide].style.transform = '';
                slides[currentSlide].style.opacity = '';
            }, 50);
        }

        // Aller à un slide spécifique avec animation
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                if (index === currentSlide) return;
                
                const prevSlide = currentSlide;
                slides[prevSlide].classList.remove('active');
                dots[prevSlide].classList.remove('active');
                
                // Effet de sortie
                slides[prevSlide].style.transform = 'scale(0.8)';
                slides[prevSlide].style.opacity = '0';
                
                currentSlide = index;
                
                // Préparer la nouvelle slide
                slides[currentSlide].style.transform = 'scale(1.1)';
                slides[currentSlide].style.opacity = '0';
                
                setTimeout(() => {
                    slides[currentSlide].classList.add('active');
                    dots[currentSlide].classList.add('active');
                    slides[currentSlide].style.transform = '';
                    slides[currentSlide].style.opacity = '';
                    slides[prevSlide].style.transform = '';
                    slides[prevSlide].style.opacity = '';
                }, 100);
                
                // Redémarrer le timer
                clearInterval(slideInterval);
                startSlideshow();
            });
        });

        // Fermer l'animation
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                if (adContainer) {
                    adContainer.classList.add('hidden');
                    clearInterval(slideInterval);
                    // Sauvegarder dans localStorage pour ne plus l'afficher
                    localStorage.setItem('adSpotClosed', 'true');
                }
            });
        }

        // Vérifier si l'animation a été fermée précédemment
        if (localStorage.getItem('adSpotClosed') === 'true') {
            if (adContainer) {
                adContainer.classList.add('hidden');
            }
        } else {
            // Pause au survol avec effet visuel
            if (adContainer) {
                adContainer.addEventListener('mouseenter', () => {
                    isPaused = true;
                    adContainer.style.filter = 'brightness(1.1)';
                });
                
                adContainer.addEventListener('mouseleave', () => {
                    isPaused = false;
                    adContainer.style.filter = '';
                });
            }

            // Ajouter des particules animées en arrière-plan
            function createParticles() {
                if (!adContainer) return;
                
                for (let i = 0; i < 20; i++) {
                    const particle = document.createElement('div');
                    particle.className = 'ad-particle';
                    particle.style.cssText = `
                        position: absolute;
                        width: ${Math.random() * 4 + 2}px;
                        height: ${Math.random() * 4 + 2}px;
                        background: rgba(0, 255, 136, ${Math.random() * 0.5 + 0.2});
                        border-radius: 50%;
                        pointer-events: none;
                        left: ${Math.random() * 100}%;
                        top: ${Math.random() * 100}%;
                        animation: particleFloat ${Math.random() * 10 + 10}s linear infinite;
                        animation-delay: ${Math.random() * 5}s;
                    `;
                    adContainer.appendChild(particle);
                }
            }

            // Créer les particules après un court délai pour s'assurer que le container est rendu
            setTimeout(() => {
                createParticles();
            }, 500);

            // Vérifier le hash au chargement
            checkHash();

            // Vérifier le hash lors du changement
            window.addEventListener('hashchange', checkHash);

            // Scroll vers #new-games si nécessaire
            if (window.location.hash === '#new-games') {
                setTimeout(() => {
                    const element = document.getElementById('new-games');
                    if (element) {
                        element.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }, 100);
            }
        }
    });

    // Dashboard button management in footer
    document.addEventListener('DOMContentLoaded', function() {
        const dashboardFooterBtn = document.getElementById('dashboardFooterBtn');
        
        if (dashboardFooterBtn) {
            dashboardFooterBtn.addEventListener('click', function(e) {
                e.preventDefault();
            
            // Display alert indicating admin login
            alert('You are logged in as administrator.\n\nTo access the Dashboard, please enter the access code.');
            
            // Request the code
            const code = prompt('Enter the Dashboard access code:');
            
            // Verify the code
            if (code === '0000') {
                    // Correct code, redirect to collaboration dashboard
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

