<?php
// admin/index.php
// Dashboard simple sans auth (pour démonstration / usage local uniquement)

ini_set('display_errors', 1);
error_reporting(E_ALL);

// charge le model
require_once __DIR__ . '/../app/models/FeedbackModel.php';

// instancie et récupère les avis
$model = new FeedbackModel();
$feedbacks = $model->getAll();
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Admin - Dashboard (simple)</title>
  <link rel="stylesheet" href="/feedback-games/public/assets/style.css">
  <style>
    body{font-family:Arial,Helvetica,sans-serif;background:#0f1724;color:#e6eef8}
    .container{max-width:1100px;margin:30px auto;padding:20px}
    table{width:100%;border-collapse:collapse;background:rgba(255,255,255,0.03)}
    th,td{padding:10px;border-bottom:1px solid rgba(255,255,255,0.05);text-align:left}
    th{background:rgba(255,255,255,0.02)}
    a.btn{display:inline-block;padding:6px 10px;border-radius:6px;background:#00cc88;color:#001; text-decoration:none}
    .topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:12px}
  </style>
</head>
<body>
  <div class="container">
    <div class="topbar">
      <h1>Dashboard Admin (démo)</h1>
      <div>
        <a href="/feedback-games/public/index.php?url=accueil" class="btn">Voir site public</a>
        <a href="/feedback-games/admin/index.php?action=refresh" class="btn">Refresh</a>
      </div>
    </div>

    <p>Total avis : <strong><?= count($feedbacks) ?></strong></p>

    <table>
      <thead>
        <tr><th>ID</th><th>Pseudo</th><th>Jeu</th><th>Note</th><th>Message</th><th>Créé</th><th>Action</th></tr>
      </thead>
      <tbody>
        <?php foreach($feedbacks as $f): ?>
        <tr>
          <td><?= (int)$f['id'] ?></td>
          <td><?= htmlspecialchars($f['pseudo']) ?></td>
          <td><?= htmlspecialchars($f['game']) ?></td>
          <td><?= (int)$f['rating'] ?>/5</td>
          <td><?= nl2br(htmlspecialchars($f['message'])) ?></td>
          <td><?= htmlspecialchars($f['created_at']) ?></td>
          <td>
            <a href="index.php?action=delete&id=<?= (int)$f['id'] ?>" onclick="return confirm('Supprimer cet avis ?')" class="btn">Suppr</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <p style="margin-top:20px;color:#99c">Remarque : suppression immédiate (sans confirmation serveur côté CSRF). Utiliser seulement en local.</p>
  </div>

<?php
// actions simples : delete
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($id > 0) {
        $model->delete($id);
        // redirige proprement pour éviter la ré-exécution
        header('Location: /feedback-games/admin/index.php');
        exit;
    }
}
?>
</body>
</html>
