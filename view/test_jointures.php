<?php
/**
 * PAGE DE TEST - Jointures Contact & Feedback
 * Testez les différentes requêtes JOIN ici
 */

require_once __DIR__ . '/../model/config.php';
require_once __DIR__ . '/../model/setup_jointure.php';
require_once __DIR__ . '/../model/auto_migration.php';

// Auto-migration
runAutoMigration($conn);
setupForeignKey($conn);

// Récupérer le type de requête depuis l'URL
$query_type = isset($_GET['query']) ? $_GET['query'] : 'overview';
$results = [];
$query_sql = '';

// Exécuter les différentes requêtes
switch ($query_type) {
    case 'all_feedbacks':
        $query_sql = "
            SELECT 
                f.id, f.pseudo, f.email, f.game, f.rating, f.message, f.status, f.created_at,
                c.id as contact_id, c.name as contact_name, c.status as contact_status
            FROM feedback f
            LEFT JOIN contact c ON f.email = c.email
            ORDER BY f.created_at DESC
            LIMIT 20
        ";
        break;
        
    case 'contacts_with_avis':
        $query_sql = "
            SELECT 
                c.id, c.name, c.email, c.status,
                COUNT(f.id) as nombre_avis,
                GROUP_CONCAT(DISTINCT f.game SEPARATOR ', ') as jeux,
                ROUND(AVG(f.rating), 2) as note_moyenne
            FROM contact c
            LEFT JOIN feedback f ON f.email = c.email
            GROUP BY c.id, c.name, c.email, c.status
            ORDER BY nombre_avis DESC
        ";
        break;
        
    case 'users_with_both':
        $query_sql = "
            SELECT 
                c.id, c.name, c.email,
                COUNT(DISTINCT f.id) as nombre_avis,
                COUNT(CASE WHEN f.status = 'approved' THEN 1 END) as avis_approuves,
                COUNT(CASE WHEN f.status = 'pending' THEN 1 END) as avis_attente,
                COUNT(CASE WHEN f.status = 'rejected' THEN 1 END) as avis_rejetes
            FROM contact c
            INNER JOIN feedback f ON f.email = c.email
            GROUP BY c.id, c.name, c.email
            ORDER BY nombre_avis DESC
        ";
        break;
        
    case 'feedbacks_no_contact':
        $query_sql = "
            SELECT 
                f.id, f.pseudo, f.email, f.game, f.rating, f.status, f.created_at
            FROM feedback f
            LEFT JOIN contact c ON f.email = c.email
            WHERE c.id IS NULL
            ORDER BY f.created_at DESC
        ";
        break;
        
    case 'contacts_no_feedback':
        $query_sql = "
            SELECT 
                c.id, c.name, c.email, c.status, c.created_at
            FROM contact c
            LEFT JOIN feedback f ON f.email = c.email
            WHERE f.id IS NULL
            ORDER BY c.created_at DESC
        ";
        break;
        
    case 'stats':
        $query_sql = "
            SELECT 
                COUNT(DISTINCT c.id) as nb_contacts,
                COUNT(DISTINCT f.id) as nb_avis,
                COUNT(DISTINCT f.email) as emails_uniques,
                ROUND(AVG(f.rating), 2) as note_moyenne,
                COUNT(CASE WHEN c.id IS NOT NULL THEN 1 END) as avis_avec_contact,
                COUNT(CASE WHEN c.id IS NULL THEN 1 END) as avis_sans_contact
            FROM (SELECT * FROM feedback) f
            LEFT JOIN (SELECT * FROM contact) c ON f.email = c.email
        ";
        break;
        
    default: // overview
        $query_sql = "
            SELECT 
                (SELECT COUNT(*) FROM contact) as nb_contacts,
                (SELECT COUNT(*) FROM feedback) as nb_avis,
                (SELECT COUNT(DISTINCT email) FROM feedback) as emails_feedbacks,
                (SELECT COUNT(DISTINCT email) FROM contact) as emails_contacts
        ";
}

// Exécuter la requête
if (!empty($query_sql)) {
    $result = $conn->query($query_sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $results[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Jointures - Feedback Games</title>
    <link rel="stylesheet" href="/feeeed_backkkkkkkkk/public/assets/style.css">
    <style>
        body { font-family: Arial; background: #0a0e27; color: #e0e0e0; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { background: linear-gradient(135deg, #00ff88 0%, #00ccff 100%); padding: 30px; border-radius: 10px; margin-bottom: 30px; }
        .header h1 { color: #000; }
        .header p { color: #333; }
        
        .query-buttons { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 30px; }
        .query-btn { padding: 12px 20px; background: #00ff88; color: #000; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; transition: 0.3s; }
        .query-btn:hover { background: #00ccff; }
        .query-btn.active { background: #ff9800; }
        
        .results-card { background: #1a1f3a; padding: 20px; border-radius: 8px; border: 1px solid #00ff88; margin-bottom: 20px; }
        .results-card h3 { color: #00ff88; margin-bottom: 15px; }
        
        .sql-query { background: #0a0e27; padding: 15px; border-radius: 5px; border-left: 3px solid #00ccff; margin-bottom: 15px; font-family: monospace; font-size: 0.9em; overflow-x: auto; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #00ff88; color: #000; padding: 12px; text-align: left; font-weight: bold; }
        td { padding: 10px 12px; border-bottom: 1px solid #2a2f4a; }
        tr:hover { background: #232845; }
        
        .stat-row { display: flex; gap: 20px; margin-top: 15px; flex-wrap: wrap; }
        .stat { background: #0a0e27; padding: 15px; border-radius: 5px; border-left: 3px solid #00ff88; min-width: 150px; }
        .stat-label { color: #00ff88; font-size: 0.9em; }
        .stat-value { font-size: 1.8em; font-weight: bold; }
        
        .info-box { background: #1a1f3a; padding: 15px; border-radius: 5px; border: 1px solid #00ccff; margin-bottom: 20px; }
        .info-box strong { color: #00ff88; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔗 Test des Jointures</h1>
            <p>Contact ↔ Feedback - Testez différentes requêtes JOIN</p>
        </div>

        <div class="info-box">
            <strong>ℹ️ Comment ça marche :</strong><br>
            Les jointures relient les tables <strong>contact</strong> et <strong>feedback</strong> par l'email. 
            Sélectionnez une requête ci-dessous pour voir les résultats.
        </div>

        <div class="query-buttons">
            <a href="?query=overview" class="query-btn <?= ($query_type === 'overview') ? 'active' : '' ?>">📊 Vue d'ensemble</a>
            <a href="?query=all_feedbacks" class="query-btn <?= ($query_type === 'all_feedbacks') ? 'active' : '' ?>">📝 Tous les Avis + Contact</a>
            <a href="?query=contacts_with_avis" class="query-btn <?= ($query_type === 'contacts_with_avis') ? 'active' : '' ?>">👥 Contacts avec Avis</a>
            <a href="?query=users_with_both" class="query-btn <?= ($query_type === 'users_with_both') ? 'active' : '' ?>">🔗 Utilisateurs (Contact + Avis)</a>
            <a href="?query=feedbacks_no_contact" class="query-btn <?= ($query_type === 'feedbacks_no_contact') ? 'active' : '' ?>">❌ Avis sans Contact</a>
            <a href="?query=contacts_no_feedback" class="query-btn <?= ($query_type === 'contacts_no_feedback') ? 'active' : '' ?>">❌ Contacts sans Avis</a>
            <a href="?query=stats" class="query-btn <?= ($query_type === 'stats') ? 'active' : '' ?>">📈 Statistiques</a>
        </div>

        <div class="results-card">
            <h3>Requête SQL</h3>
            <div class="sql-query"><?= htmlspecialchars($query_sql) ?></div>

            <?php if ($query_type === 'overview'): ?>
                <h3>Résumé de la Base de Données</h3>
                <?php if (!empty($results)): ?>
                    <div class="stat-row">
                        <div class="stat">
                            <div class="stat-label">Contacts</div>
                            <div class="stat-value"><?= $results[0]['nb_contacts'] ?></div>
                        </div>
                        <div class="stat">
                            <div class="stat-label">Avis</div>
                            <div class="stat-value"><?= $results[0]['nb_avis'] ?></div>
                        </div>
                        <div class="stat">
                            <div class="stat-label">Emails (Feedbacks)</div>
                            <div class="stat-value"><?= $results[0]['emails_feedbacks'] ?></div>
                        </div>
                        <div class="stat">
                            <div class="stat-label">Emails (Contacts)</div>
                            <div class="stat-value"><?= $results[0]['emails_contacts'] ?></div>
                        </div>
                    </div>
                <?php endif; ?>

            <?php elseif ($query_type === 'stats'): ?>
                <h3>Statistiques Combinées</h3>
                <?php if (!empty($results)): ?>
                    <div class="stat-row">
                        <div class="stat">
                            <div class="stat-label">Contacts</div>
                            <div class="stat-value"><?= $results[0]['nb_contacts'] ?></div>
                        </div>
                        <div class="stat">
                            <div class="stat-label">Avis Total</div>
                            <div class="stat-value"><?= $results[0]['nb_avis'] ?></div>
                        </div>
                        <div class="stat">
                            <div class="stat-label">Emails Uniques</div>
                            <div class="stat-value"><?= $results[0]['emails_uniques'] ?></div>
                        </div>
                        <div class="stat">
                            <div class="stat-label">Note Moyenne</div>
                            <div class="stat-value"><?= $results[0]['note_moyenne'] ?>/5</div>
                        </div>
                        <div class="stat">
                            <div class="stat-label">Avis avec Contact</div>
                            <div class="stat-value"><?= $results[0]['avis_avec_contact'] ?></div>
                        </div>
                        <div class="stat">
                            <div class="stat-label">Avis sans Contact</div>
                            <div class="stat-value"><?= $results[0]['avis_sans_contact'] ?></div>
                        </div>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <h3>Résultats (<?= count($results) ?> lignes)</h3>
                <?php if (!empty($results)): ?>
                    <table>
                        <thead>
                            <tr>
                                <?php 
                                $firstRow = $results[0];
                                foreach (array_keys($firstRow) as $key): 
                                ?>
                                    <th><?= htmlspecialchars($key) ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($results as $row): ?>
                                <tr>
                                    <?php foreach ($row as $value): ?>
                                        <td><?= htmlspecialchars($value ?? '-') ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="color: #999;">Aucun résultat trouvé.</p>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <div class="info-box">
            <strong>📖 Explication :</strong><br>
            <ul style="margin-top: 10px;">
                <li><strong>LEFT JOIN :</strong> Tous les avis + info contact si existe</li>
                <li><strong>INNER JOIN :</strong> Seulement avis avec contact</li>
                <li><strong>WHERE c.id IS NULL :</strong> Avis sans contact correspondant</li>
                <li><strong>WHERE f.id IS NULL :</strong> Contacts sans avis</li>
            </ul>
        </div>
    </div>
</body>
</html>
