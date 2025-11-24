<?php
// controllers/CommentController.php (MODIFIÉ)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../models/Comment.php';

class CommentController {
    private $commentModel;

    public function __construct() {
        $this->commentModel = new Comment();
    }

    // Affiche la liste de tous les commentaires pour modération (CRUD R)
    public function index() {
        $comments = $this->commentModel->readAllComments();
        
        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['success']); 
        
        // Inclure la vue pour le back-office (modération)
        include '../views/comment/index.php'; 
    }
    
    /**
     * C - Traite la soumission du formulaire de commentaire (Front Office). 
     */
    public function store() {
        $article_id = filter_input(INPUT_POST, 'article_id', FILTER_VALIDATE_INT);
        $content = filter_input(INPUT_POST, 'content'); 
        
        $errors = [];
        
        $_SESSION['comment_input'] = [
            'article_id' => $article_id,
            'content' => $content
        ];

        if (!$article_id) {
            $errors['article_id'] = "L'ID de l'article est manquant ou invalide.";
        }
        if (empty(trim($content))) {
            $errors['content'] = "Le contenu du commentaire ne peut pas être vide.";
        }
        
        if (!empty($errors)) {
            $_SESSION['comment_errors'] = $errors;
            $_SESSION['error'] = "Erreur: Le commentaire n'a pas pu être posté. Veuillez corriger les erreurs.";
            header("Location: ArticleController.php?action=show&id={$article_id}");
            exit;
        }

        if ($this->commentModel->create($content, $article_id)) {
            $_SESSION['success'] = "Votre commentaire a été posté avec succès !";
        } else {
            $_SESSION['error'] = "Erreur: Une erreur s'est produite lors de l'enregistrement du commentaire.";
        }
        
        // Redirection vers l'article (ajustez si besoin de l'ancre #comments)
        header("Location: ArticleController.php?action=show&id={$article_id}");
        exit;
    }
    
    // Supprime un commentaire (CRUD D)
    public function delete() {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $_SESSION['success'] = "Erreur: ID du commentaire à supprimer est invalide.";
        } else {
            if ($this->commentModel->delete($id)) {
                $_SESSION['success'] = "Le commentaire ID {$id} a été supprimé avec succès.";
            } else {
                $_SESSION['success'] = "Erreur lors de la suppression du commentaire ID {$id}.";
            }
        }
        
        header('Location: CommentController.php?action=index'); 
        exit;
    }
}

// ROUTAGE
$controller = new CommentController();
$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'index':
        $controller->index();
        break;
        
    case 'delete':
        $controller->delete();
        break;
        
    case 'store': 
        $controller->store();
        break;
        
    default:
        $controller->index();
        break;
}