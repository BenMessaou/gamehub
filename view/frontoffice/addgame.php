<?php
// Démarrer la session pour les messages
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add a Game - GameHub Pro</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="c.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.0-2/css/all.min.css">
</head>
<body>
    <script src="c.js"></script>
            <!-- HEADER -->
            <header class="header">
                <a href="index.php">
                <img src="assests/l1ogo.png" alt="GameHub Pro" class="logo">
                </a>
                <nav>
                <ul class="nav-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="index.php#new-games">Recent Games</a></li>
                    <li><a href="index.php#about">About</a></li>
                    <li><a href="index.php#team">Team</a></li>
                    <li><a href="index.php#contact">Contact</a></li>
                    <li><a href="addgame.html" class="add-game-btn active"><span>Add your game</span></a></li>
                </ul>
                </nav>
                <div class="burger-container">
                <div class="burger"></div>
                </div>
            </header>

            <!-- SECTION AJOUT DE JEU -->
            <section class="add-game-section">
                <div class="container">
                <h1 class="section-title">Publish Your Game on GameHub Pro</h1>
                <p class="section-description">
                    Fill out the form below to submit your game for validation. 
                    All fields marked with <span class="required">*</span> are required.
                </p>
                <?php if (isset($_GET['success']) && $_GET['success'] == '1'): ?>
                    <div style="background: rgba(0, 255, 234, 0.1); border: 1px solid rgba(0, 255, 234, 0.5); color: #00ffea; padding: 16px; border-radius: 8px; margin-bottom: 24px;">
                        <strong>✓ Project submitted successfully!</strong><br>
                        Your project is pending validation by the administrator. You will be notified once it is approved.
                    </div>
                <?php endif; ?>
                <?php if (isset($_GET['error']) && !empty($_GET['error'])): ?>
                    <div style="background: rgba(255, 51, 92, 0.1); border: 1px solid rgba(255, 51, 92, 0.5); color: #ff6b81; padding: 16px; border-radius: 8px; margin-bottom: 24px;">
                        <strong>✗ Error:</strong> <?= htmlspecialchars($_GET['error']); ?>
                    </div>
                <?php endif; ?>

                <!-- FORMULAIRE (enctype pour upload fichier) -->
                <form action="control/add_game.php" method="POST" enctype="multipart/form-data" class="game-form">

                    <!-- Nom du jeu -->
                    <div class="form-group">
                    <label for="nom"><span class="required"></span></label>
                    <input type="text" id="nom" name="nom" required placeholder="Ex: Eternal Quest" maxlength="100">
                    </div>

                    <!-- Développeur / Studio -->
                    <div class="form-group">
                    <label for="developpeur"><span class="required"></span></label>
                    <input type="text" id="developpeur" name="developpeur" required placeholder="Ex: Alex Dupont / Pixel Studio" maxlength="100">
                    </div>

                    <!-- Date de création -->
                    <div class="form-group">
                    <label for="date_creation"><span class="required"></span></label>
                    <input type="date" id="date_creation" name="date_creation" required>
                    </div>

                    <!-- Catégorie -->
                    <div class="form-group">
                    <label for="categorie"><span class="required"></span></label>
                    <select id="categorie" name="categorie" required>
                        <option value="" disabled selected>Choose a category</option>
                        <option value="Action">Action</option>
                        <option value="Aventure">Adventure</option>
                        <option value="RPG">RPG</option>
                        <option value="Stratégie">Strategy</option>
                        <option value="Puzzle">Puzzle</option>
                        <option value="Plateforme">Platform</option>
                        <option value="Simulation">Simulation</option>
                        <option value="Course">Racing</option>
                        <option value="Horreur">Horror</option>
                        <option value="Sport">Sports</option>
                        <option value="Combat">Fighting</option>
                        <option value="Autres">Others</option>
                    </select>
                    </div>

                    <!-- Âge recommandé -->
                    <div class="form-group">
                    <label for="age_recommande"></label>
                    <select id="age_recommande" name="age_recommande">
                        <option value="" selected>None</option>
                        <option value="3+">3+</option>
                        <option value="7+">7+</option>
                        <option value="12+">12+</option>
                        <option value="16+">16+</option>
                        <option value="18+">18+</option>
                    </select>
                    </div>

                    <!-- Lieu de développement -->
                    <div class="form-group">
                    <label for="lieu"></label>
                    <input type="text" id="lieu" name="lieu" placeholder="Ex: Tunis, Tunisie" maxlength="100">
                    </div>

                    <!-- Description détaillée -->
                    <div class="form-group">
                    <label for="description"><span class="required"></span></label>
                    <textarea id="description" name="description" rows="6" required 
                                placeholder="Describe your game: story, gameplay, universe, mechanics, target audience... (min. 150 characters)"></textarea>
                    </div>

                    <!-- Image de couverture -->
                    <div class="form-group">
                    <label for="image"><span class="required"></span></label>
                    <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif" required>
                    <small>Formats: JPG, PNG, GIF | Max: 5 MB | Recommended: 1200x600 px</small>
                    </div>

                    <!-- Captures d’écran -->
                    <div class="form-group">
                    <label for="screenshots"></label>
                    <input type="file" id="screenshots" name="screenshots[]" accept="image/jpeg,image/png" multiple>
                    <small>Max: 3 MB per image</small>
                    </div>

                    <!-- Trailer YouTube -->
                    <div class="form-group">
                    <label for="trailer"><span class="required"></span></label>
                    <input type="url" id="trailer" name="trailer" required 
                            placeholder="https://www.youtube.com/watch?v=...">
                    </div>

                    <!-- Lien de téléchargement -->
                    <div class="form-group">
                    <label for="lien_telechargement"></label>
                    <input type="url" id="lien_telechargement" name="lien_telechargement" 
                            placeholder="https://monjeu.com/download">
                    </div>

                    <!-- Mots-clés -->
                    <div class="form-group">
                    <label for="tags"></label>
                    <input type="text" id="tags" name="tags" 
                            placeholder="Ex: fantasy, open-world, coop, pixel art">
                    <small>Max. 10 keywords</small>
                    </div>

                    <!-- BOUTONS -->
                    <div class="form-actions">
                    <button type="submit" class="submit-btn">
                        Submit for Validation
                    </button>
                    <a href="javascript:history.back()" class="cancel-btn">
                        Cancel
                    </a>
                    </div>
                </form>

                <!-- AIDE -->
                <div class="help-box">
                    <h3>Need Help?</h3>
                    <p>
                    <i class="fas fa-envelope"></i> <a href="mailto:support@gamehubpro.com">support@gamehubpro.com</a><br>
                    <i class="fas fa-book"></i> <a href="guide-soumission.html" target="_blank">Submission Guide</a>
                    </p>
                </div>
                </div>
            </section>

            <!-- FOOTER -->
            <footer class="footer">
                <div class="container">
                <p>© 2025 GameHub Pro | All rights reserved | Tunis, Tunisia</p>
                </div>
            </footer>

<script>
// Forcer la suppression du placeholder dès qu'on tape
document.addEventListener('DOMContentLoaded', function() {
  const inputs = document.querySelectorAll('.form-group input, .form-group textarea');
  
  inputs.forEach(input => {
    // Supprimer le placeholder au focus
    input.addEventListener('focus', function() {
      if (this.placeholder) {
        this.setAttribute('data-placeholder-backup', this.placeholder);
        this.placeholder = '';
        this.setAttribute('data-has-value', 'true');
      }
    });
    
    // Supprimer le placeholder pendant la saisie
    input.addEventListener('input', function() {
      if (this.value && this.value.trim() !== '') {
        if (this.placeholder) {
          this.setAttribute('data-placeholder-backup', this.placeholder);
          this.placeholder = '';
          this.setAttribute('data-has-value', 'true');
        }
      } else {
        // Restaurer si vide
        if (this.hasAttribute('data-placeholder-backup')) {
          this.placeholder = this.getAttribute('data-placeholder-backup');
          this.removeAttribute('data-placeholder-backup');
          this.removeAttribute('data-has-value');
        }
      }
    });
    
    // Restaurer au blur seulement si vide
    input.addEventListener('blur', function() {
      if (this.value === '' || this.value.trim() === '') {
        if (this.hasAttribute('data-placeholder-backup')) {
          this.placeholder = this.getAttribute('data-placeholder-backup');
          this.removeAttribute('data-placeholder-backup');
          this.removeAttribute('data-has-value');
        }
      }
    });
    
    // Vérifier si l'input a déjà une valeur au chargement
    if (input.value && input.value.trim() !== '') {
      if (input.placeholder) {
        input.setAttribute('data-placeholder-backup', input.placeholder);
        input.placeholder = '';
        input.setAttribute('data-has-value', 'true');
      }
    }
  });
});
</script>

</body>
</html>