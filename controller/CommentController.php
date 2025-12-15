<?php
// controllers/CommentController.php (VERSION CORRIGÉE AVEC INJECTION DE DÉPENDANCE)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../model/Comment.php';
// require_once '../model/Article.php'; // Utile si vous avez besoin des méthodes de Article ici, sinon pas obligatoire

class CommentController {
    
    private $db;
    private $commentModel;

    /**
     * CONSTRUCTEUR CORRIGÉ : Accepte l'injection de dépendance PDO.
     * @param PDO $db La connexion à la base de données.
     */
    public function __construct(PDO $db) {
        $this->db = $db;
        // Instanciation du modèle en lui passant la connexion PDO
        $this->commentModel = new Comment($this->db); 
    }
    
    // --- ACTIONS DU CONTRÔLEUR ---
    
    /**
     * Affiche la liste de tous les commentaires pour modération (CRUD R - Back Office).
     */
    public function index() {
        $comments = $this->commentModel->readAllComments();
        
        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['success']); 
        
        // La vue pour la modération (views/comment/index.php)
        include '../views/comment/index.php'; 
    }
    
    /**
     * Traite la soumission du formulaire de création de commentaire (CRUD C - Store).
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
             header('Location: ../views/article/list.php');
             exit;
        }
        
        $user_id = 1; // ID utilisateur temporaire (à adapter à votre système d'authentification)

        $article_id = filter_input(INPUT_POST, 'article_id', FILTER_VALIDATE_INT);
        $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $errors = [];
        
        if (!$article_id) {
            $errors['article_id'] = "L'article est manquant.";
        }
        // Validation PHP (Contrôle de saisie)
        if (empty($content)) {
            $errors['content'] = "Le commentaire ne peut pas être vide.";
        } elseif (strlen($content) < 10) { 
            $errors['content'] = "Le commentaire doit contenir au moins 10 caractères.";
        }

        $redirect_url = '../views/article/show.php?id=' . ($article_id ?: 0);

        if (count($errors) > 0) {
            $_SESSION['comment_errors'] = $errors;
            $_SESSION['comment_input'] = $_POST;
            $_SESSION['error'] = "Erreur de validation. Veuillez corriger le commentaire.";
            header('Location: ' . $redirect_url);
            exit;
        }

        if ($this->commentModel->create($content, $article_id, $user_id)) {
            $_SESSION['success'] = "Votre commentaire a été posté avec succès.";
        } else {
            $_SESSION['error'] = "Erreur lors de la publication du commentaire.";
        }
        
        header('Location: ' . $redirect_url);
        exit;
    }

    /**
     * Affiche le formulaire de modification d'un commentaire (CRUD U - Edit).
     */
    public function edit() {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        
        if (!$id) {
            $_SESSION['error'] = "ID de commentaire invalide.";
            header('Location: ../views/article/list.php');
            exit;
        }

        $comment = $this->commentModel->readOne($id);
        
        if (!$comment) {
             $_SESSION['error'] = "Le commentaire demandé n'existe pas.";
             header('Location: ../views/article/list.php');
             exit;
        }
        
        $article_id = $comment['article_id']; 
        
        // Le chemin inclut views/comment/edit.php
        include '../views/comment/edit.php'; 
        exit;
    }

    /**
     * Traite la soumission du formulaire de modification (CRUD U - Update).
     */
    public function update() {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $article_id = filter_input(INPUT_POST, 'article_id', FILTER_VALIDATE_INT);
        $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $errors = [];
        
        if (!$id || !$article_id) {
            $errors['general'] = "ID manquant pour le commentaire ou l'article.";
        }
        // Validation PHP
        if (empty($content)) {
            $errors['content'] = "Le commentaire ne peut pas être vide.";
        } elseif (strlen($content) < 10) { 
            $errors['content'] = "Le commentaire doit contenir au moins 10 caractères.";
        }

        $redirect_url = '../views/article/show.php?id=' . ($article_id ?: 0);

        if (count($errors) > 0) {
            $_SESSION['comment_errors'] = $errors;
            $_SESSION['comment_input'] = $_POST;
            $_SESSION['error'] = "Erreur de validation lors de la modification.";
            // Redirection vers le formulaire d'édition
            header('Location: ../views/comment/edit.php?id=' . ($id ?: 0));
            exit;
        }

        if ($this->commentModel->update($id, $content)) {
            $_SESSION['success'] = "Le commentaire ID {$id} a été mis à jour avec succès.";
        } else {
            $_SESSION['error'] = "Erreur lors de la mise à jour du commentaire ID {$id}.";
        }
        
        // Redirection vers l'article
        header('Location: ' . $redirect_url);
        exit;
    }
    
    /**
     * Supprime un commentaire (CRUD D).
     */
    public function delete() {
        // Accepte l'ID depuis GET (Dashboard) ou POST (Page Article)
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) 
             ?: filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT); 

        // Accepte l'ID de l'article depuis GET ou POST pour la redirection
        $article_id = filter_input(INPUT_GET, 'article_id', FILTER_VALIDATE_INT) 
                      ?: filter_input(INPUT_POST, 'article_id', FILTER_VALIDATE_INT); 
        
        // Redirection par défaut (Dashboard modération)
        $redirect_url = '../views/comment/index.php'; 

        if (!$id) {
            $_SESSION['error'] = "Erreur: ID du commentaire à supprimer est invalide.";
        } else {
            // Si l'on ne connaît pas l'article_id, on le cherche pour pouvoir rediriger sur la page de l'article (si nécessaire)
            if (!$article_id) {
                $comment = $this->commentModel->readOne($id);
                if ($comment) {
                    $article_id = $comment['article_id'];
                }
            }

            // Si l'article_id est connu, on redirige vers l'article (Front Office), sinon vers le Dashboard (Back Office)
            if ($article_id) {
                $redirect_url = '../views/article/show.php?id=' . $article_id;
            }

            if ($this->commentModel->delete($id)) {
                $_SESSION['success'] = "Le commentaire ID {$id} a été supprimé avec succès.";
            } else {
                $_SESSION['error'] = "Erreur lors de la suppression du commentaire ID {$id}.";
            }
        }
        
        header('Location: ' . $redirect_url); 
        exit;
    }
}

// -------------------------------------------------------------

## ROUTAGE ET INITIALISATION

// ⚠️ INCLUSION DE LA CLASSE DATABASE POUR L'INITIALISATION
require_once '../model/Database.php'; 

try {
    // ⭐️ Récupération de l'instance PDO via votre classe Singleton
    $db = Database::getInstance()->getConnection(); 

    if (!$db instanceof PDO) {
        throw new Exception("L'instance de connexion à la base de données (PDO) n'est pas disponible.");
    }
    
    // ⭐️ Instanciation du Contrôleur en lui PASSANT LA CONNEXION PDO
    $controller = new CommentController($db);
    
} catch (Exception $e) {
    // Gestion des erreurs de connexion (important)
    http_response_code(500);
    echo "<h1>Erreur critique de connexion</h1>";
    echo "<p>Veuillez vérifier vos paramètres de base de données. Message d'erreur : " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}

// Détermination de l'action à exécuter
$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'store':
        $controller->store();
        break;

    case 'edit':
        $controller->edit();
        break;
        
    case 'update':
        $controller->update();
        break;
        
    case 'delete':
        $controller->delete();
        break;
        
    case 'index': 
    default:
        $controller->index();
        break;
}