<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../controllers/OrderController.php';

$controller = new OrderController($pdo);
$orderId = $_GET['id'];
$items = $controller->details($orderId);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails commande - GameHub Admin</title>
    <link rel="stylesheet" href="../style.css?v=9999">
</head>

<body>

<header>
    <div class="container header-flex">

        <div class="logo">
            <img src="../logo.png" alt="logo">
            <span>GameHub Admin</span>
        </div>

        <nav class="nav-links">
            <a href="shop.php" class="super-button">Voir le site</a>
            <a href="backoffice.php" class="super-button">Jeux</a>
            <a href="orders.php" class="super-button">Commandes</a>
        </nav>

    </div>
</header>

<div class="backoffice-wrapper">

    <section class="hero" style="padding-top:150px;">
        <div class="container">
            <h2>📦 Détails de la commande #<?= $orderId ?></h2>

            <div id="games-container" style="margin-top:40px;">
                <?php foreach ($items as $i): ?>
                    <div class="card game-card" style="display:flex;align-items:center;gap:15px;">

                        <img src="<?= $i['image'] ?>" style="width:100px;border-radius:10px;">

                        <div>
                            <h3 style="color:#00ff88;"><?= $i['name'] ?></h3>
                            <p>Prix : <?= $i['price'] ?> €</p>
                            <p>Quantité : <?= $i['quantity'] ?></p>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>

        </div>
    </section>

</div>

</body>
</html>
