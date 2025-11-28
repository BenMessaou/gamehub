<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../frontoffice/index.php");
    exit;
}

// Calculer le chemin racine du projet
$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . "/controller/controllercollab/CollabProjectController.php";
require_once $rootPath . "/controller/controllercollab/CollabMemberController.php";
require_once $rootPath . "/model/collab/CollabProject.php";

$projectController = new CollabProjectController();
$memberController = new CollabMemberController();

// Traiter le formulaire de création
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $owner_id = $_SESSION['user_id'];
    $titre = trim($_POST['titre']);
    $description = trim($_POST['description']);
    $max_membres = intval($_POST['max_membres']);
    $image = trim($_POST['image'] ?? '');

    if (!empty($titre) && !empty($description) && $max_membres > 0) {
        $collab = new CollabProject(
            null,
            $owner_id,
            $titre,
            $description,
            date("Y-m-d"),
            "ouvert",
            $max_membres,
            $image ?: null
        );

        $createdId = $projectController->create($collab);

        if ($createdId) {
            $message = 'Collaboration créée avec succès !';
            $messageType = 'success';
            // Rediriger vers la page de visualisation
            header("Location: view_collab.php?id=" . $createdId);
            exit;
        } else {
            $message = 'Erreur : impossible de créer la collaboration.';
            $messageType = 'error';
        }
    } else {
        $message = 'Veuillez remplir tous les champs requis.';
        $messageType = 'error';
    }
}

// Récupérer toutes les collaborations ouvertes
$collabs = $projectController->getAllOpen();

// Récupérer les projets de l'utilisateur
$myCollabs = $projectController->getByOwner($_SESSION['user_id']);

// Pour chaque collaboration, récupérer le nombre de membres
foreach ($collabs as &$collab) {
    $collab['current_members'] = $memberController->countMembers($collab['id']);
    $collab['is_member'] = $memberController->isMember($collab['id'], $_SESSION['user_id']);
    $collab['is_owner'] = ($collab['owner_id'] == $_SESSION['user_id']);
}

// Pour mes collaborations aussi
foreach ($myCollabs as &$collab) {
    $collab['current_members'] = $memberController->countMembers($collab['id']);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collaborations - GameHub</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 50%, #16213e 100%);
            color: #e8dfff;
            min-height: 100vh;
            padding: 20px;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 20% 50%, rgba(255, 0, 150, 0.1) 0%, transparent 50%),
                        radial-gradient(circle at 80% 20%, rgba(0, 255, 150, 0.1) 0%, transparent 50%),
                        radial-gradient(circle at 40% 80%, rgba(150, 0, 255, 0.1) 0%, transparent 50%);
            pointer-events: none;
            z-index: -1;
            animation: rgbShift 10s ease-in-out infinite alternate;
        }

        @keyframes rgbShift {
            0% { filter: hue-rotate(0deg); }
            100% { filter: hue-rotate(360deg); }
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
            padding-top: 20px;
        }

        .header h1 {
            font-size: 3rem;
            background: linear-gradient(90deg, #ff00c7, #00ffea);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
            text-shadow: 0 0 20px rgba(255, 0, 199, 0.5);
        }

        .header p {
            color: #b8a8d9;
            font-size: 1.1rem;
        }

        .tabs {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 40px;
            flex-wrap: wrap;
        }

        .tab-button {
            padding: 12px 30px;
            background: rgba(255, 0, 199, 0.1);
            border: 2px solid rgba(255, 0, 199, 0.3);
            color: #e8dfff;
            border-radius: 25px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .tab-button:hover,
        .tab-button.active {
            background: rgba(255, 0, 199, 0.3);
            border-color: #ff00c7;
            box-shadow: 0 0 20px rgba(255, 0, 199, 0.5);
            transform: translateY(-2px);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .create-form {
            background: rgba(15, 5, 35, 0.95);
            padding: 40px;
            border-radius: 20px;
            border: 2px solid rgba(255, 0, 199, 0.3);
            max-width: 700px;
            margin: 0 auto;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.5);
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #00ffea;
            font-weight: 600;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 0, 199, 0.3);
            border-radius: 10px;
            color: #e8dfff;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #ff00c7;
            box-shadow: 0 0 15px rgba(255, 0, 199, 0.3);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }

        .btn {
            padding: 15px 40px;
            background: linear-gradient(135deg, #ff00c7, #00ffea);
            border: none;
            border-radius: 25px;
            color: white;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 5px 20px rgba(255, 0, 199, 0.4);
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(255, 0, 199, 0.6);
        }

        .btn-secondary {
            background: rgba(255, 0, 199, 0.2);
            border: 2px solid rgba(255, 0, 199, 0.5);
        }

        .collabs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }

        .collab-card {
            background: rgba(15, 5, 35, 0.95);
            border-radius: 20px;
            padding: 25px;
            border: 2px solid rgba(255, 0, 199, 0.3);
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
            position: relative;
            overflow: hidden;
        }

        .collab-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #ff00c7, #00ffea);
        }

        .collab-card:hover {
            transform: translateY(-5px);
            border-color: #ff00c7;
            box-shadow: 0 12px 35px rgba(255, 0, 199, 0.5);
        }

        .collab-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 15px;
            margin-bottom: 15px;
            border: 2px solid rgba(255, 0, 199, 0.2);
        }

        .collab-card h3 {
            color: #ff00c7;
            margin-bottom: 10px;
            font-size: 1.5rem;
        }

        .collab-card .description {
            color: #b8a8d9;
            margin-bottom: 15px;
            line-height: 1.6;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .collab-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .collab-info .statut {
            padding: 5px 15px;
            border-radius: 15px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .statut.ouvert {
            background: rgba(0, 255, 234, 0.2);
            color: #00ffea;
            border: 1px solid rgba(0, 255, 234, 0.5);
        }

        .statut.en_cours {
            background: rgba(255, 215, 0, 0.2);
            color: #ffd700;
            border: 1px solid rgba(255, 215, 0, 0.5);
        }

        .statut.ferme {
            background: rgba(255, 51, 92, 0.2);
            color: #ff335c;
            border: 1px solid rgba(255, 51, 92, 0.5);
        }

        .members-info {
            color: #00ffea;
            font-size: 0.95rem;
        }

        .card-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            flex-wrap: wrap;
        }

        .btn-small {
            padding: 8px 20px;
            font-size: 0.9rem;
            border-radius: 15px;
            text-decoration: none;
            transition: all 0.3s ease;
            border: 2px solid;
            font-weight: 600;
        }

        .btn-view {
            background: rgba(0, 255, 234, 0.1);
            color: #00ffea;
            border-color: rgba(0, 255, 234, 0.5);
        }

        .btn-view:hover {
            background: rgba(0, 255, 234, 0.3);
            box-shadow: 0 0 15px rgba(0, 255, 234, 0.5);
        }

        .btn-join {
            background: rgba(255, 0, 199, 0.1);
            color: #ff00c7;
            border-color: rgba(255, 0, 199, 0.5);
        }

        .btn-join:hover {
            background: rgba(255, 0, 199, 0.3);
            box-shadow: 0 0 15px rgba(255, 0, 199, 0.5);
        }

        .btn-edit {
            background: rgba(255, 215, 0, 0.1);
            color: #ffd700;
            border-color: rgba(255, 215, 0, 0.5);
        }

        .btn-edit:hover {
            background: rgba(255, 215, 0, 0.3);
            box-shadow: 0 0 15px rgba(255, 215, 0, 0.5);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #b8a8d9;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #ff00c7;
        }

        .message {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 600;
        }

        .message.success {
            background: rgba(0, 255, 234, 0.2);
            color: #00ffea;
            border: 2px solid rgba(0, 255, 234, 0.5);
        }

        .message.error {
            background: rgba(255, 51, 92, 0.2);
            color: #ff335c;
            border: 2px solid rgba(255, 51, 92, 0.5);
        }

        @media (max-width: 768px) {
            .collabs-grid {
                grid-template-columns: 1fr;
            }

            .header h1 {
                font-size: 2rem;
            }

            .create-form {
                padding: 25px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🤝 Collaborations</h1>
            <p>Créez ou rejoignez des projets collaboratifs</p>
        </div>

        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="tabs">
            <button class="tab-button active" onclick="switchTab('list')">📋 Liste des Collaborations</button>
            <button class="tab-button" onclick="switchTab('create')">➕ Créer une Collaboration</button>
            <button class="tab-button" onclick="switchTab('my-collabs')">⭐ Mes Collaborations</button>
        </div>

        <!-- Onglet Liste des Collaborations -->
        <div id="list" class="tab-content active">
            <?php if (empty($collabs)): ?>
                <div class="empty-state">
                    <h3>Aucune collaboration disponible</h3>
                    <p>Soyez le premier à créer une collaboration !</p>
                </div>
            <?php else: ?>
                <div class="collabs-grid">
                    <?php foreach ($collabs as $collab): 
                        $canJoin = !$collab['is_member'] && 
                                  !$collab['is_owner'] && 
                                  $collab['current_members'] < $collab['max_membres'] &&
                                  $collab['statut'] === 'ouvert';
                    ?>
                        <div class="collab-card">
                            <?php if (!empty($collab['image'])): ?>
                                <img src="<?php echo htmlspecialchars($collab['image']); ?>" alt="<?php echo htmlspecialchars($collab['titre']); ?>">
                            <?php else: ?>
                                <div style="width: 100%; height: 200px; background: linear-gradient(135deg, #ff00c7, #00ffea); border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 3rem; margin-bottom: 15px;">
                                    🤝
                                </div>
                            <?php endif; ?>
                            
                            <h3><?php echo htmlspecialchars($collab['titre']); ?></h3>
                            
                            <div class="collab-info">
                                <span class="statut <?php echo htmlspecialchars($collab['statut']); ?>">
                                    <?php echo ucfirst($collab['statut']); ?>
                                </span>
                                <span class="members-info">
                                    👥 <?php echo $collab['current_members']; ?>/<?php echo $collab['max_membres']; ?> membres
                                </span>
                            </div>
                            
                            <p class="description"><?php echo htmlspecialchars($collab['description']); ?></p>
                            
                            <div class="card-actions">
                                <a href="view_collab.php?id=<?php echo $collab['id']; ?>" class="btn-small btn-view">
                                    👁️ Voir
                                </a>
                                <?php if ($collab['is_owner']): ?>
                                    <a href="edit_collab.php?id=<?php echo $collab['id']; ?>" class="btn-small btn-edit">
                                        ✏️ Modifier
                                    </a>
                                <?php elseif ($canJoin): ?>
                                    <form action="join_collab.php" method="POST" style="display: inline;">
                                        <input type="hidden" name="collab_id" value="<?php echo $collab['id']; ?>">
                                        <button type="submit" class="btn-small btn-join" onclick="return confirm('Voulez-vous rejoindre cette collaboration ?');">
                                            ➕ Rejoindre
                                        </button>
                                    </form>
                                <?php elseif ($collab['is_member']): ?>
                                    <span style="color: #00ffea; font-size: 0.9rem;">✓ Déjà membre</span>
                                <?php elseif ($collab['current_members'] >= $collab['max_membres']): ?>
                                    <span style="color: #ff335c; font-size: 0.9rem;">✗ Complet</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Onglet Créer une Collaboration -->
        <div id="create" class="tab-content">
            <div class="create-form">
                <h2 style="text-align: center; margin-bottom: 30px; color: #ff00c7;">Créer une Nouvelle Collaboration</h2>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="form-group">
                        <label for="titre">Titre du projet *</label>
                        <input type="text" id="titre" name="titre" required placeholder="Ex: Développement d'un jeu de stratégie">
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description *</label>
                        <textarea id="description" name="description" required placeholder="Décrivez votre projet collaboratif..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="max_membres">Nombre maximum de membres *</label>
                        <input type="number" id="max_membres" name="max_membres" min="1" max="20" value="5" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="image">URL de l'image (optionnel)</label>
                        <input type="text" id="image" name="image" placeholder="https://example.com/image.jpg">
                    </div>
                    
                    <div style="text-align: center; margin-top: 30px;">
                        <button type="submit" class="btn">🚀 Créer la Collaboration</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Onglet Mes Collaborations -->
        <div id="my-collabs" class="tab-content">
            <?php if (empty($myCollabs)): ?>
                <div class="empty-state">
                    <h3>Vous n'avez pas encore créé de collaboration</h3>
                    <p>Créez votre première collaboration pour commencer !</p>
                </div>
            <?php else: ?>
                <div class="collabs-grid">
                    <?php foreach ($myCollabs as $collab): ?>
                        <div class="collab-card">
                            <?php if (!empty($collab['image'])): ?>
                                <img src="<?php echo htmlspecialchars($collab['image']); ?>" alt="<?php echo htmlspecialchars($collab['titre']); ?>">
                            <?php else: ?>
                                <div style="width: 100%; height: 200px; background: linear-gradient(135deg, #ff00c7, #00ffea); border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 3rem; margin-bottom: 15px;">
                                    🤝
                                </div>
                            <?php endif; ?>
                            
                            <h3><?php echo htmlspecialchars($collab['titre']); ?></h3>
                            
                            <div class="collab-info">
                                <span class="statut <?php echo htmlspecialchars($collab['statut']); ?>">
                                    <?php echo ucfirst($collab['statut']); ?>
                                </span>
                                <span class="members-info">
                                    👥 <?php echo $collab['current_members']; ?>/<?php echo $collab['max_membres']; ?> membres
                                </span>
                            </div>
                            
                            <p class="description"><?php echo htmlspecialchars($collab['description']); ?></p>
                            
                            <div class="card-actions">
                                <a href="view_collab.php?id=<?php echo $collab['id']; ?>" class="btn-small btn-view">
                                    👁️ Voir
                                </a>
                                <a href="edit_collab.php?id=<?php echo $collab['id']; ?>" class="btn-small btn-edit">
                                    ✏️ Modifier
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function switchTab(tabName) {
            // Masquer tous les onglets
            const tabs = document.querySelectorAll('.tab-content');
            tabs.forEach(tab => tab.classList.remove('active'));
            
            // Désactiver tous les boutons
            const buttons = document.querySelectorAll('.tab-button');
            buttons.forEach(btn => btn.classList.remove('active'));
            
            // Afficher l'onglet sélectionné
            document.getElementById(tabName).classList.add('active');
            
            // Activer le bouton correspondant
            event.target.classList.add('active');
        }
    </script>
</body>
</html>

