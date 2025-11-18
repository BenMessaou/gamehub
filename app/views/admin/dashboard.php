<?php
// app/views/admin/dashboard.php
// $feedbacks attendu : tableau d'avis
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Dashboard Admin - Avis</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <!-- Lier ton style global (copie selon ton arborescence) -->
  <link rel="stylesheet" href="/feedback-games/style.css">
  <style>
    /* styles rapides pour le tableau admin (optionnel) */
    .admin-wrap { max-width:1100px; margin:40px auto; color:#fff; font-family:Arial, sans-serif; }
    .admin-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
    .admin-table { width:100%; border-collapse:collapse; background:#0e1116; }
    .admin-table th, .admin-table td { padding:12px 14px; border-bottom:1px solid rgba(255,255,255,0.04); }
    .admin-table th { text-align:left; background:#0b0d11; }
    .btn { display:inline-block; padding:8px 12px; border-radius:8px; background:#00c789; color:#003; text-decoration:none; }
    .danger { background:#ff5c5c; color:#fff; }
    .note { color:#9aa; margin-top:10px; font-size:0.95rem;}
  </style>
</head>
<body style="background:#0b0f18;">
  <div class="admin-wrap">
    <div class="admin-header">
      <h1>Dashboard Admin (démonstration)</h1>
      <div>
        <a class="btn" href="/feedback-games/index.php">Voir site public</a>
        <a class="btn" href="/feedback-games/admin/index.php?action=refresh">Refresh</a>
      </div>
    </div>

    <p>Total avis : <?= count($feedbacks) ?></p>

    <table class="admin-table" role="table" aria-label="Liste des avis">
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
          <tr><td colspan="7" style="padding:16px; color:#ccc;">Aucun avis pour le moment.</td></tr>
        <?php else: ?>
          <?php foreach ($feedbacks as $f): ?>
            <tr>
              <td><?= (int)$f['id'] ?></td>
              <td><?= htmlspecialchars($f['pseudo']) ?></td>
              <td><?= htmlspecialchars($f['game']) ?></td>
              <td><?= (int)$f['rating'] ?>/5</td>
              <td><?= nl2br(htmlspecialchars($f['message'])) ?></td>
              <td><?= htmlspecialchars($f['created_at']) ?></td>
              <td>
                <form method="post" action="/feedback-games/admin/index.php?action=delete" style="display:inline">
                  <input type="hidden" name="id" value="<?= (int)$f['id'] ?>">
                  <button class="btn danger" type="submit" onclick="return confirm('Supprimer cet avis ?')">Suppr</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>

    <p class="note">Remarque : suppression immédiate (utilise seulement en local).</p>
  </div>
</body>
</html>
