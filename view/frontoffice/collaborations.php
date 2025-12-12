<?php
session_start();

// Prevent caching to ensure fresh data
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Calculer le chemin racine du projet
$rootPath = dirname(dirname(__DIR__));

require_once $rootPath . "/controller/controllercollab/CollabProjectController.php";
require_once $rootPath . "/controller/controllercollab/CollabMemberController.php";

$projectController = new CollabProjectController();
$memberController = new CollabMemberController();

// Vérifier si l'utilisateur est connecté
$isLoggedIn = isset($_SESSION['user_id']);
$userId = $isLoggedIn ? $_SESSION['user_id'] : null;

// Vérifier si un message de succès/erreur doit être affiché
$showCreatedMessage = isset($_GET['created']);
$showSuccessMessage = isset($_GET['joined']) && $_GET['joined'] == '1';
$showDeletedMessage = isset($_GET['deleted']) && $_GET['deleted'] == '1';
$errorMessage = '';
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'already_member':
            $errorMessage = 'Vous êtes déjà membre de ce projet !';
            break;
        case 'full':
            $errorMessage = 'Ce projet a atteint le nombre maximum de membres.';
            break;
        default:
            $errorMessage = 'Une erreur est survenue.';
    }
}

// Fonction pour traduire le statut en anglais
function translateStatus($statut) {
    $translations = [
        'ouvert' => 'Open',
        'en_cours' => 'In Progress',
        'ferme' => 'Closed'
    ];
    return isset($translations[$statut]) ? $translations[$statut] : ucfirst($statut);
}

// Récupérer toutes les collaborations ouvertes
$collabs = $projectController->getAllOpen();

// Pour chaque collaboration, récupérer le nombre de membres
foreach ($collabs as &$collab) {
    $collab['current_members'] = $memberController->countMembers($collab['id']);
    if ($isLoggedIn) {
        $collab['is_member'] = $memberController->isMember($collab['id'], $userId);
        $collab['is_owner'] = ($collab['owner_id'] == $userId);
    } else {
        $collab['is_member'] = false;
        $collab['is_owner'] = false;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collaborations - GameHub Pro</title>
    <link rel="stylesheet" href="collaborations.css">
    <!-- Chatbot IA -->
    <link rel="stylesheet" href="../backoffice/collabcrud/chatbot.css">
</head>
<body>
    <header>
        <div class="container">
            <div style="display: flex; align-items: center; gap: 10px;">
                <img src="assests/logo.png" alt="Logo GameHub Pro" class="header-logo">
                <h1 class="logo">GameHub Pro</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php" class="super-button">Home</a></li>
                    <li><a href="index.php#new-games" class="super-button">Recent Games</a></li>
                    <li><a href="collaborations.php" class="super-button">Collaborations</a></li>
                    <li><a href="index.php#about" class="super-button">About</a></li>
                </ul>
            </nav>
            
            <button id="sidebar-toggle" class="sidebar-toggle">☰</button>
        </div>
    </header>

    <aside id="sidebar" class="sidebar">
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="index.php#new-games">Recent Games</a></li>
                <li><a href="collaborations.php"> Collaborations</a></li>
                <li><a href="index.php#about">About</a></li>
            </ul>
        </nav>
    </aside>

    <main id="main-content" class="main-content">
        <!-- Images décoratives flottantes en arrière-plan -->
        <div class="decorative-images">
            <img src="assests/logo.png" alt="Decor" class="decor-img decor-img-1">
            <img src="assests/game5.png" alt="Decor" class="decor-img decor-img-2">
            <img src="assests/logo.png" alt="Decor" class="decor-img decor-img-3">
        </div>
        
        <div class="collabs-container">
            <div class="collabs-header">
                <h1> Collaborations</h1>
                <a href="../backoffice/collabcrud/create_collab.php" class="super-button">
                    ➕ Add Collab
                </a>
            </div>
<div class="animated-strip">
    <div class="strip-content">
        <img src="assests /nim.jpg" />
        <img src="assests/rambling.jpg" />
        <img src="assests/house.jpg" />
        <img src="assests/planet.jpg"  />
        <img src="assests/girl.jpg"  />

             <img src="assests /nim.jpg" />
        <img src="assests/rambling.jpg" />
        <img src="assests/3.png" />
        <img src="assests/planet.jpg"  />
        <img src="assests/1.png"  />
                <img src="assests /nim.jpg" />
        <img src="assests/rambling.jpg" />
        <img src="assests/house.jpg" />
        <img src="assests/planet.jpg"  />
        <img src="assests/girl.jpg"  />
                <img src="assests /nim.jpg" />
        <img src="assests/rambling.jpg" />
        <img src="assests/house.jpg" />
        <img src="assests/planet.jpg"  />
        <img src="assests/girl.jpg"  />
                    <img src="assests /nim.jpg" />
        <img src="assests/1.png" />
        <img src="assests/house.jpg" />
        <img src="assests/5.png"  />
        <img src="assests/girl.jpg"  />
    </div>
</div>

            <?php if ($showCreatedMessage): ?>
                <div class="success-message" style="background: rgba(0, 255, 136, 0.2); color: #00ff88; padding: 15px; border-radius: 10px; margin-bottom: 2rem; border: 2px solid rgba(0, 255, 136, 0.5); text-align: center; font-weight: 600;">
                    ✅ Collaboration created successfully! Your project is now visible in the list.
                </div>
                <script>
                    // Remove URL parameter after display to force fresh load
                    setTimeout(function() {
                        window.history.replaceState({}, document.title, window.location.pathname);
                    }, 2000);
                </script>
            <?php endif; ?>

            <?php if ($showSuccessMessage): ?>
                <div class="success-message" style="background: rgba(0, 255, 136, 0.2); color: #00ff88; padding: 15px; border-radius: 10px; margin-bottom: 2rem; border: 2px solid rgba(0, 255, 136, 0.5); text-align: center; font-weight: 600;">
                    ✅ You have successfully joined the collaboration!
                </div>
                <script>
                    // Supprimer le paramètre de l'URL après affichage
                    setTimeout(function() {
                        window.history.replaceState({}, document.title, window.location.pathname);
                    }, 3000);
                </script>
            <?php endif; ?>

            <?php if ($showDeletedMessage): ?>
                <div class="success-message" style="background: rgba(255, 51, 92, 0.2); color: #ff335c; padding: 15px; border-radius: 10px; margin-bottom: 2rem; border: 2px solid rgba(255, 51, 92, 0.5); text-align: center; font-weight: 600;">
                     Collaboration deleted successfully!
                </div>
                <script>
                    // Supprimer le paramètre de l'URL après affichage
                    setTimeout(function() {
                        window.history.replaceState({}, document.title, window.location.pathname);
                    }, 3000);
                </script>
            <?php endif; ?>

            <?php if (!empty($errorMessage)): ?>
                <div class="error-message" style="background: rgba(255, 51, 92, 0.2); color: #ff335c; padding: 15px; border-radius: 10px; margin-bottom: 2rem; border: 2px solid rgba(255, 51, 92, 0.5); text-align: center; font-weight: 600;">
                    ❌ <?php echo htmlspecialchars($errorMessage); ?>
                </div>
                <script>
                    // Supprimer le paramètre de l'URL après affichage
                    setTimeout(function() {
                        window.history.replaceState({}, document.title, window.location.pathname);
                    }, 3000);
                </script>
            <?php endif; ?>

            <?php if (empty($collabs)): ?>
                <div class="empty-state">
                    <h3>No collaborations available</h3>
                    <p>Be the first to create a collaboration!</p>
                </div>
            <?php else: ?>
                <div class="collabs-grid">
                    <?php foreach ($collabs as $collab): 
                        // Calculer si on peut rejoindre (même sans connexion en mode développeur)
                        $canJoin = !$collab['is_member'] && 
                                  !$collab['is_owner'] && 
                                  $collab['current_members'] < $collab['max_membres'] &&
                                  $collab['statut'] === 'ouvert';
                    ?>
                        <div class="collab-card">
                            <?php if (!empty($collab['image'])): ?>
                                <div class="card-image-wrapper">
                                    <img src="<?php echo htmlspecialchars($collab['image']); ?>" alt="<?php echo htmlspecialchars($collab['titre']); ?>" class="collab-image">
                                </div>
                            <?php else: ?>
                                <div class="no-image">
                                    <img src="assests/logo.png" alt="Default Collaboration Image" class="default-collab-image">
                                    <div class="no-image-overlay"></div>
                                </div>
                            <?php endif; ?>
                            
                            <h3><?php echo htmlspecialchars($collab['titre']); ?></h3>
                            
                            <div class="collab-info">
                                <span class="statut <?php echo htmlspecialchars($collab['statut']); ?>">
                                    <?php echo translateStatus($collab['statut']); ?>
                                </span>
                                <span class="members-info">
                                    👥 <?php echo $collab['current_members']; ?>/<?php echo $collab['max_membres']; ?> membres
                                </span>
                            </div>
                            
                            <p class="description"><?php echo htmlspecialchars($collab['description']); ?></p>
                            
                            <div class="card-actions">
                                <a href="../backoffice/collabcrud/view_collab.php?id=<?php echo $collab['id']; ?>" class="btn-view">
                                     See
                                </a>
                                
                                <?php if ($isLoggedIn): ?>
                                    <?php if ($collab['is_member']): ?>
                                        <span class="btn-joined">✓ Déjà membre</span>
                                    <?php elseif ($collab['is_owner']): ?>
                                        <span class="btn-owner">👑owner</span>
                                    <?php elseif ($canJoin): ?>
                                        <form action="../backoffice/collabcrud/join_collab.php" method="POST" style="display: inline;">
                                            <input type="hidden" name="collab_id" value="<?php echo $collab['id']; ?>">
                                            <button type="submit" class="btn-join" onclick="return confirm('Do you want to join this collaboration?');">
                                                ➕ Join
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="btn-full">✗ Complet</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?php if ($canJoin): ?>
                                        <form action="../backoffice/collabcrud/join_collab.php" method="POST" class="join-collab-form-dev" style="display: inline;">
                                            <input type="hidden" name="collab_id" value="<?php echo $collab['id']; ?>">
                                            <input type="hidden" name="user_id" value="">
                                            <button type="submit" class="btn-join" title="Choisir un ID (mode admin)">
                                                ➕ Join
                                            </button>
                                        </form>
                                    <?php elseif ($collab['current_members'] >= $collab['max_membres']): ?>
                                        <span class="btn-full">✗ Complet</span>
                                    <?php else: ?>
                                        <span class="btn-full">✗ Not available</span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
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
    // Mode admin (non connecté) : demander un ID avant de rejoindre
    document.addEventListener('DOMContentLoaded', function() {
        const devJoinForms = document.querySelectorAll('.join-collab-form-dev');
        devJoinForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const userIdInput = form.querySelector('input[name="user_id"]');
                let chosenId = prompt("Entrez l'ID utilisateur à utiliser (mode admin) :", "1");
                if (!chosenId) {
                    e.preventDefault();
                    return false;
                }
                chosenId = chosenId.trim();
                if (!/^[0-9]+$/.test(chosenId)) {
                    e.preventDefault();
                    alert("ID invalide. Merci de saisir uniquement des chiffres.");
                    return false;
                }
                userIdInput.value = chosenId;
                return true;
            });
        });
    });
    </script>
    <script>
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
                    window.location.href = '../backoffice/collabcrud/collaboration.php';
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
