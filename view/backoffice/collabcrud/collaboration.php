<?php
session_start();

// Mode développeur : permettre l'accès même sans connexion
$isLoggedIn = isset($_SESSION['user_id']);
$userId = $isLoggedIn ? $_SESSION['user_id'] : null;

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
    $defaultOwnerId = 1; // ID par défaut pour le développeur
    $owner_id = $isLoggedIn ? $userId : (isset($_POST['owner_id']) ? intval($_POST['owner_id']) : $defaultOwnerId);
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
            $message = 'Collaboration created successfully!';
            $messageType = 'success';
            // Rediriger vers la page de visualisation
            header("Location: view_collab.php?id=" . $createdId);
            exit;
        } else {
            $message = 'Error: Unable to create the collaboration.';
            $messageType = 'error';
        }
    } else {
        $message = 'Please fill in all required fields.';
        $messageType = 'error';
    }
}

// Calculer les statistiques des collaborations
$rootPath = dirname(dirname(dirname(__DIR__)));
require_once $rootPath . "/config/config.php";
$db = config::getConnexion();

// Récupérer toutes les collaborations (pour les stats et l'affichage)
$allCollabsStmt = $db->query("SELECT * FROM collab_project ORDER BY date_creation DESC");
$allCollabs = $allCollabsStmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer toutes les collaborations ouvertes (pour compatibilité avec le reste du code)
$collabs = $projectController->getAllOpen();

// Grouper les collaborations par statut pour l'affichage dans l'onglet Liste
$collabsByStatus = [
    'ouvert' => [],
    'en_cours' => [],
    'ferme' => []
];

foreach ($allCollabs as $collab) {
    $statut = $collab['statut'] ?? 'ouvert';
    if (isset($collabsByStatus[$statut])) {
        $collabsByStatus[$statut][] = $collab;
    } else {
        $collabsByStatus['ouvert'][] = $collab; // Par défaut, mettre dans ouvert
    }
}

// Récupérer les projets de l'utilisateur (seulement si connecté)
$myCollabs = [];
if ($isLoggedIn) {
    $myCollabs = $projectController->getByOwner($userId);
} else {
    // En mode développeur, afficher toutes les collaborations dans "Mes Collaborations"
    $myCollabs = $collabs;
}

// Calculer les statistiques
$stats = [
    'total' => count($allCollabs),
    'ouvert' => 0,
    'en_cours' => 0,
    'ferme' => 0,
    'total_membres' => 0,
    'thisWeek' => 0,
    'thisMonth' => 0,
    'avg_membres' => 0,
    'max_capacity' => 0,
    'utilization_rate' => 0
];

$now = new DateTime();
$weekAgo = clone $now;
$weekAgo->modify('-7 days');
$monthAgo = clone $now;
$monthAgo->modify('-30 days');

$totalMembers = 0;
$totalCapacity = 0;

foreach ($allCollabs as $collab) {
    // Par statut
    $statut = $collab['statut'] ?? 'ouvert';
    if (isset($stats[$statut])) {
        $stats[$statut]++;
    }
    
    // Par date
    $dateCreation = new DateTime($collab['date_creation']);
    if ($dateCreation >= $weekAgo) {
        $stats['thisWeek']++;
    }
    if ($dateCreation >= $monthAgo) {
        $stats['thisMonth']++;
    }
    
    // Membres et capacité
    $membersCount = $memberController->countMembers($collab['id']);
    $totalMembers += $membersCount;
    $totalCapacity += $collab['max_membres'];
}

$stats['total_membres'] = $totalMembers;
$stats['max_capacity'] = $totalCapacity;
$stats['avg_membres'] = $stats['total'] > 0 ? round($totalMembers / $stats['total'], 2) : 0;
$stats['utilization_rate'] = $totalCapacity > 0 ? round(($totalMembers / $totalCapacity) * 100, 2) : 0;

// Pour chaque collaboration, récupérer le nombre de members
foreach ($collabs as &$collab) {
    $collab['current_members'] = $memberController->countMembers($collab['id']);
    if ($isLoggedIn) {
        $collab['is_member'] = $memberController->isMember($collab['id'], $userId);
        $collab['is_owner'] = ($collab['owner_id'] == $userId);
    } else {
        // Mode développeur : pas de membre/owner défini
        $collab['is_member'] = false;
        $collab['is_owner'] = false;
    }
}

// Pour chaque collaboration dans les groupes par statut, récupérer le nombre de members
foreach ($collabsByStatus as $statut => &$collabsList) {
    foreach ($collabsList as &$collab) {
        $collab['current_members'] = $memberController->countMembers($collab['id']);
    }
}
unset($collabsList, $collab); // Libérer les références

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

        /* Super Button - From admin dashboard template */
        .tab-button {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 14px 28px;
            background: linear-gradient(145deg, #0f0f0f, #1c1c1c);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 100px;
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            letter-spacing: 0.5px;
            cursor: pointer;
            overflow: hidden;
            transition: all 0.4s ease-in-out;
            box-shadow: 0 0 20px rgba(0, 255, 255, 0.1);
            backdrop-filter: blur(8px);
            z-index: 1;
            text-decoration: none;
            font-family: inherit;
        }

        .tab-button::before {
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

        .tab-button::after {
            content: "";
            position: absolute;
            inset: 2px;
            background: #0a0a0a;
            border-radius: inherit;
            z-index: -1;
        }

        .tab-button:hover {
            transform: scale(1.05);
            box-shadow: 0 0 40px rgba(0, 255, 255, 0.2);
            color: #90EE90;
        }

        .tab-button.active {
            background: linear-gradient(145deg, rgba(255, 0, 199, 0.3), rgba(0, 255, 234, 0.3));
            border-color: rgba(255, 0, 199, 0.8);
            box-shadow: 0 0 30px rgba(255, 0, 199, 0.6), 0 0 40px rgba(0, 255, 234, 0.4);
            color: #fff;
        }

        .tab-button.active::after {
            background: rgba(255, 0, 199, 0.1);
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Special style for Statistics button - always has gradient */
        #statisticsBtn {
            background: linear-gradient(135deg, rgba(255, 0, 199, 0.2), rgba(0, 255, 234, 0.2));
            border-color: rgba(255, 0, 199, 0.5);
        }

        #statisticsBtn::after {
            background: rgba(255, 0, 199, 0.05);
        }

        #statisticsBtn.active,
        #statisticsBtn:hover {
            background: linear-gradient(135deg, rgba(255, 0, 199, 0.4), rgba(0, 255, 234, 0.4));
            border-color: rgba(255, 0, 199, 0.8);
            box-shadow: 0 0 40px rgba(255, 0, 199, 0.5), 0 0 40px rgba(0, 255, 234, 0.3);
        }

        #statisticsBtn.active::after {
            background: rgba(255, 0, 199, 0.15);
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

        .collabs-list-container {
            margin-top: 30px;
            overflow-x: auto;
        }

        .collabs-list-table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(15, 5, 35, 0.95);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
        }

        .collabs-list-table thead {
            background: linear-gradient(135deg, rgba(255, 0, 199, 0.3), rgba(0, 255, 234, 0.3));
        }

        .collabs-list-table th {
            padding: 18px 20px;
            text-align: left;
            color: #fff;
            font-weight: 700;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-bottom: 2px solid rgba(255, 0, 199, 0.5);
        }

        .collabs-list-table tbody tr {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .collabs-list-table tbody tr:hover {
            background: rgba(255, 0, 199, 0.1);
            transform: scale(1.01);
        }

        .collabs-list-table tbody tr:last-child {
            border-bottom: none;
        }

        .collabs-list-table td {
            padding: 15px 20px;
            color: #ddd;
            font-size: 1rem;
        }

        .collabs-list-table .collab-name {
            font-weight: 600;
            color: #00ffea;
            font-size: 1.1rem;
        }

        .collabs-list-table .owner-id {
            color: #ffd700;
            font-family: 'Courier New', monospace;
        }

        .collabs-list-table .creation-date {
            color: #aaa;
            font-size: 0.95rem;
        }

        .collabs-list-table .list-actions {
            text-align: center;
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

            .collabs-list-table {
                font-size: 0.9rem;
            }

            .collabs-list-table th,
            .collabs-list-table td {
                padding: 12px 10px;
            }

            .collabs-list-table th {
                font-size: 0.9rem;
            }

            .collabs-list-table .collab-name {
                font-size: 1rem;
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
            <h1> Collaborations</h1>
            <p>Create or join collaborative projects</p>
        </div>

        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="tabs">
            <button class="tab-button active" onclick="switchTab('list')">📋 Collaborations List</button>
            <button class="tab-button" onclick="switchTab('create')">➕ Create Collaboration</button>
            <button class="tab-button" onclick="switchTab('my-collabs')">⭐ My Collaborations</button>
            <button class="tab-button" id="statisticsBtn">📊 Statistics</button>
        </div>

        <!-- Onglet Liste des Collaborations -->
        <div id="list" class="tab-content active">
            <?php 
            // Vérifier s'il y a des collaborations
            $totalCollabs = count($collabsByStatus['ouvert']) + count($collabsByStatus['en_cours']) + count($collabsByStatus['ferme']);
            ?>
            <?php if ($totalCollabs == 0): ?>
                <div class="empty-state">
                    <h3>No collaborations available</h3>
                    <p>Be the first to create a collaboration!</p>
                </div>
            <?php else: ?>
                <div class="collabs-list-container">
                    <?php
                    // Fonction pour traduire le statut
                    function translateStatusForDisplay($statut) {
                        $translations = [
                            'ouvert' => 'Open',
                            'en_cours' => 'In Progress',
                            'ferme' => 'Closed'
                        ];
                        return isset($translations[$statut]) ? $translations[$statut] : ucfirst($statut);
                    }
                    
                    // Afficher les collaborations par statut dans l'ordre demandé
                    $statusOrder = ['ouvert', 'en_cours', 'ferme'];
                    $statusColors = [
                        'ouvert' => ['bg' => 'rgba(0, 255, 234, 0.2)', 'border' => 'rgba(0, 255, 234, 0.5)', 'text' => '#00ffea'],
                        'en_cours' => ['bg' => 'rgba(255, 215, 0, 0.2)', 'border' => 'rgba(255, 215, 0, 0.5)', 'text' => '#ffd700'],
                        'ferme' => ['bg' => 'rgba(255, 51, 92, 0.2)', 'border' => 'rgba(255, 51, 92, 0.5)', 'text' => '#ff335c']
                    ];
                    
                    foreach ($statusOrder as $statut):
                        $collabsList = $collabsByStatus[$statut];
                        if (!empty($collabsList)):
                    ?>
                        <div class="status-section" style="margin-bottom: 3rem;">
                            <h2 class="status-header" style="color: <?php echo $statusColors[$statut]['text']; ?>; margin-bottom: 1.5rem; padding: 15px; background: <?php echo $statusColors[$statut]['bg']; ?>; border: 2px solid <?php echo $statusColors[$statut]['border']; ?>; border-radius: 10px; text-align: center; font-size: 1.5rem;">
                                📋 <?php echo translateStatusForDisplay($statut); ?> (<?php echo count($collabsList); ?>)
                            </h2>
                            <table class="collabs-list-table">
                                <thead>
                                    <tr>
                                        <th>Collaboration Name</th>
                                        <th>Owner ID</th>
                                        <th>Creation Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($collabsList as $collab): ?>
                                        <tr>
                                            <td class="collab-name"><?php echo htmlspecialchars($collab['titre']); ?></td>
                                            <td class="owner-id"><?php echo htmlspecialchars($collab['owner_id']); ?></td>
                                            <td class="creation-date"><?php echo date('Y-m-d H:i', strtotime($collab['date_creation'])); ?></td>
                                            <td class="list-actions">
                                                <a href="view_collab.php?id=<?php echo $collab['id']; ?>" class="btn-small btn-view">See</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php
                        endif;
                    endforeach;
                    ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Onglet Créer une Collaboration -->
        <div id="create" class="tab-content">
            <div class="create-form">
                <h2 style="text-align: center; margin-bottom: 30px; color: #ff00c7;">Create a New Collaboration</h2>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="form-group">
                        <label for="titre">Project Title *</label>
                        <input type="text" id="titre" name="titre" placeholder="Ex: Strategy Game Development">
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description *</label>
                        <textarea id="description" name="description" placeholder="Describe your collaborative project..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="max_membres">Maximum Number of Members *</label>
                        <input type="number" id="max_membres" name="max_membres" min="1" max="20" value="5">
                    </div>
                    
                    <div class="form-group">
                        <label for="image">Image URL (optional)</label>
                        <input type="text" id="image" name="image" placeholder="https://example.com/image.jpg">
                    </div>
                    
                    <div style="text-align: center; margin-top: 30px;">
                        <button type="submit" class="btn">🚀 Create Collaboration</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Onglet Mes Collaborations -->
        <div id="my-collabs" class="tab-content">
            <?php if (empty($myCollabs)): ?>
                <div class="empty-state">
                    <h3>You haven't created any collaboration yet</h3>
                    <p>Create your first collaboration to get started!</p>
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
                                    <?php 
                                    $translations = [
                                        'ouvert' => 'Open',
                                        'en_cours' => 'In Progress',
                                        'ferme' => 'Closed'
                                    ];
                                    echo isset($translations[$collab['statut']]) ? $translations[$collab['statut']] : ucfirst($collab['statut']); 
                                    ?>
                                </span>
                                <span class="members-info">
                                    👥 <?php echo $collab['current_members']; ?>/<?php echo $collab['max_membres']; ?> members
                                </span>
                            </div>
                            
                            <p class="description"><?php echo htmlspecialchars($collab['description']); ?></p>
                            
                            <div class="card-actions">
                                <a href="view_collab.php?id=<?php echo $collab['id']; ?>" class="btn-small btn-view">
                                    See
                                </a>
                                <a href="edit_collab.php?id=<?php echo $collab['id']; ?>" class="btn-small btn-edit">
                                    Edit
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
            
            // Désactiver tous les boutons (sauf le bouton statistiques)
            const buttons = document.querySelectorAll('.tab-button');
            buttons.forEach(btn => {
                if (btn.id !== 'statisticsBtn') {
                    btn.classList.remove('active');
                }
            });
            
            // Afficher l'onglet sélectionné
            document.getElementById(tabName).classList.add('active');
            
            // Activer le bouton correspondant
            if (event && event.target && event.target.id !== 'statisticsBtn') {
                event.target.classList.add('active');
            }
        }
    </script>

    <!-- Modal des statistiques -->
    <div id="statisticsModal" class="statistics-modal" style="display: none;">
        <div class="statistics-modal-content">
            <div class="statistics-modal-header">
                <div>
                    <h2>📊 Collaboration Statistics</h2>
                    <p class="statistics-subtitle">Complete analysis of your collaborations</p>
                </div>
                <button class="statistics-modal-close" id="closeStatisticsBtn">&times;</button>
            </div>
            <div class="statistics-modal-body">
                <!-- Cartes statistiques principales -->
                <div class="statistics-grid-main">
                    <div class="stat-card stat-card-primary">
                        <div class="stat-card-icon">🤝</div>
                        <div class="stat-card-content">
                            <div class="stat-card-value" data-target="<?= $stats['total']; ?>">0</div>
                            <div class="stat-card-label">Total Collaborations</div>
                            <div class="stat-card-trend">
                                <span class="trend-up">↗</span> <?= $stats['thisWeek']; ?> this week
                            </div>
                        </div>
                    </div>
                    <div class="stat-card stat-card-success">
                        <div class="stat-card-icon">🆕</div>
                        <div class="stat-card-content">
                            <div class="stat-card-value" data-target="<?= $stats['thisMonth']; ?>">0</div>
                            <div class="stat-card-label">New This Month</div>
                            <div class="stat-card-trend">
                                <span class="trend-up">↗</span> <?= $stats['total'] > 0 ? round(($stats['thisMonth'] / $stats['total']) * 100, 1) : 0; ?>% of total
                            </div>
                        </div>
                    </div>
                    <div class="stat-card stat-card-info">
                        <div class="stat-card-icon">👥</div>
                        <div class="stat-card-content">
                            <div class="stat-card-value" data-target="<?= $stats['total_membres']; ?>">0</div>
                            <div class="stat-card-label">Total Members</div>
                            <div class="stat-card-trend">
                                <span class="trend-up">↗</span> <?= $stats['avg_membres']; ?> on average
                            </div>
                        </div>
                    </div>
                    <div class="stat-card stat-card-warning">
                        <div class="stat-card-icon">📈</div>
                        <div class="stat-card-content">
                            <div class="stat-card-value" data-target="<?= $stats['utilization_rate']; ?>">0</div>
                            <div class="stat-card-label">Utilization Rate</div>
                            <div class="stat-card-trend">
                                <span class="trend-up">↗</span> <?= $stats['total_membres']; ?>/<?= $stats['max_capacity']; ?> slots
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistiques par statut -->
                <div class="statistics-section">
                    <h3>📋 Distribution by Status</h3>
                    <div class="statistics-grid">
                        <div class="stat-item-enhanced">
                            <div class="stat-item-label">Open</div>
                            <div class="stat-item-value" data-value="<?= $stats['ouvert']; ?>" data-max="<?= $stats['total']; ?>">
                                <?= $stats['ouvert']; ?>
                            </div>
                        </div>
                        <div class="stat-item-enhanced">
                            <div class="stat-item-label">In Progress</div>
                            <div class="stat-item-value" data-value="<?= $stats['en_cours']; ?>" data-max="<?= $stats['total']; ?>">
                                <?= $stats['en_cours']; ?>
                            </div>
                        </div>
                        <div class="stat-item-enhanced">
                            <div class="stat-item-label">Closed</div>
                            <div class="stat-item-value" data-value="<?= $stats['ferme']; ?>" data-max="<?= $stats['total']; ?>">
                                <?= $stats['ferme']; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Résumé -->
                <div class="statistics-summary">
                    <h3>📊 Summary</h3>
                    <div class="summary-grid">
                        <div class="summary-item">
                            <span class="summary-label">Open Collaborations</span>
                            <span class="summary-value"><?= $stats['ouvert']; ?></span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">New This Week</span>
                            <span class="summary-value"><?= $stats['thisWeek']; ?></span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">New This Month</span>
                            <span class="summary-value"><?= $stats['thisMonth']; ?></span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Members per Collab (average)</span>
                            <span class="summary-value"><?= $stats['avg_membres']; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Modal des statistiques */
        .statistics-modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.95);
            backdrop-filter: blur(20px);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            animation: fadeInModal 0.4s ease;
        }

        @keyframes fadeInModal {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .statistics-modal-content {
            background: linear-gradient(135deg, rgba(20, 10, 50, 0.98), rgba(10, 5, 30, 0.99));
            border: 2px solid rgba(255, 0, 199, 0.5);
            border-radius: 24px;
            max-width: 900px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.9), 0 0 60px rgba(255, 0, 199, 0.4);
            animation: slideUpScale 0.5s ease;
        }

        @keyframes slideUpScale {
            from {
                transform: translateY(50px) scale(0.9);
                opacity: 0;
            }
            to {
                transform: translateY(0) scale(1);
                opacity: 1;
            }
        }

        .statistics-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 30px;
            border-bottom: 1px solid rgba(255, 0, 199, 0.3);
        }

        .statistics-modal-header h2 {
            color: #ff00c7;
            margin: 0;
            font-size: 2rem;
            text-shadow: 0 0 20px rgba(255, 0, 199, 0.5);
        }

        .statistics-subtitle {
            color: #b8a8d9;
            margin: 5px 0 0 0;
            font-size: 0.9rem;
        }

        .statistics-modal-close {
            background: rgba(255, 51, 92, 0.2);
            border: 2px solid rgba(255, 51, 92, 0.5);
            color: #ff335c;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            font-size: 24px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .statistics-modal-close:hover {
            background: rgba(255, 51, 92, 0.4);
            transform: rotate(90deg);
        }

        .statistics-modal-body {
            padding: 30px;
        }

        .statistics-grid-main {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(0, 0, 0, 0.5);
            border: 2px solid;
            border-radius: 15px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        .stat-card-primary {
            border-color: rgba(255, 0, 199, 0.5);
        }

        .stat-card-success {
            border-color: rgba(0, 255, 234, 0.5);
        }

        .stat-card-info {
            border-color: rgba(0, 132, 255, 0.5);
        }

        .stat-card-warning {
            border-color: rgba(255, 200, 0, 0.5);
        }

        .stat-card-icon {
            font-size: 2.5rem;
        }

        .stat-card-content {
            flex: 1;
        }

        .stat-card-value {
            font-size: 2rem;
            font-weight: bold;
            color: #fff;
            margin-bottom: 5px;
        }

        .stat-card-label {
            color: #b8a8d9;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }

        .stat-card-trend {
            color: #00ffea;
            font-size: 0.85rem;
        }

        .trend-up {
            color: #00ff88;
        }

        .statistics-section {
            margin-bottom: 30px;
        }

        .statistics-section h3 {
            color: #ff00c7;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }

        .statistics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }

        .stat-item-enhanced {
            background: rgba(0, 0, 0, 0.5);
            border: 2px solid rgba(255, 0, 199, 0.3);
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .stat-item-enhanced:hover {
            border-color: rgba(255, 0, 199, 0.6);
            transform: scale(1.05);
        }

        .stat-item-label {
            color: #b8a8d9;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        .stat-item-value {
            font-size: 2rem;
            font-weight: bold;
            color: #ff00c7;
        }

        .statistics-summary {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 15px;
            padding: 25px;
            border: 1px solid rgba(255, 0, 199, 0.3);
        }

        .statistics-summary h3 {
            color: #ff00c7;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
        }

        .summary-label {
            color: #b8a8d9;
            font-size: 0.9rem;
        }

        .summary-value {
            color: #00ffea;
            font-weight: bold;
            font-size: 1.2rem;
        }

        /* Scrollbar personnalisée */
        .statistics-modal-content::-webkit-scrollbar {
            width: 10px;
        }

        .statistics-modal-content::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 10px;
        }

        .statistics-modal-content::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #ff00c7, #00ffea);
            border-radius: 10px;
        }
    </style>

    <script>
        // Gestion de la modale des statistiques
        document.addEventListener('DOMContentLoaded', function() {
            const statisticsBtn = document.getElementById('statisticsBtn');
            const statisticsModal = document.getElementById('statisticsModal');
            const closeBtn = document.getElementById('closeStatisticsBtn');
            
            function animateValue(element, start, end, duration) {
                const range = end - start;
                const increment = end > start ? 1 : -1;
                const stepTime = Math.abs(Math.floor(duration / range));
                let current = start;
                const timer = setInterval(() => {
                    current += increment;
                    if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
                        element.textContent = end;
                        clearInterval(timer);
                    } else {
                        element.textContent = current;
                    }
                }, stepTime);
            }
            
            function openModal() {
                if (statisticsModal) {
                    statisticsModal.style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                    
                    // Animer les valeurs
                    setTimeout(() => {
                        document.querySelectorAll('.stat-card-value').forEach((el, index) => {
                            setTimeout(() => {
                                const target = parseFloat(el.getAttribute('data-target')) || 0;
                                animateValue(el, 0, target, 1500);
                            }, index * 100);
                        });
                    }, 300);
                }
            }
            
            function closeModal() {
                if (statisticsModal) {
                    statisticsModal.style.display = 'none';
                    document.body.style.overflow = '';
                }
            }
            
            if (statisticsBtn) {
                statisticsBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    openModal();
                });
            }
            
            if (closeBtn) {
                closeBtn.addEventListener('click', closeModal);
            }
            
            // Fermer en cliquant en dehors
            if (statisticsModal) {
                statisticsModal.addEventListener('click', function(e) {
                    if (e.target === statisticsModal) {
                        closeModal();
                    }
                });
            }
            
            // Fermer avec Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && statisticsModal && statisticsModal.style.display === 'flex') {
                    closeModal();
                }
            });
        });
    </script>
</body>
</html>

