<?php
session_start();

// Mode développeur : permettre l'accès même sans connexion
$isLoggedIn = isset($_SESSION['user_id']);

require_once __DIR__ . "/../../../controller/controllercollab/MessageModerationController.php";
require_once __DIR__ . "/../../../config/config.php";

$moderationController = new MessageModerationController();
$db = config::getConnexion();

// Récupérer les statistiques
$stats = $moderationController->getModerationStats();
$collabId = isset($_GET['collab_id']) ? intval($_GET['collab_id']) : null;
$collabStats = $collabId ? $moderationController->getModerationStats($collabId) : null;

// Récupérer les logs récents
try {
    $sql = "SELECT * FROM message_moderation_logs 
            ORDER BY created_at DESC 
            LIMIT 50";
    if ($collabId) {
        $sql = "SELECT * FROM message_moderation_logs 
                WHERE collab_id = ? 
                ORDER BY created_at DESC 
                LIMIT 50";
        $stmt = $db->prepare($sql);
        $stmt->execute([$collabId]);
    } else {
        $stmt = $db->prepare($sql);
        $stmt->execute();
    }
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $logs = [];
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Modération - GameHub Pro</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 50%, #16213e 100%);
            color: #fff;
            min-height: 100vh;
            padding: 2rem;
        }
        
        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .dashboard-header {
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 2px solid rgba(0, 255, 136, 0.3);
        }
        
        .dashboard-header h1 {
            color: #00ff88;
            margin-bottom: 1rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: rgba(0, 0, 0, 0.6);
            border-radius: 10px;
            padding: 1.5rem;
            border: 2px solid rgba(0, 255, 136, 0.2);
        }
        
        .stat-card h3 {
            color: #00ffea;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
        }
        
        .stat-card .value {
            color: #00ff88;
            font-size: 2rem;
            font-weight: 700;
        }
        
        .logs-section {
            background: rgba(0, 0, 0, 0.8);
            border-radius: 15px;
            padding: 2rem;
            border: 2px solid rgba(0, 255, 136, 0.3);
        }
        
        .logs-section h2 {
            color: #00ff88;
            margin-bottom: 1.5rem;
        }
        
        .log-item {
            background: rgba(0, 0, 0, 0.4);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            border-left: 4px solid;
        }
        
        .log-item.blocked {
            border-left-color: #ff335c;
        }
        
        .log-item.approved {
            border-left-color: #00ff88;
        }
        
        .log-item.level1 {
            background: rgba(255, 51, 92, 0.1);
        }
        
        .log-item.level2 {
            background: rgba(255, 170, 0, 0.1);
        }
        
        .log-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }
        
        .log-message {
            color: #aaa;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        
        .log-scores {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 0.5rem;
        }
        
        .score-item {
            padding: 0.25rem 0.75rem;
            background: rgba(0, 255, 136, 0.1);
            border-radius: 15px;
            font-size: 0.75rem;
        }
        
        .score-item.high {
            background: rgba(255, 51, 92, 0.2);
            color: #ff335c;
        }
        
        .back-link {
            display: inline-block;
            margin-bottom: 1rem;
            color: #00ff88;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border: 2px solid rgba(0, 255, 136, 0.5);
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .back-link:hover {
            background: rgba(0, 255, 136, 0.2);
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <a href="view_collab.php<?php echo $collabId ? '?id=' . $collabId : ''; ?>" class="back-link">← Retour</a>
        
        <div class="dashboard-header">
            <h1>🛡️ Dashboard de Modération</h1>
            <?php if ($collabId): ?>
                <p>Collaboration ID: <?php echo $collabId; ?></p>
            <?php endif; ?>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Messages</h3>
                <div class="value"><?php echo $stats['total'] ?? 0; ?></div>
            </div>
            <div class="stat-card">
                <h3>Messages Bloqués</h3>
                <div class="value" style="color: #ff335c;"><?php echo $stats['blocked'] ?? 0; ?></div>
            </div>
            <div class="stat-card">
                <h3>Niveau 1 (Filtre)</h3>
                <div class="value" style="color: #ffaa00;"><?php echo $stats['level1'] ?? 0; ?></div>
            </div>
            <div class="stat-card">
                <h3>Niveau 2 (IA)</h3>
                <div class="value" style="color: #ffaa00;"><?php echo $stats['level2'] ?? 0; ?></div>
            </div>
        </div>
        
        <div class="logs-section">
            <h2>📋 Logs de Modération</h2>
            
            <?php if (empty($logs)): ?>
                <p style="text-align: center; color: #888; padding: 2rem;">Aucun log de modération pour le moment.</p>
            <?php else: ?>
                <?php foreach ($logs as $log): 
                    $result = json_decode($log['moderation_result'], true);
                    $scores = json_decode($log['scores'] ?? '{}', true);
                    $isBlocked = $result['blocked'] ?? false;
                    $level = $result['level'] ?? 0;
                ?>
                    <div class="log-item <?php echo $isBlocked ? 'blocked level' . $level : 'approved'; ?>">
                        <div class="log-header">
                            <span><strong>User ID:</strong> <?php echo $log['user_id']; ?> | <strong>Collab ID:</strong> <?php echo $log['collab_id']; ?></span>
                            <span style="color: #888; font-size: 0.85rem;"><?php echo date('d/m/Y H:i', strtotime($log['created_at'])); ?></span>
                        </div>
                        <div class="log-message">
                            <strong>Message:</strong> <?php echo htmlspecialchars(substr($log['message'], 0, 100)); ?><?php echo strlen($log['message']) > 100 ? '...' : ''; ?>
                        </div>
                        <div style="color: <?php echo $isBlocked ? '#ff335c' : '#00ff88'; ?>; font-weight: 600;">
                            <?php echo $isBlocked ? '🚫 BLOQUÉ' : '✅ APPROUVÉ'; ?> 
                            <?php if ($isBlocked): ?>
                                (Niveau <?php echo $level; ?>)
                            <?php endif; ?>
                        </div>
                        <?php if ($isBlocked && !empty($result['reason'])): ?>
                            <div style="color: #ffaa00; margin-top: 0.5rem; font-size: 0.85rem;">
                                <strong>Raison:</strong> <?php echo htmlspecialchars($result['reason']); ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($scores)): ?>
                            <div class="log-scores">
                                <?php foreach ($scores as $category => $score): 
                                    $isHigh = $score >= 0.7;
                                ?>
                                    <div class="score-item <?php echo $isHigh ? 'high' : ''; ?>">
                                        <?php echo ucfirst($category); ?>: <?php echo number_format($score, 2); ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

