<?php
// public/admin/index.php
// Dashboard simple (sans authentification)

require_once __DIR__ . '/../../app/models/FeedbackModel.php';

$model = new FeedbackModel();

// action delete ?
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $model->delete($id);
    header('Location: index.php');
    exit;
}

$feedbacks = $model->getAll();
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Admin - Dashboard</title>
  <style>
    body{font-family: Arial; padding:20px}
    table{width:100%; border-collapse:collapse}
    th,td{border:1px solid #999;padding:8px;text-align:left}
    th{background:#f4f4f4}
    a.delete{color:red}
    .top{margin-bottom:1rem}
  </style>
</head>
<body>
  <div class="top">
    <h1>Dashboard Admin (simple)</h1>
    <p><a href="../index.php">Retour front</a></p>
  </div>

  <?php if (empty($feedbacks)): ?>
    <p>Aucun avis pour le moment.</p>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Pseudo</th>
          <th>Jeu</th>
          <th>Note</th>
          <th>Message</th>
          <th>Date</th>
          <th>Action</th>
        </tr>
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
            <td><a class="delete" href="index.php?action=delete&id=<?= (int)$f['id'] ?>" onclick="return confirm('Supprimer ?')">Supprimer</a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</body>
</html>
