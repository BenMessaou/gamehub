
<?php
require_once __DIR__ . '/../model/config.php';
require_once __DIR__ . '/../model/auto_migration.php';

// Auto-migration si nécessaire
$migrationResult = runAutoMigration($conn);

// --- Paramètres de tri/filtre (sécurisés)
$allowedSort = ['created_at','rating','pseudo','game','status','email'];
$sort = isset($_GET['sort']) && in_array($_GET['sort'], $allowedSort) ? $_GET['sort'] : 'created_at';
$order = isset($_GET['order']) && strtolower($_GET['order']) === 'asc' ? 'ASC' : 'DESC';
$filter_game = isset($_GET['filter_game']) ? trim($_GET['filter_game']) : '';
$filter_rating = isset($_GET['filter_rating']) && is_numeric($_GET['filter_rating']) ? (int)$_GET['filter_rating'] : 0;
$filter_status = isset($_GET['filter_status']) && in_array($_GET['filter_status'], ['pending', 'approved', 'rejected']) ? $_GET['filter_status'] : '';
$filter_email = isset($_GET['filter_email']) ? trim($_GET['filter_email']) : '';

// Construction de la requête feedback avec WHERE sécurisé
$where = [];
$params = [];
$types = '';
if ($filter_game !== '') {
  $where[] = 'game LIKE ?';
  $params[] = "%" . $filter_game . "%";
  $types .= 's';
}
if ($filter_rating > 0 && $filter_rating <= 5) {
  $where[] = 'rating = ?';
  $params[] = $filter_rating;
  $types .= 'i';
}
if ($filter_status !== '') {
  $where[] = 'status = ?';
  $params[] = $filter_status;
  $types .= 's';
}
if ($filter_email !== '') {
  $where[] = 'email LIKE ?';
  $params[] = "%" . $filter_email . "%";
  $types .= 's';
}

$sqlFeedback = 'SELECT * FROM feedback' . (count($where) ? ' WHERE ' . implode(' AND ', $where) : '') . " ORDER BY `" . $sort . "` " . $order;
$stmt = $conn->prepare($sqlFeedback);
if ($stmt) {
  if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
  }
  $stmt->execute();
  $res = $stmt->get_result();
  $feedbacks = $res->fetch_all(MYSQLI_ASSOC);
  $stmt->close();
} else {
  $feedbacks = [];
}

// Récupération Messages Contact (simple liste)
$sqlContact = "SELECT * FROM contact ORDER BY created_at DESC";
$contacts = $conn->query($sqlContact)->fetch_all(MYSQLI_ASSOC);

// --- Statistiques
$stats = [];
$res = $conn->query("SELECT COUNT(*) AS total, ROUND(AVG(rating),2) AS avg_rating FROM feedback");
if ($res) $stats = $res->fetch_assoc();

// Statistiques par statut
$statusStats = [];
$resStatus = @$conn->query("SELECT status, COUNT(*) AS cnt FROM feedback GROUP BY status");
if ($resStatus) $statusStats = $resStatus->fetch_all(MYSQLI_ASSOC);
else $statusStats = [];

$res7 = $conn->query("SELECT COUNT(*) AS last7 FROM feedback WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
if ($res7) $s7 = $res7->fetch_assoc(); else $s7 = ['last7'=>0];

$res30 = $conn->query("SELECT COUNT(*) AS last30 FROM feedback WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
if ($res30) $s30 = $res30->fetch_assoc(); else $s30 = ['last30'=>0];

// Top jeux
$topGamesRes = $conn->query("SELECT game, COUNT(*) AS cnt FROM feedback GROUP BY game ORDER BY cnt DESC LIMIT 10");
$topGames = $topGamesRes ? $topGamesRes->fetch_all(MYSQLI_ASSOC) : [];

// Récurrences (pseudos et emails)
$dupPseudosRes = $conn->query("SELECT pseudo, COUNT(*) AS cnt FROM feedback GROUP BY pseudo HAVING cnt > 1 ORDER BY cnt DESC");
$dupPseudos = $dupPseudosRes ? $dupPseudosRes->fetch_all(MYSQLI_ASSOC) : [];

$dupEmailsRes = @$conn->query("SELECT email, COUNT(*) AS cnt FROM feedback WHERE email IS NOT NULL AND email != '' GROUP BY email HAVING cnt > 1 ORDER BY cnt DESC");
$dupEmails = $dupEmailsRes ? $dupEmailsRes->fetch_all(MYSQLI_ASSOC) : [];

$dupContactEmailsRes = @$conn->query("SELECT email, COUNT(*) AS cnt FROM contact GROUP BY email HAVING cnt > 1 ORDER BY cnt DESC");
$dupContactEmails = $dupContactEmailsRes ? $dupContactEmailsRes->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Admin - Feedback Games</title>
<link rel="stylesheet" href="public/assets/style.css">
<link rel="stylesheet" href="frontoffice/index.css">
<style>
  .admin-container { max-width: 1400px; margin: 0 auto; padding: 20px; padding-top: 40px; }
  
  .admin-header-section { background: linear-gradient(135deg, #00ff88 0%, #00ccff 100%); padding: 30px; border-radius: 10px; margin-bottom: 30px; box-shadow: 0 8px 32px rgba(0,255,136,0.2); }
  .admin-header-section h1 { color: #000; font-size: 2.5em; margin-bottom: 10px; }
  .admin-header-section p { color: #333; font-size: 1.1em; }
  
  .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
  .stat-card { background: #1a1f3a; padding: 20px; border-radius: 8px; border-left: 4px solid #00ff88; box-shadow: 0 4px 15px rgba(0,0,0,0.3); }
  .stat-card h3 { color: #00ff88; font-size: 0.9em; text-transform: uppercase; margin-bottom: 10px; }
  .stat-card .value { font-size: 2.2em; font-weight: bold; color: #00ff88; }
  
  .filters-section { background: #1a1f3a; padding: 20px; border-radius: 8px; margin-bottom: 30px; border: 1px solid #00ff88; }
  .filters-section h3 { color: #00ff88; margin-bottom: 15px; font-size: 1.2em; }
  .filter-row { display: flex; flex-wrap: wrap; gap: 15px; align-items: center; }
  .filter-row label { display: flex; flex-direction: column; gap: 5px; }
  .filter-row label span { font-size: 0.9em; color: #00ff88; }
  .filter-row input, .filter-row select { background: #0a0e27; color: #e0e0e0; border: 1px solid #00ff88; padding: 8px 12px; border-radius: 5px; font-size: 0.95em; }
  .filter-row input:focus, .filter-row select:focus { outline: none; border-color: #00ccff; box-shadow: 0 0 10px rgba(0,204,255,0.3); }
  .filter-row button { background: linear-gradient(135deg, #00ff88 0%, #00ccff 100%); color: #000; border: none; padding: 10px 25px; border-radius: 5px; font-weight: bold; cursor: pointer; transition: 0.3s; }
  .filter-row button:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0,255,136,0.4); }
  .filter-row a { color: #00ccff; text-decoration: none; padding: 10px 20px; border: 1px solid #00ccff; border-radius: 5px; transition: 0.3s; }
  .filter-row a:hover { background: #00ccff; color: #000; }
  
  .table-section { background: #1a1f3a; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.3); margin-bottom: 30px; }
  .table-section h2 { color: #00ff88; padding: 20px; border-bottom: 2px solid #00ff88; margin: 0; font-size: 1.5em; }
  .table-wrapper { overflow-x: auto; }
  table { width: 100%; border-collapse: collapse; }
  th { background: #00ff88; color: #000; padding: 15px; text-align: left; font-weight: bold; font-size: 1em; }
  td { padding: 12px 15px; border-bottom: 1px solid #2a2f4a; color: #e0e0e0; font-size: 0.95em; }
  tr:hover { background: #232845; }
  td strong { color: #00ff88; font-weight: bold; }
  
  .status-badge { display: inline-block; padding: 5px 12px; border-radius: 20px; font-size: 0.85em; font-weight: bold; }
  .status-pending { background: #ff9800; color: #000; }
  .status-approved { background: #4caf50; color: #fff; }
  .status-rejected { background: #f44336; color: #fff; }
  
  .action-buttons { display: flex; gap: 8px; flex-wrap: wrap; }
  .btn-small { padding: 8px 14px; border-radius: 4px; border: none; cursor: pointer; font-size: 0.9em; font-weight: bold; transition: 0.3s; text-decoration: none; display: inline-block; }
  .btn-edit { background: #00ccff; color: #000; }
  .btn-edit:hover { background: #00ffff; transform: translateY(-2px); }
  .btn-delete { background: #f44336; color: #fff; }
  .btn-delete:hover { background: #d32f2f; transform: translateY(-2px); }
  .btn-status { background: #9c27b0; color: #fff; padding: 4px 8px; font-size: 0.85em; border-radius: 4px; }
  
  .insight-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 30px; }
  .insight-card { background: #1a1f3a; padding: 20px; border-radius: 8px; border: 1px solid #00ff88; }
  .insight-card h3 { color: #00ff88; margin-bottom: 15px; font-size: 1.2em; }
  .insight-card ul { list-style: none; }
  .insight-card li { padding: 8px 0; border-bottom: 1px solid #2a2f4a; display: flex; justify-content: space-between; align-items: center; color: #e0e0e0; font-size: 0.95em; }
  .insight-card li:last-child { border-bottom: none; }
  .insight-value { color: #00ff88; font-weight: bold; }
</style>
</head>
<body>
  <header>
    <div class="container">
        <h1 class="logo">gamehub</h1>
        <img src="logo.png" class="logo1" alt="">
        <nav>
            <ul>
                <li><a href="frontoffice/index.php" class="super-button">Projects</a></li>
                <li><a href="#deals" class="super-button">Events
                </a></li>
                <li><a href="shop.php" class="super-button">Shop </a></li>
                <li><a href="article/list.php" class="super-button">Article</a></li><li><a class="super-button" href="../index1.php">feedback</a></li>
                <li><a class="super-button" href="backoffice/dashboardmain.html">Dashboard</a></li>
                
            </ul>
        </nav>
    </div>
</header>

  <main>
    <section class="hero">
      <div class="container">
        <h2>🔐 Dashboard Feedback</h2>
        <p>Gérez les avis, contacts et analysez les données en temps réel</p>
      </div><li><a href="avis.php" class="super-button">Avis <span class="arrow">➡️</span></a></li>
    </section>

    <section class="deals">
      <div class="container">
        <div class="container">
    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <h3>Total Avis</h3>
        <div class="value"><?= intval($stats['total'] ?? 0) ?></div>
      </div>
      <div class="stat-card">
        <h3>Note Moyenne</h3>
        <div class="value"><?= htmlspecialchars($stats['avg_rating'] ?? '0') ?>/5</div>
      </div>
      <div class="stat-card">
        <h3>7 Derniers Jours</h3>
        <div class="value"><?= intval($s7['last7'] ?? 0) ?></div>
      </div>
      <div class="stat-card">
        <h3>30 Derniers Jours</h3>
        <div class="value"><?= intval($s30['last30'] ?? 0) ?></div>
      </div>
    </div>

    <!-- Feedback Section -->
    <div class="filters-section">
      <h3>🔍 Filtrer et Trier les Avis</h3>
      <form method="get" class="filter-row">
        <label>
          <span>Jeu</span>
          <input type="text" name="filter_game" placeholder="Nom du jeu..." value="<?= htmlspecialchars($filter_game) ?>">
        </label>
        <label>
          <span>Email</span>
          <input type="email" name="filter_email" placeholder="Email..." value="<?= htmlspecialchars($filter_email) ?>">
        </label>
        <label>
          <span>Note</span>
          <select name="filter_rating">
            <option value="0">Toutes</option>
            <?php for ($r=1;$r<=5;$r++): ?>
              <option value="<?= $r ?>" <?= ($filter_rating==$r)?'selected':'' ?>><?= $r ?> ⭐</option>
            <?php endfor; ?>
          </select>
        </label>
        <label>
          <span>Statut</span>
          <select name="filter_status">
            <option value="">Tous</option>
            <option value="pending" <?= ($filter_status=='pending')?'selected':'' ?>>⏳ En attente</option>
            <option value="approved" <?= ($filter_status=='approved')?'selected':'' ?>>✅ Approuvé</option>
            <option value="rejected" <?= ($filter_status=='rejected')?'selected':'' ?>>❌ Rejeté</option>
          </select>
        </label>
        <label>
          <span>Trier par</span>
          <select name="sort">
            <option value="created_at" <?= ($sort=='created_at')?'selected':'' ?>>Date</option>
            <option value="rating" <?= ($sort=='rating')?'selected':'' ?>>Note</option>
            <option value="pseudo" <?= ($sort=='pseudo')?'selected':'' ?>>Pseudo</option>
            <option value="game" <?= ($sort=='game')?'selected':'' ?>>Jeu</option>
            <option value="status" <?= ($sort=='status')?'selected':'' ?>>Statut</option>
          </select>
        </label>
        <label>
          <span>Ordre</span>
          <select name="order">
            <option value="desc" <?= ($order=='DESC')?'selected':'' ?>>Décroissant</option>
            <option value="asc" <?= ($order=='ASC')?'selected':'' ?>>Croissant</option>
          </select>
        </label>
        <button type="submit">🔎 Appliquer</button>
        <a href="admin.php">↺ Réinitialiser</a>
      </form>
    </div>

    <!-- Feedbacks Table -->
    <div class="usersTablen">
      <h2>📋 Avis des Joueurs</h2>
      <div class="table-wrapper">
        <table>
          <tr>
            <th>ID</th>
            <th>Pseudo</th>
            <th>Email</th>
            <th>Jeu</th>
            <th>Note</th>
            <th>Avis</th>
            <th>Statut</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
          <?php foreach ($feedbacks as $f): ?>
            <tr>
              <td><strong><?= $f['id'] ?></strong></td>
              <td><?= htmlspecialchars($f['pseudo']) ?></td>
              <td><?= htmlspecialchars($f['email'] ?? '') ?></td>
              <td><?= htmlspecialchars($f['game']) ?></td>
              <td>⭐ <?= $f['rating'] ?>/5</td>
              <td><?= htmlspecialchars(substr($f['message'], 0, 50)) ?>...</td>
              <td>
                <span class="status-badge status-<?= $f['status'] ?? 'pending' ?>">
                  <?= ucfirst($f['status'] ?? 'pending') ?>
                </span>
              </td>
              <td><?= $f['created_at'] ?></td>
              <td>
                <div class="action-buttons">
                  <a href="edit_feedback.php?id=<?= $f['id'] ?>" class="btn-small btn-edit">✏️ Modifier</a>
                  <form style="display:inline;" method="POST" action="update_status.php">
                    <input type="hidden" name="id" value="<?= $f['id'] ?>">
                    <select name="status" onchange="this.parentElement.submit();" class="btn-status">
                      <option value="">Statut</option>
                      <option value="pending">⏳ Attente</option>
                      <option value="approved">✅ Approuver</option>
                      <option value="rejected">❌ Rejeter</option>
                    </select>
                  </form>
                  <a href="delete.php?id=<?= $f['id'] ?>" onclick="return confirm('Supprimer ?')" class="btn-small btn-delete">🗑️ Supprimer</a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </table>
      </div>
    </div>

    <!-- Insights Grid -->
    <div class="insight-grid">
      <!-- Top Jeux -->
      <?php if (!empty($topGames)): ?>
        <div class="insight-card">
          <h3>🎮 Top Jeux</h3>
          <ul>
            <?php foreach ($topGames as $tg): ?>
              <li>
                <span><?= htmlspecialchars($tg['game']) ?></span>
                <span class="insight-value"><?= intval($tg['cnt']) ?> avis</span>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <!-- Pseudos Récurrents -->
      <?php if (!empty($dupPseudos)): ?>
        <div class="insight-card">
          <h3>👥 Pseudos Récurrents</h3>
          <ul>
            <?php foreach ($dupPseudos as $d): ?>
              <li>
                <span><?= htmlspecialchars($d['pseudo']) ?></span>
                <span class="insight-value"><?= intval($d['cnt']) ?></span>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <!-- Emails Récurrents (Avis) -->
      <?php if (!empty($dupEmails)): ?>
        <div class="insight-card">
          <h3>📧 Emails Récurrents (Avis)</h3>
          <ul>
            <?php foreach ($dupEmails as $d): ?>
              <li>
                <span><?= htmlspecialchars($d['email']) ?></span>
                <span class="insight-value"><?= intval($d['cnt']) ?></span>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <!-- Emails Récurrents (Contact) -->
      <?php if (!empty($dupContactEmails)): ?>
        <div class="insight-card">
          <h3>💌 Emails Contact Récurrents</h3>
          <ul>
            <?php foreach ($dupContactEmails as $d): ?>
              <li>
                <span><?= htmlspecialchars($d['email']) ?></span>
                <span class="insight-value"><?= intval($d['cnt']) ?></span>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
    </div>

    <!-- Messages Contact Table -->
    <div class="table-section">
      <h2>💬 Messages de Contact</h2>
      <div class="table-wrapper">
        <table>
          <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Email</th>
            <th>Message</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
          <?php foreach ($contacts as $c): ?>
            <tr>
              <td><strong><?= $c['id'] ?></strong></td>
              <td><?= htmlspecialchars($c['name']) ?></td>
              <td><?= htmlspecialchars($c['email']) ?></td>
              <td><?= htmlspecialchars(substr($c['message'], 0, 50)) ?>...</td>
              <td><?= $c['created_at'] ?></td>
              <td>
                <div class="action-buttons">
                  <a href="edit_contact.php?id=<?= $c['id'] ?>" class="btn-small btn-edit">✏️ Modifier</a>
                  <a href="delete_contact.php?id=<?= $c['id'] ?>" onclick="return confirm('Supprimer ?')" class="btn-small btn-delete">🗑️ Supprimer</a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </table>
      </div>
    </div>
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
