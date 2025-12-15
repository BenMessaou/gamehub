<?php
require_once __DIR__ . '/../config.php';

// ======= Récupération des statistiques =======

// Nombre de jeux
$totalGames = $pdo->query("SELECT COUNT(*) FROM games")->fetchColumn();

// Nombre de commandes
$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();

// Total ventes (€)
$totalSales = $pdo->query("SELECT SUM(total) FROM orders")->fetchColumn();
if (!$totalSales) $totalSales = 0;

// Dernier jeu ajouté
$lastGame = $pdo->query("SELECT name FROM games ORDER BY id DESC LIMIT 1")->fetchColumn();
if (!$lastGame) $lastGame = "Aucun jeu";

// Dernière commande ID
$lastOrder = $pdo->query("SELECT id FROM orders ORDER BY id DESC LIMIT 1")->fetchColumn();
if (!$lastOrder) $lastOrder = "Aucune commande";

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - GameHub Admin</title>
    <link rel="stylesheet" href="../style.css?v=9999">
    <link rel="stylesheet" href="frontoffice/index.css">

    <style>
        /* ======= DASHBOARD WRAPPER ======= */
        .dashboard-wrapper {
            margin-top: 120px;
            padding: 50px 0;
            min-height: 100vh;
            background: linear-gradient(180deg, rgba(10,10,25,0.95), rgba(20,20,40,0.95));
        }

        /* ======= GRID ======= */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 2rem;
            padding: 30px;
        }

        /* ======= DASH CARD ======= */
        .dash-card {
            background: rgba(0,0,0,0.7);
            border: 1px solid rgba(0,255,136,0.3);
            border-radius: 18px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 0 25px rgba(0,255,136,0.25);
            backdrop-filter: blur(8px);
            transition: 0.3s;
        }

        .dash-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 0 35px rgba(0,255,136,0.45);
        }

        .dash-card h3 {
            color: #00ff88;
            font-size: 1.4rem;
            margin-bottom: 10px;
        }

        .dash-card .number {
            font-size: 2.6rem;
            font-weight: bold;
            color: #fff;
            text-shadow: 0 0 20px rgba(0,255,136,0.7);
        }

        .dash-card p {
            color: #aaa;
            margin-top: 8px;
        }

        /* ======= ACTION BUTTONS ======= */
        .dash-actions {
            text-align: center;
            margin-top: 40px;
        }

        .dash-actions a {
            margin: 0 10px;
        }
    </style>
</head>

<body>

<!-- HEADER STYLE GAMEHUB -->
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


<!-- ======= DASHBOARD CONTENT ======= -->
<div class="dashboard-wrapper">

    <section class="hero" style="padding-top:150px;">
        <div class="container">

            <h2>📊 Dashboard Administrateur</h2>
            <p style="color:#ccc;margin-top:10px;">Vue d’ensemble de votre plateforme GameHub</p>
          <a href="backoffice.php" class="shop-now-btn">Jeux</a>
            <a href="orders.php" class="shop-now-btn">Commandes</a>
            <!-- ======= GRID STATISTIQUES ======= -->
            <div class="dashboard-grid">

                <div class="dash-card">
                    <h3>🎮 Total Jeux</h3>
                    <div class="number"><?= $totalGames ?></div>
                </div>

                <div class="dash-card">
                    <h3>🛒 Commandes</h3>
                    <div class="number"><?= $totalOrders ?></div>
                </div>

                <div class="dash-card">
                    <h3>💰 Ventes Totales</h3>
                    <div class="number"><?= number_format($totalSales, 2) ?> €</div>
                </div>

                <div class="dash-card">
                    <h3>🔥 Dernier Jeu Ajouté</h3>
                    <p style="font-size:1.4rem;color:#fff;"><?= $lastGame ?></p>
                </div>

                <div class="dash-card">
                    <h3>📦 Dernière Commande</h3>
                    <p style="font-size:1.4rem;color:#fff;">#<?= $lastOrder ?></p>
                </div>

            </div>

            <!-- ======= BOUTONS ======= -->
            <div class="dash-actions">
                <a href="backoffice.php" class="super-button-clean">Gérer les Jeux</a>
                <a href="orders.php" class="super-button-clean">Voir les Commandes</a>
                <a href="shop.php" class="super-button-clean">Ouvrir la Boutique</a>
            </div>

        </div>
    </section>

</div>

</body>
</html>
