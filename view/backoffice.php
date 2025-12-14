<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../controller/GameController.php';

$controller = new GameController($pdo);
$message = "";

/* ============================
   ADD / UPDATE / DELETE
   ============================ */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    /* --- SUPPRESSION --- */
    if ($_POST["action"] === "delete") {
        if ($controller->delete($_POST["id"])) {
            $message = "<p style='color:#ff0096;text-align:center;'>✔ Jeu supprimé</p>";
        }
    }

    /* --- AJOUT --- */
    if ($_POST["action"] === "add") {
        $game = [
            "name" => $_POST["name"],
            "price" => floatval($_POST["price"]),
            "rating" => floatval($_POST["rating"]),
            "category" => $_POST["category"],
            "image" => $_POST["image"],
            "description" => $_POST["description"],
        ];

        if ($controller->add($game)) {
            $message = "<p style='color:#00ff88;text-align:center;'>✔ Jeu ajouté</p>";
        }
    }

    /* --- MODIFICATION --- */
    if ($_POST["action"] === "update") {

        $game = [
            "name" => $_POST["name"],
            "price" => floatval($_POST["price"]),
            "rating" => floatval($_POST["rating"]),
            "category" => $_POST["category"],
            "image" => $_POST["image"],
            "description" => $_POST["description"],
        ];

        if ($controller->update($_POST["id"], $game)) {
            $message = "<p style='color:#00ff88;text-align:center;'>✔ Jeu mis à jour</p>";
        }
    }
}

/* ============================
   CHARGER LES JEUX
   ============================ */

try {
    $games = $controller->index();
} catch (Exception $e) {
    $games = [];
    $message = "<p style='color:red;text-align:center;'>Erreur de chargement</p>";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Backoffice - GameHub</title>

    <!-- FORCE CSS UPDATE -->
    <link rel="stylesheet" href="../style.css?v=9999" />
</head>

<body>

<!-- ================= HEADER ================= -->
<header>
    <div class="container header-flex">

        <div class="logo">
            <img src="https://scontent-pmo1-1.xx.fbcdn.net/v/t1.15752-9/582645474_3853735594931532_6197179205393523828_n.png?_nc_cat=108&ccb=1-7&_nc_sid=0024fc&_nc_ohc=QA5GaGx5snsQ7kNvwGxQbKD&_nc_oc=Adl9hAnh5bAG2-ilhkY6lhoY82g7D66Wv-URxXgT3DDsXdOscrg0Ifr9e7p4Pg77Zh4&_nc_ad=z-m&_nc_cid=0&_nc_zt=23&_nc_ht=scontent-pmo1-1.xx&oh=03_Q7cD3wGcL5fTt3cl3B-oNfMu8m4S6E2aUOmWih8aJZdhhHUuOQ&oe=69540D11" alt="logo">
            <span>GameHub Admin</span>
        </div>

        <nav class="nav-links">
            <a href="shop.php" class="super-button">Voir le site</a>
            <a href="#" class="super-button">Backoffice</a>
        </nav>

    </div>
</header>

<!-- WRAPPER BACKGROUND FIX -->
<div class="backoffice-wrapper">

<!-- ================= FORMULAIRE ================= -->

<section class="hero" style="padding-top:150px;">
    <div class="container">

        <h2 id="form-title" style="margin-bottom:30px;">Ajouter un jeu</h2>

        <?= $message ?>

        <form method="POST" class="form-container" id="gameForm" style="max-width:600px;margin:auto;">
            <input type="hidden" name="action" id="form-action" value="add">
            <input type="hidden" name="id" id="game-id" value="">

            <div class="form-group">
                <label>Nom du jeu</label>
                <input name="name" id="name-input" required>
            </div>

            <div class="form-row" style="display:flex;gap:1rem;">
                <div class="form-group" style="flex:1;">
                    <label>Prix (€)</label>
                    <input type="number" step="0.01" name="price" id="price-input" required>
                </div>

                <div class="form-group" style="flex:1;">
                    <label>Rating</label>
                    <input type="number" step="0.1" min="0" max="5" name="rating" id="rating-input" required>
                </div>
            </div>

            <div class="form-group">
                <label>Catégorie</label>
                <select name="category" id="category-input">
                    <option value="action">Action</option>
                    <option value="sport">Sport</option>
                    <option value="rpg">RPG</option>
                    <option value="aventure">Aventure</option>
                </select>
            </div>

            <div class="form-group">
                <label>Image (URL)</label>
                <input name="image" id="image-input" required>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" id="description-input" rows="4" required></textarea>
            </div>

            <button class="super-button" type="submit" id="submit-button" style="width:100%;">Ajouter le jeu</button>

            <button type="button" id="cancel-button"
                    class="super-button"
                    style="background:#444; display:none; margin-top:10px;"
                    onclick="resetForm()">
                Annuler
            </button>
        </form>

    </div>
</section>

<!-- ================= GESTION DES JEUX ================= -->

<section class="deals">
    <div class="container">

        <h3 style="margin-bottom:3rem;">Gestion des jeux</h3>

        <div id="games-container" style="gap:2rem;
                display:grid;
                grid-template-columns:repeat(auto-fit,minmax(280px,1fr));">

            <?php foreach ($games as $g): ?>
            <div class="card game-card">

                <img src="<?= $g['image'] ?>" alt="<?= $g['name'] ?>">

                <h4><?= $g['name'] ?></h4>

                <p class="description" style="max-height:60px; overflow:hidden;">
                    <?= $g['description'] ?>
                </p>

                <p style="color:#00ff88;font-size:1.1rem;">
                    <?= number_format($g['price'],2) ?> €  
                    <span style="color:#ffd700;">★ <?= $g['rating'] ?></span>
                </p>

                <div style="display:flex; gap:10px; justify-content:center;">

                    <!-- Bouton Modifier -->
                    <button class="edit-btn"
                            style="padding:8px 14px; border-radius:8px; cursor:pointer;
                                   background:#ffbb00; font-weight:bold;"
                            onclick='editGame(<?= json_encode($g, JSON_UNESCAPED_UNICODE) ?>)'>
                        Modifier
                    </button>

                    <!-- Supprimer -->
                    <form method="POST">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $g['id'] ?>">
                        <button class="delete-btn" style="padding:8px 14px;">Supprimer</button>
                    </form>

                </div>

            </div>
            <?php endforeach; ?>

        </div>

    </div>
</section>

</div> <!-- END WRAPPER -->

<footer>
    <div class="container">
        <p>&copy; 2025 GameHub Admin. Tous droits réservés.</p>
    </div>
</footer>

<!-- ================= JAVASCRIPT ================= -->

<script>
function editGame(game) {

    document.getElementById("form-title").textContent = "Modifier le jeu";

    // remplir formulaire
    document.getElementById("name-input").value = game.name;
    document.getElementById("price-input").value = game.price;
    document.getElementById("rating-input").value = game.rating;
    document.getElementById("category-input").value = game.category;
    document.getElementById("image-input").value = game.image;
    document.getElementById("description-input").value = game.description;

    // changer mode
    document.getElementById("form-action").value = "update";
    document.getElementById("game-id").value = game.id;

    // changer bouton
    document.getElementById("submit-button").textContent = "Mettre à jour";
    document.getElementById("cancel-button").style.display = "block";

    // scroll top
    window.scrollTo({ top: 100, behavior: 'smooth' });
}

function resetForm() {
    document.getElementById("form-title").textContent = "Ajouter un jeu";

    document.getElementById("gameForm").reset();
    document.getElementById("form-action").value = "add";
    document.getElementById("game-id").value = "";
    document.getElementById("submit-button").textContent = "Ajouter le jeu";
    document.getElementById("cancel-button").style.display = "none";
}
</script>

</body>
</html>
