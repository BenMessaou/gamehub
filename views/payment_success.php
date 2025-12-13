<?php
require_once __DIR__ . '/../config.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Paiement Réussi - GameHub</title>
    <link rel="stylesheet" href="../style.css?v=9999">
    <style>
        .success-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 70vh;
            text-align: center;
        }
        .success-card {
            background: #1a1a1a;
            border: 1px solid #00ff88;
            border-radius: 12px;
            padding: 3rem;
            box-shadow: 0 0 30px rgba(0, 255, 136, 0.2);
        }
        .check-icon {
            font-size: 4rem;
            color: #00ff88;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

<header>
    <div class="container header-flex">
        <div class="logo">
            <img src="https://scontent-pmo1-1.xx.fbcdn.net/v/t1.15752-9/582645474_3853735594931532_6197179205393523828_n.png?_nc_cat=108&ccb=1-7&_nc_sid=0024fc&_nc_ohc=QA5GaGx5snsQ7kNvwGxQbKD&_nc_oc=Adl9hAnh5bAG2-ilhkY6lhoY82g7D66Wv-URxXgT3DDsXdOscrg0Ifr9e7p4Pg77Zh4&_nc_ad=z-m&_nc_cid=0&_nc_zt=23&_nc_ht=scontent-pmo1-1.xx&oh=03_Q7cD3wGcL5fTt3cl3B-oNfMu8m4S6E2aUOmWih8aJZdhhHUuOQ&oe=69540D11" alt="logo">
            <span>GameHub</span>
        </div>
    </div>
</header>

<div class="success-wrapper">
    <div class="success-card">
        <div class="check-icon">✔</div>
        <h1 style="color: #fff; margin-bottom: 1rem;">Paiement Confirmé !</h1>
        <p style="color: #ccc; font-size: 1.1rem; margin-bottom: 2rem;">
            Merci pour votre commande. Elle a été enregistrée avec succès.
        </p>
        <a href="shop.php" class="super-button">Retour à la boutique</a>
    </div>
</div>

<footer>
    <div class="container">
        <p>&copy; 2025 GameHub. Tous droits réservés.</p>
    </div>
</footer>

</body>
</html>
