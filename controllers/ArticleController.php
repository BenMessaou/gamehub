<?php
// ArticleController.php
// Démarrer la session pour stocker les erreurs de validation et les messages de succès
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../models/Article.php';
require_once '../models/Comment.php'; 

class ArticleController {
    private $articleModel;
    // private $commentModel; // Non nécessaire si on l'instancie uniquement dans delete() et show()

    public function __construct() {
        $this->articleModel = new Article();
    }

    // Affiche la liste des articles (Front Office).
    public function list() {
        $articles = $this->articleModel->readAll();
        // Le modèle Comment est nécessaire pour lire les commentaires dans show.php
        $commentModel = new Comment(); 
        
        // La vue list.php est maintenant la fonction index()
        include '../views/article/list.php';
    }

    // Affiche l'article unique (Front Office).
    public function show() {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        
        if (!$id) {
            header('Location: ArticleController.php?action=list');
            exit;
        }

        $article = $this->articleModel->readOne($id);
        
        if (!$article) {
             $_SESSION['article_error'] = "L'article demandé n'existe pas.";
             header('Location: ArticleController.php?action=list');
             exit;
        }

        // Récupérer les commentaires associés
        $commentModel = new Comment();
        $comments = $commentModel->readByArticleId($id);
        
        include '../views/article/show.php';
    }

    // Affiche le tableau de bord d'administration (Back Office).
    public function dashboard() {
        // Chargement des données statistiques
        $stats = [
            'totalArticles' => $this->articleModel->countTotalArticles(),
            'totalComments' => $this->articleModel->countTotalComments(),
            'uniqueAuthors' => $this->articleModel->countUniqueAuthors(),
            'publishedToday' => $this->articleModel->countPublishedToday()
        ];
        
        // Chargement des articles pour la table
        $articles = $this->articleModel->readDashboardArticles();
        
        // Récupération et effacement du message de session
        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['success']);
        
        include '../views/article/dashboard.php';
    }
    
    // Affiche le formulaire de création (C - Create).
    public function create() {
        include '../views/article/create.php';
    }
    
    // Traite la soumission du formulaire de création (C - Create/Store).
    public function store() {
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS);
        $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_FULL_SPECIAL_CHARS); // Contenu peut être long
        $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT) ?: 1; // ID temporaire

        $errors = [];
        
        // 1. Validation : Titre OBLIGATOIRE et MAX 255
        if (empty($title)) {
            $errors['title'] = "Le titre est obligatoire.";
        } elseif (strlen($title) > 255) {
             $errors['title'] = "Le titre ne doit pas dépasser 255 caractères.";
        }
        
        // 2. Validation : Contenu OBLIGATOIRE et MIN 50 (selon votre consigne)
        if (empty($content)) {
            $errors['content'] = "Le contenu est obligatoire.";
        } elseif (strlen($content) < 50) { 
             $errors['content'] = "Le contenu doit contenir au moins 50 caractères.";
        }

        if (count($errors) > 0) {
            // Échec: Enregistre les erreurs et les données saisies
            $_SESSION['article_errors'] = $errors;
            $_SESSION['article_input'] = $_POST;
            $_SESSION['error'] = "Erreur de validation. Veuillez corriger les champs.";
            header('Location: ArticleController.php?action=create'); 
            exit;
        }
        
        // Succès: Appel au modèle
        if ($this->articleModel->create($title, $content, $user_id)) {
            $_SESSION['success'] = "L'article '{$title}' a été créé avec succès.";
        } else {
            $_SESSION['error'] = "Erreur lors de la création de l'article.";
        }
        
        header('Location: ArticleController.php?action=dashboard');
        exit;
    }
    
    // Affiche le formulaire d'édition (U - Update).
    public function edit() {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        
        if (!$id) {
            header('Location: ArticleController.php?action=dashboard');
            exit;
        }
        
        $article = $this->articleModel->readOne($id);
        
        if (!$article) {
             $_SESSION['success'] = "L'article à éditer n'existe pas.";
             header('Location: ArticleController.php?action=dashboard');
             exit;
        }
        
        include '../views/article/edit.php';
    }
    
    // Traite la soumission du formulaire d'édition (U - Update).
    public function update() {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS);
        $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_FULL_SPECIAL_CHARS); // Contenu peut être long
        
        // ✅ CORRECTION APPLIQUÉE : L'ID utilisateur est le 4e argument attendu par Article::update()
        $user_id = 1; 
        
        // Si l'ID est invalide, on ne peut pas continuer
        if (!$id) {
            $_SESSION['success'] = "Erreur: ID de l'article à modifier est invalide.";
            header('Location: ArticleController.php?action=dashboard');
            exit;
        }
        
        $errors = [];
        
        // 1. Validation : Titre OBLIGATOIRE et MAX 255
        if (empty($title)) {
            $errors['title'] = "Le titre est obligatoire.";
        } elseif (strlen($title) > 255) {
             $errors['title'] = "Le titre ne doit pas dépasser 255 caractères.";
        }
        
        // 2. Validation : Contenu OBLIGATOIRE et MIN 50 (selon votre consigne)
        if (empty($content)) {
            $errors['content'] = "Le contenu est obligatoire.";
        } elseif (strlen($content) < 50) { 
             $errors['content'] = "Le contenu doit contenir au moins 50 caractères.";
        }
        
        if (count($errors) > 0) {
            // Échec: Enregistre les erreurs et les données saisies
            $_SESSION['article_errors'] = $errors;
            $_SESSION['article_input'] = $_POST;
            $_SESSION['error'] = "Erreur de validation. Veuillez corriger les champs.";
            // Redirection vers le formulaire d'édition avec l'ID
            header("Location: ArticleController.php?action=edit&id=" . $id);
            exit;
        }
        
        // Succès: Appel au modèle
        // 🚨 LIGNE CORRIGÉE : Passage de $id, $title, $content, ET $user_id (4 arguments)
        if ($this->articleModel->update($id, $title, $content, $user_id)) {
            $_SESSION['success'] = "L'article ID {$id} a été mis à jour avec succès.";
        } else {
            $_SESSION['error'] = "Erreur lors de la mise à jour de l'article ID {$id}.";
        }
        
        header('Location: ArticleController.php?action=dashboard');
        exit;
    }
    
    // Supprime un article (D - Delete).
    public function delete() {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if (!$id) {
            $_SESSION['error'] = "Erreur: ID de l'article à supprimer est invalide.";
        } else {
            // ✅ AMÉLIORATION : Suppression des commentaires liés à l'article
            $commentModel = new Comment();
            $commentModel->deleteByArticleId($id); // Assure la suppression en cascade des commentaires

            if ($this->articleModel->delete($id)) {
                $_SESSION['success'] = "L'article ID {$id} a été supprimé avec succès.";
            } else {
                $_SESSION['error'] = "Erreur lors de la suppression de l'article ID {$id}.";
            }
        }
        
        header('Location: ArticleController.php?action=dashboard');
        exit;
    }
}

// ROUTAGE
$controller = new ArticleController();
$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'dashboard':
        $controller->dashboard();
        break;
        
    case 'create':
        $controller->create();
        break;
        
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
        
    case 'show':
        $controller->show();
        break;
        
    case 'list': // Page d'accueil Front Office
    default:
        $controller->list();
        break;
}