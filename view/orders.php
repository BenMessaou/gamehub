<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../controller/OrderController.php';

$controller = new OrderController($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $controller->delete($_POST['id']);
    header("Location: orders.php");
    exit;
}

$orders = $controller->all();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Commandes - GameHub Admin</title>
    <link rel="stylesheet" href="../style.css?v=9999">
</head>

<body>

<header>
    <div class="container header-flex">
        <div class="logo">
            <img src="https://scontent-pmo1-1.xx.fbcdn.net/v/t1.15752-9/582645474_3853735594931532_6197179205393523828_n.png?_nc_cat=108&ccb=1-7&_nc_sid=0024fc&_nc_ohc=QA5GaGx5snsQ7kNvwGxQbKD&_nc_oc=Adl9hAnh5bAG2-ilhkY6lhoY82g7D66Wv-URxXgT3DDsXdOscrg0Ifr9e7p4Pg77Zh4&_nc_ad=z-m&_nc_cid=0&_nc_zt=23&_nc_ht=scontent-pmo1-1.xx&oh=03_Q7cD3wGcL5fTt3cl3B-oNfMu8m4S6E2aUOmWih8aJZdhhHUuOQ&oe=69540D11" alt="logo">
            <span>GameHub Admin</span>
        </div>

        <nav class="nav-links">
            <a href="shop.php" class="super-button">Voir le site</a>
            <a href="backoffice.php" class="super-button">Jeux</a>
            <a href="orders.php" class="super-button">Commandes</a>
        </nav>
    </div>
</header>

<div class="orders-wrapper">

    <section class="hero" style="padding-top:150px;">
        <div class="container">
            <h2>📦 Liste des commandes</h2>

            <!-- GRID pour commandes -->
            <div id="orders-grid">

                <?php foreach ($orders as $o): ?>
                    <div class="order-card-container">

                        <h3 style="color:#00ff88;">Commande #<?= $o['id'] ?></h3>

                        <p>Total : <strong><?= $o['total'] ?> €</strong></p>

                        <p style="color:#aaa;">
                            Date : <?= $o['created_at'] ?>
                        </p>

                        <div style="display:flex; gap:10px; margin-top:15px;">
                            <a href="order_details.php?id=<?= $o['id'] ?>" 
                               class="super-button-small">
                                Voir détails
                            </a>

                            <form method="POST" onsubmit="return confirm('Supprimer cette commande ?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $o['id'] ?>">
                                <button type="submit" class="delete-btn" style="padding:8px 14px;">Supprimer</button>
                            </form>
                        </div>

                    </div>
                <?php endforeach; ?>

            </div>

        </div>
    </section>

</div>

</body>
</html>
