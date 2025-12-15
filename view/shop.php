<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../controller/GameController.php';

$controller = new GameController($pdo);
try {
    $games = $controller->index();
} catch (Exception $e) {
    $error = "Erreur : " . $e->getMessage();
    $games = [];
}
if (!empty($_GET['sort'])) {
    if ($_GET['sort'] === 'asc') {
        usort($games, fn($a, $b) => $a['price'] <=> $b['price']);
    } elseif ($_GET['sort'] === 'desc') {
        usort($games, fn($a, $b) => $b['price'] <=> $a['price']);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GameHub - Boutique</title>
    <link rel="stylesheet" href="../style.css?v=9999">
</head>
<body>

<!-- HEADER -->
<header>
    <div class="container header-flex">
        
        <div class="logo">
            <img src="https://scontent-pmo1-1.xx.fbcdn.net/v/t1.15752-9/582645474_3853735594931532_6197179205393523828_n.png?_nc_cat=108&ccb=1-7&_nc_sid=0024fc&_nc_ohc=QA5GaGx5snsQ7kNvwGxQbKD&_nc_oc=Adl9hAnh5bAG2-ilhkY6lhoY82g7D66Wv-URxXgT3DDsXdOscrg0Ifr9e7p4Pg77Zh4&_nc_ad=z-m&_nc_cid=0&_nc_zt=23&_nc_ht=scontent-pmo1-1.xx&oh=03_Q7cD3wGcL5fTt3cl3B-oNfMu8m4S6E2aUOmWih8aJZdhhHUuOQ&oe=69540D11" alt="logo">
            <span>GameHub</span>
        </div>

       <div class="container">
        <h1 class="logo">gamehub</h1>
        <img src="logo.png" class="logo1" alt="">
        <nav>
            <ul>
                <li><a href="frontoffice/index.php" class="super-button">Projects</a></li>
                <li><a href="#deals" class="super-button">Events
                </a></li>
                <li><a href="shop.php" class="super-button">Shop </a></li>
                <li><a href="article/list.php" class="super-button">Article</a></li><li><a class="super-button" href="index1.php">feedback</a></li>
                <li><a class="super-button" href="frontoffice/profile.php">Profile</a></li>
               
            </ul>
        </nav>
    </div>

        <div class="cart-icon" id="cartIcon">
            <svg width="26" height="26" stroke="#00ff88">
                <circle cx="9" cy="21" r="1"></circle>
                <circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.6 13.3a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.6L23 6H6"></path>
            </svg>
            <span class="cart-badge" id="cartBadge">0</span>
        </div>

    </div>

    <div class="cart-dropdown" id="cartDropdown">
        <div class="cart-header">
            <h4>Votre Panier</h4>
            <button class="close-btn" id="closeCart">&times;</button>
        </div>

        <div class="cart-items" id="cartItems"></div>

        <div class="cart-footer">
            <div class="total">
                <strong>Sous-total :</strong>
                <span id="cartTotal">0,00 €</span>
            </div>

            <div class="promo-section">
                <input type="text" id="promoInput" class="super-button" placeholder="Code promo">
                <button class="super-button" id="applyPromoBtn">Appliquer</button>
            </div>

            <div class="discount" id="discountDisplay" style="display:none;">
                Réduction : -<span id="discountAmount">0,00</span> €
            </div>

            <div class="final-total">
                <strong>Total :</strong>
                <span id="finalTotal">0,00 €</span>
            </div>

            <button class="clear-cart-btn" id="clearCartBtn">Vider le panier</button>
            <button class="checkout-btn" id="checkoutBtn">PASSER À LA CAISSE</button>

        </div>
    </div>
</header>

<section class="hero">
    <div class="container">
        <h2>🎮 Plonge dans l’univers du jeu vidéo</h2>
        <p>Découvre les meilleures offres du moment.</p>
    </div>
    <a class="super-button">Jeux</a>
            <a class="super-button">Promos</a>
</section>

<section class="deals">
    <div class="container">
        <h3>Nos Jeux</h3>

        <div style="text-align:center;margin-bottom:2rem;">
            <select id="sortPrice" class="super-button" style="width:260px;">
                <option value="">Trier par prix</option>
                <option value="asc">Prix : du moins cher</option>
                <option value="desc">Prix : du plus cher</option>
            </select>
        </div>

        <div style="text-align:center;margin-bottom:2rem;">
            <input type="text" id="searchInput" class="super-button" 
                   style="width:300px;" placeholder="Rechercher un jeu...">
        </div>

        <div style="text-align:center;margin-bottom:2rem;">
            <button class="filter-btn super-button active" data-category="all">Tous</button>
            <button class="filter-btn super-button" data-category="sport">Sport</button>
            <button class="filter-btn super-button" data-category="rpg">RPG</button>
            <button class="filter-btn super-button" data-category="aventure">Aventure</button>
            <button class="filter-btn super-button" data-category="action">Action</button>
        </div>

        <div id="games-container">
            <?php foreach ($games as $index => $game): ?>
            <div class="card game-card"
                 data-id="<?= $game['id'] ?>"
                 data-category="<?= $game['category'] ?>"
                 data-rating="<?= $game['rating'] ?>"
                 data-price="<?= $game['price'] ?>"
                 data-name="<?= htmlspecialchars($game['name']) ?>">

                <img src="<?= $game['image'] ?>">
                <h4><?= htmlspecialchars($game['name']) ?></h4>
                <p class="description"><?= htmlspecialchars($game['description']) ?></p>

                <div class="rating">
                    <span class="stars" id="stars-<?= $index ?>"></span>
                    <span class="reviews">(... avis)</span>
                </div>

                <p class="price"><?= number_format($game['price'],2) ?> €</p>
                <button class="super-button add-to-cart">Ajouter au panier</button>
            </div>
            <?php endforeach; ?>
        </div>

    </div>
</section>

<footer>
    <div class="container">
        <p>&copy; 2025 GameHub. Tous droits réservés.</p>
    </div>
</footer>

<script src="../script.js?v=9999"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
<?php foreach ($games as $index => $game): ?>
    renderStars(<?= $game['rating'] ?>, "stars-<?= $index ?>");
<?php endforeach; ?>
});
</script>

<script>
document.getElementById('sortPrice').addEventListener('change', function() {
    let url = new URL(window.location.href);
    if (this.value) url.searchParams.set('sort', this.value);
    else url.searchParams.delete('sort');
    window.location.href = url.toString();
});
</script>

</body>
</html>
