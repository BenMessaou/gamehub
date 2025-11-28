<?php
// controllers/CommentController.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../models/Comment.php';
require_once '../models/Article.php'; // Nécessaire pour les redirections

class CommentController {
    private $commentModel;

    public function __construct() {
        $this->commentModel = new Comment();
    }

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
        
        $redirect_url = 'ArticleController.php?action=show&id=' . ($article_id ?: 0);

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
            header('Location: ArticleController.php?action=list');
            exit;
        }

        $comment = $this->commentModel->readOne($id);
        
        if (!$comment) {
             $_SESSION['error'] = "Le commentaire demandé n'existe pas.";
             header('Location: ArticleController.php?action=list');
             exit;
        }
        
        $article_id = $comment['article_id']; 
        
        // Le chemin inclut views/comment/edit.php (fourni ci-dessous)
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
        
        $redirect_url = 'ArticleController.php?action=show&id=' . ($article_id ?: 0);

        if (count($errors) > 0) {
            $_SESSION['comment_errors'] = $errors;
            $_SESSION['comment_input'] = $_POST;
            $_SESSION['error'] = "Erreur de validation lors de la modification.";
            // Redirection vers le formulaire d'édition
            header('Location: CommentController.php?action=edit&id=' . ($id ?: 0));
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
        $redirect_url = 'CommentController.php?action=index'; 

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
                $redirect_url = 'ArticleController.php?action=show&id=' . $article_id;
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

// ROUTAGE
$controller = new CommentController();
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