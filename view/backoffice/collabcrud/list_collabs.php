<?php
session_start();

require_once "../../controller/controllercollab/CollabProjectController.php";
require_once "../../controller/controllercollab/CollabMemberController.php";

$projectController = new CollabProjectController();
$memberController = new CollabMemberController();

// Récupérer toutes les collaborations ouvertes
$collabs = $projectController->getAllOpen();

// Pour chaque collaboration, récupérer le nombre de membres
foreach ($collabs as &$collab) {
    $collab['current_members'] = $memberController->countMembers($collab['id']);
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Collaborations - GameHub</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 50%, #16213e 100%);
            color: #e8dfff;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        h1 {
            text-align: center;
            color: #ff00c7;
            margin-bottom: 30px;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 20px;
            background: rgba(255, 0, 199, 0.2);
            color: #ff00c7;
            text-decoration: none;
            border-radius: 10px;
            border: 2px solid rgba(255, 0, 199, 0.5);
        }
        .back-link:hover {
            background: rgba(255, 0, 199, 0.3);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(15, 5, 35, 0.95);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
        }
        th {
            background: rgba(255, 0, 199, 0.3);
            padding: 15px;
            text-align: left;
            color: #ff00c7;
            font-weight: 700;
        }
        td {
            padding: 15px;
            border-bottom: 1px solid rgba(255, 0, 199, 0.1);
        }
        tr:hover {
            background: rgba(255, 0, 199, 0.1);
        }
        .btn-view {
            padding: 8px 20px;
            background: rgba(0, 255, 234, 0.2);
            color: #00ffea;
            text-decoration: none;
            border-radius: 8px;
            border: 2px solid rgba(0, 255, 234, 0.5);
            transition: all 0.3s ease;
        }
        .btn-view:hover {
            background: rgba(0, 255, 234, 0.3);
            box-shadow: 0 0 15px rgba(0, 255, 234, 0.5);
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #b8a8d9;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="collaboration.php" class="back-link">← Retour aux Collaborations</a>
        
        <h1>📋 Liste des Collaborations Ouvertes</h1>

        <?php if (empty($collabs)): ?>
            <div class="empty-state">
                <h3>Aucune collaboration disponible</h3>
                <p>Soyez le premier à créer une collaboration !</p>
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Description</th>
                        <th>Statut</th>
                        <th>Membres</th>
                        <th>Date de création</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($collabs as $c): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($c['titre']); ?></strong></td>
                            <td><?php echo htmlspecialchars(substr($c['description'], 0, 100)) . (strlen($c['description']) > 100 ? '...' : ''); ?></td>
                            <td>
                                <span style="padding: 5px 15px; border-radius: 15px; background: rgba(0, 255, 234, 0.2); color: #00ffea; border: 1px solid rgba(0, 255, 234, 0.5);">
                                    <?php echo ucfirst($c['statut']); ?>
                                </span>
                            </td>
                            <td><?php echo $c['current_members']; ?>/<?php echo $c['max_membres']; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($c['date_creation'])); ?></td>
                            <td>
                                <a href="view_collab.php?id=<?php echo $c['id']; ?>" class="btn-view">Voir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
