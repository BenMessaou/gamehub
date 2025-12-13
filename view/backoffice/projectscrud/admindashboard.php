<?php
require_once "admin layout.php";
require_once "../../../controller/ProjectController.php";

$controller = new ProjectController();
$projects = $controller->listProjects();

$published = array_filter($projects, fn($p) => $p['statut'] === 'publie');
$pending = array_filter($projects, fn($p) => $p['statut'] === 'en_attente');
$rejected = array_filter($projects, fn($p) => $p['statut'] === 'rejete');

ob_start();
?>

<h1>Dashboard Admin</h1>

<div class="stats">
    <div class="stat-box">
        <?= count($projects) ?><br><span>Jeux totaux</span>
    </div>
    <div class="stat-box">
        <?= count($published) ?><br><span>Publiés</span>
    </div>
    <div class="stat-box">
        <?= count($pending) ?><br><span>En attente</span>
    </div>
    <div class="stat-box">
        <?= count($rejected) ?><br><span>Rejetés</span>
    </div>
</div>

<h2 style="margin-top:40px;">Derniers jeux envoyés</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Nom</th>
        <th>Statut</th>
        <th>Développeur</th>
        <th>Actions</th>
    </tr>

    <?php foreach (array_slice($projects, 0, 6) as $p): ?>
    <tr>
        <td><?= $p['id'] ?></td>
        <td><?= $p['nom'] ?></td>
        <td><?= $p['statut'] ?></td>
        <td><?= $p['developpeur'] ?></td>
        <td>
            <a href="showproject.php?id=<?= $p['id'] ?>" class="btn btn-primary">Voir</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php
$content = ob_get_clean();
$title = "Dashboard Admin";
require "admin layout.php";
