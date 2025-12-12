<?php
session_start();

// Mode développeur : permettre l'accès même sans connexion
$isLoggedIn = isset($_SESSION['user_id']);
$userId = $isLoggedIn ? $_SESSION['user_id'] : 1; // ID par défaut pour le développeur
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avatar Shop - GameHub Pro</title>
    <link rel="stylesheet" href="avatar_shop.css">
    <link rel="stylesheet" href="avatar_cartoon.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- html2canvas pour capturer l'avatar -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <!-- Avatar AI Analyzer -->
    <script src="avatar_ai_analyzer.js"></script>
</head>
<body>
    <!-- Header -->
    <header class="avatar-header">
        <div class="header-content">
            <div class="logo-section">
                <img src="../frontoffice/assests/logo.png" alt="GameHub Pro" class="logo-img">
                <h1 class="main-title">Super Tropper</h1>
            </div>
            <nav class="header-nav">
                <a href="../frontoffice/index.php" class="nav-link">Home</a>
                <a href="../frontoffice/collaborations.php" class="nav-link">Collaborations</a>
            </nav>
        </div>
        <!-- Fairy Lights Decoration -->
        <div class="fairy-lights">
            <span class="light"></span><span class="light"></span><span class="light"></span>
            <span class="light"></span><span class="light"></span><span class="light"></span>
            <span class="light"></span><span class="light"></span><span class="light"></span>
            <span class="light"></span><span class="light"></span><span class="light"></span>
        </div>
    </header>

    <!-- Main Container -->
    <div class="avatar-shop-container">
        <!-- Left Panel - Customization -->
        <div class="customization-panel">
            <!-- Category Navigation -->
            <div class="category-nav">
                <button class="category-btn active" data-category="hair" title="Cheveux">
                    <span class="icon">💇</span>
                </button>
                <button class="category-btn" data-category="face" title="Visage">
                    <span class="icon">😊</span>
                </button>
                <button class="category-btn" data-category="helmet" title="Casque">
                    <span class="icon">⛑️</span>
                </button>
                <button class="category-btn" data-category="shirt" title="T-shirt">
                    <span class="icon">👕</span>
                </button>
                <button class="category-btn" data-category="pants" title="Pantalon">
                    <span class="icon">👖</span>
                </button>
                <button class="category-btn" data-category="shoes" title="Chaussures">
                    <span class="icon">👟</span>
                </button>
                <button class="category-btn" data-category="accessories" title="Accessoires">
                    <span class="icon">🎩</span>
                </button>
            </div>

            <!-- Avatar Presets Section -->
            <div class="presets-section">
                <h3 class="section-title">🎨 Avatars Pré-faits</h3>
                <div class="presets-grid">
                    <button class="preset-btn" onclick="loadPreset('realistic')" data-preset="realistic">
                        <span class="preset-icon">👤</span>
                        <span class="preset-name">Réaliste</span>
                    </button>
                    <button class="preset-btn" onclick="loadPreset('cartoon')" data-preset="cartoon">
                        <span class="preset-icon">🎭</span>
                        <span class="preset-name">Cartoon</span>
                    </button>
                    <button class="preset-btn" onclick="loadPreset('gamer')" data-preset="gamer">
                        <span class="preset-icon">🎮</span>
                        <span class="preset-name">Gamer</span>
                    </button>
                </div>
            </div>

            <!-- AI Generation Section -->
            <div class="ai-section">
                <h3 class="section-title">✨ Génération IA</h3>
                <div class="selfie-upload">
                    <label for="selfie-input" class="upload-label">
                        <span class="upload-icon">📸</span>
                        <span class="upload-text">Uploader un selfie</span>
                    </label>
                    <input type="file" id="selfie-input" accept="image/*" style="display: none;">
                    <div id="selfie-preview" class="selfie-preview" style="display: none;">
                        <img id="selfie-img" src="" alt="Selfie">
                        <button class="remove-selfie" onclick="removeSelfie()">×</button>
                    </div>
                </div>
                <div class="demo-workflow">
                    <div class="workflow-step active" id="step-1">
                        <div class="step-number">1</div>
                        <div class="step-content">
                            <h4>Selfie Uploadé</h4>
                            <p>Simulation : Avatar généré par IA</p>
                        </div>
                    </div>
                    <div class="workflow-step" id="step-2">
                        <div class="step-number">2</div>
                        <div class="step-content">
                            <h4>Changer les traits</h4>
                            <p>Personnalisez les traits faciaux</p>
                        </div>
                    </div>
                    <div class="workflow-step" id="step-3">
                        <div class="step-number">3</div>
                        <div class="step-content">
                            <h4>Ajouter un accessoire</h4>
                            <p>Complétez votre avatar</p>
                        </div>
                    </div>
                </div>
                <button class="generate-btn" onclick="simulateAIGeneration()">
                    <span class="sparkle">✨</span>
                    Générer avec IA
                </button>
                <div class="facial-traits-editor" id="facial-traits-editor" style="display: none;">
                    <h4>🎭 Modifier les traits faciaux</h4>
                    <div class="traits-controls">
                        <div class="trait-control">
                            <label>Expression:</label>
                            <select id="expression-select" onchange="changeFacialTrait('expression', this.value)">
                                <option value="happy">Heureux 😊</option>
                                <option value="laugh">Rire 😂</option>
                                <option value="surprised">Surpris 😲</option>
                                <option value="sad">Triste 😢</option>
                                <option value="neutral">Neutre 😐</option>
                                <option value="cool">Cool 😎</option>
                                <option value="wink">Clin d'œil 😉</option>
                                <option value="star">Étoiles ⭐</option>
                            </select>
                        </div>
                        <div class="trait-control">
                            <label>Couleur des yeux:</label>
                            <input type="color" id="eye-color" value="#000000" onchange="changeFacialTrait('eyeColor', this.value)">
                        </div>
                        <div class="trait-control">
                            <label>Couleur de la bouche:</label>
                            <input type="color" id="mouth-color" value="#ff6b6b" onchange="changeFacialTrait('mouthColor', this.value)">
                        </div>
                        <div class="trait-control">
                            <label>Taille du nez:</label>
                            <input type="range" id="nose-size" min="1" max="5" value="3" onchange="changeFacialTrait('noseSize', this.value)">
                        </div>
                    </div>
                </div>
            </div>

            <!-- New Category Alert -->
            <div class="new-category-alert">
                <h3>Alerte nouvelle catégorie !</h3>
                <p>Découvrez nos nouveaux accessoires exclusifs</p>
            </div>

            <!-- Base Avatar Preview -->
            <div class="base-avatar-preview">
                <div class="avatar-mini">
                    <div class="avatar-face">😊</div>
                    <div class="avatar-body">👕👖</div>
                </div>
            </div>

            <!-- Category Items -->
            <div class="items-container" id="items-container">
                <!-- Items will be loaded dynamically -->
            </div>

            <!-- Login Prompt -->
            <div class="login-prompt">
                <p>Vous souhaitez plus d'accessoires pour personnaliser votre Qbit ?</p>
                <a href="#" class="login-link">🔐 Connectez-vous maintenant</a>
            </div>
        </div>

        <!-- Right Panel - 3D Avatar Preview -->
        <div class="preview-panel">
            <div class="avatar-display">
                <div class="pedestal">
                    <div class="spotlight"></div>
                    <div class="avatar-3d" id="avatar-3d">
                        <!-- Cartoon Avatar will be rendered here -->
                        <div id="cartoon-avatar-container"></div>
                    </div>
                </div>
                <div class="avatar-speech" id="avatar-speech">
                    Tu assures grave !
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <button class="action-btn download-btn" onclick="downloadAvatar()" title="Télécharger">
                    <span class="btn-icon">⬇️</span>
                </button>
                <button class="action-btn random-btn" onclick="randomizeAvatar()" title="Aléatoire">
                    <span class="btn-icon">🎲</span>
                </button>
            </div>

            <!-- Save Button -->
            <button class="save-btn" onclick="saveAvatar()">
                Sauvez mon Qbit
            </button>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="success-modal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <div class="modal-icon">✅</div>
            <h2>Avatar sauvegardé !</h2>
            <p>Votre Qbit a été sauvegardé avec succès.</p>
        </div>
    </div>

    <script src="avatar_cartoon_faces.js"></script>
    <script src="avatar_cartoon_renderer.js"></script>
    <script src="avatar_ai_analyzer.js"></script>
    <script src="avatar_presets.js"></script>
    <script src="avatar_shop.js"></script>
</body>
</html>

