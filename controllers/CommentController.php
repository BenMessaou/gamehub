<?php
// controllers/CommentController.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../models/Comment.php';

class CommentController {
    private $commentModel;

    public function __construct() {
        $this->commentModel = new Comment();
    }
    
    // Traite la soumission du formulaire de commentaire (C - Create/Store)
    public function store() {
        $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $article_id = filter_input(INPUT_GET, 'article_id', FILTER_VALIDATE_INT);
        $user_id = 1; // ID utilisateur temporaire (à adapter avec un système d'authentification)

        if (!$article_id) {
            $_SESSION['error'] = "Erreur: ID de l'article manquant.";
            header('Location: ArticleController.php?action=list');
            exit;
        }

        $errors = [];
        
        // 1. Validation : Contenu OBLIGATOIRE et MAX 50
        if (empty($content)) {
            $errors['content'] = "Le contenu du commentaire est obligatoire.";
        } elseif (strlen($content) > 50) {
             $errors['content'] = "Le commentaire est limité à 50 caractères.";
        }

        if (count($errors) > 0) {
            // Échec de la validation: Enregistre les erreurs et les données saisies
            $_SESSION['comment_errors'] = $errors;
            $_SESSION['comment_input'] = $_POST;
            $_SESSION['error'] = "Erreur de validation du commentaire. Veuillez corriger.";
            // Redirection vers l'article
            header('Location: ArticleController.php?action=show&id=' . $article_id);
            exit;
        }

        // Succès: Appel au modèle
        if ($this->commentModel->create($content, $article_id, $user_id)) {
            $_SESSION['success'] = "Votre commentaire a été posté avec succès.";
        } else {
            $_SESSION['error'] = "Erreur lors de la publication du commentaire.";
        }

        // Redirection vers l'article
        header('Location: ArticleController.php?action=show&id=' . $article_id);
        exit;
    }

    // Affiche la liste de tous les commentaires pour modération (CRUD R)
    public function index() {
        $comments = $this->commentModel->readAllComments();
        
        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['success']); 
        
        include '../views/comment/index.php'; 
    }
    
    // Supprime un commentaire (CRUD D)
    public function delete() {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $_SESSION['error'] = "Erreur: ID du commentaire à supprimer est invalide.";
        } else {
            if ($this->commentModel->delete($id)) {
                $_SESSION['success'] = "Le commentaire ID {$id} a été supprimé avec succès.";
            } else {
                $_SESSION['error'] = "Erreur lors de la suppression du commentaire ID {$id}.";
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
    case 'store':
        $controller->store();
        break;

    case 'delete':
        $controller->delete();
        break;
        
    case 'index':
    default:
        $controller->index();
        break;
}