<?php
// app/views/admin/avis.php
// Variables attendues : $feedbacks (array), $total (int)
// Assure-toi d'inclure le CSS global de ton site dans le layout ou ici

?><!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Admin — Liste des avis</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <!-- adapte le chemin vers ton CSS -->
  <link rel="stylesheet" href="<?= dirname($_SERVER['SCRIPT_NAME']) ?>/assets/style.css">
  <style>
    /* Styles rapides pour le tableau (si tu veux override) */
    .admin-container { max-width:1100px; margin:3rem auto; padding:0 1rem; color:#e6eef8; }
    .admin-header { display:flex; justify-content:space-between; align-items:center; gap:1rem; margin-bottom:1.5rem; }
    .admin-header h1{ margin:0; font-size:2.2rem;}
    .card { background: #0f1724; padding:1.2rem; border-radius:10px; box-shadow: 0 6px 20px rgba(0,0,0,0.5); }
    table.admin-table { width:100%; border-collapse:collapse; color:#e6eef8; }
    table.admin-table th, table.admin-table td { padding:12px 14px; border-bottom:1px solid rgba(255,255,255,0.04); text-align:left; }
    table.admin-table th { background: rgba(255,255,255,0.02); font-weight:600; }
    .btn { display:inline-block; padding:8px 12px; border-radius:8px; background:#00d084; color:#012; text-decoration:none; }
    .muted { color:#9fb0c6; font-size:0.95rem; margin-top:1rem; display:block;}
  </style>
</head>
<body style="background:#071028; font-family:Arial,Helvetica,sans-serif;">
  <div class="admin-container">
    <div class="admin-header">
      <h1>Dashboard Admin — Avis</h1>
      <div>
        <a class="btn" href="<?= dirname($_SERVER['SCRIPT_NAME']) ?>/">Voir site public</a>
        <a class="btn" href="<?= dirname($_SERVER['SCRIPT_NAME']) ?>/admin/index.php?action=dashboard" style="background:#00b399">Refresh</a>
      </div>
    </div>

    <div class="card">
      <p style="margin:0 0 1rem 0;">Total avis : <strong><?= (int)$total ?></strong></p>

      <table class="admin-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Pseudo</th>
            <th>Jeu</th>
            <th>Note</th>
            <th>Message</th>
            <th>Créé</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
        <?php if (empty($feedbacks)): ?>
          <tr><td colspan="7" style="text-align:center; padding:2rem;">Aucun avis pour le moment.</td></tr>
        <?php else: ?>
          <?php foreach($feedbacks as $f): ?>
            <tr>
              <td><?= (int)$f['id'] ?></td>
              <td><?= htmlspecialchars($f['pseudo']) ?></td>
              <td><?= htmlspecialchars($f['game']) ?></td>
              <td><?= (int)$f['rating'] ?>/5</td>
              <td><?= nl2br(htmlspecialchars($f['message'])) ?></td>
              <td><?= htmlspecialchars($f['created_at']) ?></td>
              <td>
                <!-- lien suppression simple (GET) — en local uniquement -->
                <a class="btn" style="background:#ff5a5f;" href="<?= dirname($_SERVER['SCRIPT_NAME']) ?>/admin/index.php?action=delete&id=<?= (int)$f['id'] ?>" onclick="return confirm('Supprimer cet avis ?')">Suppr</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
      </table>

      <span class="muted">Remarque : suppression immédiate. Utiliser en local seulement.</span>
    </div>
  </div>
</body>
</html>
