// controllers/ArticleController.php (CODE COMPLET AVEC ACTION show)

<?php
// Démarrer la session pour stocker les erreurs de validation et les messages de succès
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../models/Article.php';

class ArticleController {
    private $articleModel;

    public function __construct() {
        $this->articleModel = new Article();
    }

    // Affiche la liste des articles (Front Office).
    public function index() {
        $articles = $this->articleModel->readAll();
        include '../views/article/list.php';
    }

    // Affiche l'article unique (Front Office).
    public function show() {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        
        if (!$id) {
            // Pas d'ID valide, rediriger vers la liste
            header('Location: ArticleController.php?action=list');
            exit;
        }

        // Utilise la méthode readOne que nous avons déjà créée pour l'édition
        $article = $this->articleModel->readOne($id);
        
        if (!$article) {
             // Article non trouvé
             $_SESSION['article_error'] = "L'article demandé n'existe pas.";
             header('Location: ArticleController.php?action=list');
             exit;
        }
        
        include '../views/article/show.php';
    }


    // Affiche le tableau de bord de gestion (Back Office).
    public function dashboard() {
        $articles = $this->articleModel->readDashboardArticles();
        
        $totalArticles = count($articles);
        $totalAuthors = count(array_unique(array_column($articles, 'author_name')));
        $successMessage = $_SESSION['success'] ?? null; 
        unset($_SESSION['success']);
        
        include '../views/article/dashboard.php';
    }

    // Affiche le formulaire de création (C).
    public function create() {
        include '../views/article/create.php';
    }

    // Traite la soumission du formulaire de création (C).
    public function store() {
        $errors = [];
        $input = $_POST;
        // VALIDATION EN COURS...
        if (empty($input['title'])) { $errors['title'] = "Le titre est obligatoire."; } elseif (strlen($input['title']) > 255) { $errors['title'] = "Le titre ne doit pas dépasser 255 caractères."; }
        if (empty($input['content'])) { $errors['content'] = "Le contenu est obligatoire."; } elseif (strlen($input['content']) < 50) { $errors['content'] = "Le contenu doit faire au moins 50 caractères."; }
        if (empty($input['user_id']) || !is_numeric($input['user_id'])) { $errors['user_id'] = "L'ID de l'auteur est invalide."; }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['input'] = $input;
            header('Location: ArticleController.php?action=create');
            exit;
        } 
        
        if ($this->articleModel->create($input['title'], $input['content'], (int)$input['user_id'])) {
            $_SESSION['success'] = "L'article '{$input['title']}' a été créé avec succès!";
        } else {
            $_SESSION['success'] = "Une erreur est survenue lors de la création.";
        }
        
        header('Location: ArticleController.php?action=dashboard');
        exit;
    }

    // Affiche le formulaire de modification (U - GET).
    public function edit() {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) { header('Location: ArticleController.php?action=dashboard'); exit; }
        $article = $this->articleModel->readOne($id);
        if (!$article) {
             $_SESSION['success'] = "Erreur: L'article demandé n'existe pas.";
             header('Location: ArticleController.php?action=dashboard');
             exit;
        }
        include '../views/article/edit.php';
    }

    // Traite la soumission du formulaire de modification (U - POST).
    public function update() {
        $errors = [];
        $input = $_POST;
        $id = filter_var($input['id'] ?? null, FILTER_VALIDATE_INT);
        // VALIDATION EN COURS...
        if (!$id) { $_SESSION['success'] = "Erreur: ID de l'article est invalide."; header('Location: ArticleController.php?action=dashboard'); exit; }
        if (empty($input['title'])) { $errors['title'] = "Le titre est obligatoire."; } elseif (strlen($input['title']) > 255) { $errors['title'] = "Le titre ne doit pas dépasser 255 caractères."; }
        if (empty($input['content'])) { $errors['content'] = "Le contenu est obligatoire."; } elseif (strlen($input['content']) < 50) { $errors['content'] = "Le contenu doit faire au moins 50 caractères."; }
        if (empty($input['user_id']) || !is_numeric($input['user_id'])) { $errors['user_id'] = "L'ID de l'auteur est invalide."; }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['input'] = $input; 
            header("Location: ArticleController.php?action=edit&id={$id}");
            exit;
        } 
        
        if ($this->articleModel->update($id, $input['title'], $input['content'], (int)$input['user_id'])) {
            $_SESSION['success'] = "L'article '{$input['title']}' a été mis à jour avec succès!";
        } else {
            $_SESSION['success'] = "Aucune modification n'a été faite ou une erreur est survenue.";
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

// =========================================================
// ROUTAGE (Mise à jour pour inclure show)
// =========================================================

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
        
    case 'show': // Nouvelle action!
        $controller->show();
        break;
        
    case 'list':
    default:
        $controller->index();
        break;
}