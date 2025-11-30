<?php
session_start();

// Mode développeur : permettre l'accès même sans connexion
$isLoggedIn = isset($_SESSION['user_id']);

require_once __DIR__ . "/../../../controller/controllercollab/CollabMemberController.php";
require_once __DIR__ . "/../../../controller/controllercollab/CollabProjectController.php";
require_once __DIR__ . "/../../../model/collab/CollabMember.php";

$memberController = new CollabMemberController();
$projectController = new CollabProjectController();

// Récupérer le collab_id depuis l'URL ou le POST
$collab_id = isset($_GET['collab_id']) ? intval($_GET['collab_id']) : (isset($_POST['collab_id']) ? intval($_POST['collab_id']) : null);

if (!$collab_id) {
    die("ID de collaboration manquant.");
}

// Récupérer le projet pour vérifier les permissions
$collab = $projectController->getById($collab_id);
if (!$collab) {
    die("Projet collaboratif introuvable.");
}

// Vérifier que l'utilisateur est le propriétaire (sauf en mode développeur)
if ($isLoggedIn) {
    if ($collab['owner_id'] != $_SESSION['user_id']) {
        die("Erreur : vous n'êtes pas le propriétaire de ce projet. Seul le propriétaire peut ajouter des membres.");
    }
}

// Variables pour les messages
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
    $role = $_POST['role'] ?? 'membre';

    // Validation
    if ($user_id <= 0) {
        $message = 'Veuillez entrer un ID utilisateur valide.';
        $messageType = 'error';
    } else {
        // Vérifier si le projet est plein
        $currentMembers = $memberController->getMembers($collab_id);
        if (count($currentMembers) >= $collab['max_membres']) {
            $message = 'Ce projet a atteint le nombre maximum de membres.';
            $messageType = 'error';
        } else {
            // Vérifier si l'utilisateur est déjà membre
            if ($memberController->isMember($collab_id, $user_id)) {
                $message = 'Cet utilisateur est déjà membre de ce projet.';
                $messageType = 'error';
            } else {
                $member = new CollabMember(null, $collab_id, $user_id, $role);
                $success = $memberController->add($member);

                if ($success) {
                    header("Location: view_collab.php?id=" . $collab_id . "&member_added=1");
                    exit;
                } else {
                    $message = 'Erreur lors de l\'ajout du membre.';
                    $messageType = 'error';
                }
            }
        }
    }
}

// Récupérer les membres actuels pour afficher le nombre
$currentMembers = $memberController->getMembers($collab_id);
$remainingSlots = $collab['max_membres'] - count($currentMembers);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un membre - GameHub Pro</title>
    <link rel="stylesheet" href="../../frontoffice/collaborations.css">
    <style>
        body {
            padding-top: 100px;
        }
        .create-member-container {
            max-width: 700px;
            margin: 0 auto;
            padding: 2rem;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 255, 136, 0.2);
            border: 1px solid rgba(0, 255, 136, 0.3);
        }
        .create-member-container h2 {
            color: #00ff88;
            margin-bottom: 2rem;
            text-shadow: 0 0 20px rgba(0, 255, 136, 0.5);
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #00ffea;
            font-weight: 600;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(0, 255, 136, 0.3);
            border-radius: 10px;
            color: #fff;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #00ff88;
            box-shadow: 0 0 15px rgba(0, 255, 136, 0.3);
        }
        .dev-mode {
            background: rgba(255, 215, 0, 0.2);
            border: 2px solid rgba(255, 215, 0, 0.5);
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            color: #ffd700;
        }
        .info-box {
            background: rgba(0, 255, 234, 0.1);
            border: 1px solid rgba(0, 255, 234, 0.3);
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            color: #00ffea;
        }
        .btn-submit {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #00ff88, #00ffea);
            border: none;
            border-radius: 10px;
            color: #000;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 0 25px rgba(0, 255, 136, 0.6);
        }
        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 0 35px rgba(0, 255, 136, 0.9);
        }
        .back-link {
            display: inline-block;
            margin-bottom: 2rem;
            color: #00ff88;
            text-decoration: none;
            padding: 10px 20px;
            border: 2px solid rgba(0, 255, 136, 0.5);
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .back-link:hover {
            background: rgba(0, 255, 136, 0.2);
        }
        .message {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 2rem;
            font-weight: 600;
            text-align: center;
        }
        .message.success {
            background: rgba(0, 255, 234, 0.2);
            color: #00ffea;
            border: 2px solid rgba(0, 255, 234, 0.5);
        }
        .message.error {
            background: rgba(255, 51, 92, 0.2);
            color: #ff335c;
            border: 2px solid rgba(255, 51, 92, 0.5);
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <div style="display: flex; align-items: center; gap: 10px;">
                <img src="../../frontoffice/assests/logo.png" alt="Logo GameHub Pro" style="width: 50px; height: 50px;">
                <h1 class="logo">GameHub Pro</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="../../frontoffice/index.php" class="super-button">Home</a></li>
                    <li><a href="../../frontoffice/collaborations.php" class="super-button">Collaborations</a></li>
                    <li><a href="collaboration.php" class="super-button">Gestion Collab</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <a href="view_collab.php?id=<?php echo $collab_id; ?>" class="back-link">← Retour à la Collaboration</a>
            
            <div class="create-member-container">
                <h2>➕ Ajouter un membre</h2>

                <?php if (!empty($message)): ?>
                    <div class="message <?php echo $messageType; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <?php if (!$isLoggedIn): ?>
                    <div class="dev-mode">
                        ⚠️ <strong>Mode développeur</strong> : Vous n'êtes pas connecté. Vous pouvez ajouter un membre.
                    </div>
                <?php endif; ?>

                <div class="info-box">
                    <b>Collaboration :</b> <?php echo htmlspecialchars($collab['titre']); ?><br>
                    <b>Membres actuels :</b> <?php echo count($currentMembers); ?> / <?php echo $collab['max_membres']; ?><br>
                    <b>Places disponibles :</b> <?php echo $remainingSlots; ?>
                </div>

                <?php if ($remainingSlots <= 0): ?>
                    <div class="message error">
                        ❌ Ce projet a atteint le nombre maximum de membres. Vous ne pouvez pas ajouter de nouveaux membres.
                    </div>
                <?php else: ?>
                    <form id="createMemberForm" method="POST">
                        <input type="hidden" name="collab_id" value="<?php echo $collab_id; ?>">

                        <div class="form-group">
                            <label for="user_id">ID Utilisateur *</label>
                            <input type="number" id="user_id" name="user_id" placeholder="Entrez l'ID de l'utilisateur à ajouter">
                        </div>

                        <div class="form-group">
                            <label for="role">Rôle *</label>
                            <select id="role" name="role">
                                <option value="membre" selected>Membre</option>
                                <option value="moderateur">Modérateur</option>
                            </select>
                        </div>

                        <button type="submit" class="btn-submit">➕ Ajouter le membre</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <script src="validation.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const validator = new FormValidator('createMemberForm');
            
            // Validation pour user_id
            validator.addRule('user_id', {
                required: true,
                min: 1
            }, 'ID utilisateur requis (minimum 1)');
            
            // Validation pour role
            validator.addRule('role', {
                required: true
            }, 'Rôle requis');
        });
    </script>

</body>
</html>

