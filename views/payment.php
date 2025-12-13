<?php
require_once __DIR__ . '/../config.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Paiement - GameHub</title>
    <link rel="stylesheet" href="../style.css?v=9999">
    <style>
        .payment-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 60vh;
            padding-top: 100px;
        }
        .payment-card {
            background: #1a1a1a;
            border: 1px solid #333;
            border-radius: 12px;
            padding: 2rem;
            width: 100%;
            max-width: 500px;
            text-align: center;
            box-shadow: 0 0 20px rgba(0, 255, 136, 0.1);
        }
        .total-amount {
            font-size: 2.5rem;
            color: #00ff88;
            margin: 2rem 0;
            font-weight: bold;
        }
        .payment-btn {
            width: 100%;
            padding: 1rem;
            font-size: 1.2rem;
            margin-top: 1rem;
        }
    </style>
</head>
<body>

<header>
    <div class="container header-flex">
        <div class="logo">
            <img src="https://scontent-pmo1-1.xx.fbcdn.net/v/t1.15752-9/582645474_3853735594931532_6197179205393523828_n.png?_nc_cat=108&ccb=1-7&_nc_sid=0024fc&_nc_ohc=QA5GaGx5snsQ7kNvwGxQbKD&_nc_oc=Adl9hAnh5bAG2-ilhkY6lhoY82g7D66Wv-URxXgT3DDsXdOscrg0Ifr9e7p4Pg77Zh4&_nc_ad=z-m&_nc_cid=0&_nc_zt=23&_nc_ht=scontent-pmo1-1.xx&oh=03_Q7cD3wGcL5fTt3cl3B-oNfMu8m4S6E2aUOmWih8aJZdhhHUuOQ&oe=69540D11" alt="logo">
            <span>GameHub Paiement</span>
        </div>
        <nav class="nav-links">
            <a href="shop.php" class="super-button">Retour Boutique</a>
        </nav>
    </div>
</header>

<div class="payment-wrapper">
    <div class="payment-card">
        <h2>Récapitulatif de la commande</h2>
        
        <div class="total-amount" id="displayTotal">0,00 €</div>
        
        <p style="color: #aaa; margin-bottom: 2rem;">
            Veuillez confirmer votre commande pour procéder au paiement.
        </p>

        <button id="confirmPaymentBtn" class="super-button payment-btn">
            Procéder au paiement
        </button>
    </div>
</div>

<footer>
    <div class="container">
        <p>&copy; 2025 GameHub. Tous droits réservés.</p>
    </div>
</footer>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Récupérer le panier du localStorage
    const cartItems = JSON.parse(localStorage.getItem("cart")) || [];
    const discount = parseFloat(localStorage.getItem("discount")) || 0;
    
    if (cartItems.length === 0) {
        alert("Votre panier est vide !");
        window.location.href = 'shop.php';
        return;
    }

    // 2. Calculer le total
    const subtotal = cartItems.reduce((acc, item) => acc + (item.price * item.quantity), 0);
    const total = Math.max(0, subtotal - discount);

    // 3. Afficher le total
    document.getElementById('displayTotal').textContent = total.toFixed(2) + " €";

    // 4. Gérer le clic sur "Procéder au paiement"
    document.getElementById('confirmPaymentBtn').addEventListener('click', async () => {
        
        try {
            const response = await fetch("checkout.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    total: total,
                    items: cartItems
                })
            });

            const result = await response.json();

            if (result.success) {
                // Vider le panier
                localStorage.removeItem("cart");
                localStorage.removeItem("discount");
                
                // Redirection succès
                window.location.href = 'payment_success.php';
            } else {
                alert("Erreur lors de la commande : " + (result.message || "Inconnue"));
            }
        } catch (error) {
            console.error(error);
            alert("Une erreur est survenue.");
        }
    });
});
</script>

</body>
</html>
