<?php
// controllers/ArticleController.php (MODIFIÉ)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../models/Article.php';
require_once '../models/Comment.php'; 

class ArticleController {
    private $articleModel;
    private $commentModel;

    public function __construct() {
        $this->articleModel = new Article();
        $this->commentModel = new Comment(); 
    }

    // Affiche la liste des articles (Front Office).
    public function index() {
        $articles = $this->articleModel->readAll();
        include '../views/article/list.php';
    }

    // Affiche l'article unique et ses commentaires (Front Office).
    public function show() {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        
        if (!$id) {
            $_SESSION['article_error'] = "ID d'article manquant ou invalide.";
            header('Location: ArticleController.php?action=list');
            exit;
        }

        $article = $this->articleModel->readOne($id);
        
        if (!$article) {
             $_SESSION['article_error'] = "L'article demandé n'existe pas.";
             header('Location: ArticleController.php?action=list');
             exit;
        }
        
        // CHARGEMENT DES COMMENTAIRES
        $comments = $this->commentModel->readByArticleId($id); 
        
        include '../views/article/show.php';
    }

    // Affiche le tableau de bord d'administration (Back Office).
    public function dashboard() {
        // Chargement des données statistiques
        $totalArticles = $this->articleModel->countTotalArticles();
        $uniqueAuthors = $this->articleModel->countUniqueAuthors();
        $publishedToday = $this->articleModel->countPublishedToday();
        $totalComments = $this->articleModel->countTotalComments(); // Nouvelle stat

        // Chargement de la liste des articles
        $articles = $this->articleModel->readDashboardArticles();
        
        // Récupération des messages de session (success/error)
        $success = $_SESSION['success'] ?? null;
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['success'], $_SESSION['error']);
        
        include '../views/article/dashboard.php';
    }

    // Affiche le formulaire de création
    public function create() {
        include '../views/article/create.php';
    }

    // Traite la soumission du formulaire de création (C - Create)
    public function store() {
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
        $content = $_POST['content'] ?? ''; // Laisser le contenu brut
        $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
        
        $errors = [];
        if (empty(trim($title))) $errors['title'] = "Le titre est obligatoire.";
        if (empty(trim($content))) $errors['content'] = "Le contenu est obligatoire.";
        if (!$user_id) $errors['user_id'] = "L'auteur est obligatoire.";
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['input'] = ['title' => $title, 'content' => $content, 'user_id' => $user_id];
            header('Location: ArticleController.php?action=create');
            exit;
        }
        
        if ($this->articleModel->create($title, $content, $user_id)) {
            $_SESSION['success'] = "L'article a été créé avec succès.";
        } else {
            $_SESSION['error'] = "Erreur lors de la création de l'article.";
        }
        
        header('Location: ArticleController.php?action=dashboard');
        exit;
    }

    // Affiche le formulaire d'édition (U - Update)
    public function edit() {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        
        if (!$id) {
            $_SESSION['error'] = "ID invalide pour l'édition.";
            header('Location: ArticleController.php?action=dashboard');
            exit;
        }
        
        $article = $this->articleModel->readOne($id);
        
        if (!$article) {
            $_SESSION['error'] = "Article non trouvé.";
            header('Location: ArticleController.php?action=dashboard');
            exit;
        }

        include '../views/article/edit.php';
    }

    // Traite la soumission du formulaire de mise à jour (U - Update)
    public function update() {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
        $content = $_POST['content'] ?? ''; 
        $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
        
        $errors = [];
        if (!$id) $errors['id'] = "ID d'article manquant.";
        if (empty(trim($title))) $errors['title'] = "Le titre est obligatoire.";
        if (empty(trim($content))) $errors['content'] = "Le contenu est obligatoire.";
        if (!$user_id) $errors['user_id'] = "L'auteur est obligatoire.";
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['input'] = ['id' => $id, 'title' => $title, 'content' => $content, 'user_id' => $user_id];
            header("Location: ArticleController.php?action=edit&id={$id}");
            exit;
        }
        
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
            $_SESSION['success'] = "Erreur: ID de l'article à supprimer est invalide.";
        } else {
            if ($this->articleModel->delete($id)) {
                $_SESSION['success'] = "L'article ID {$id} a été supprimé avec succès.";
            } else {
                $_SESSION['success'] = "Erreur lors de la suppression de l'article ID {$id}.";
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
        
    case 'list': 
    default:
        $controller->index();
        break;
}