<?php

// controllers/ArticleController.php



// Démarrer la session pour stocker les erreurs de validation et les messages de succès

if (session_status() === PHP_SESSION_NONE) {

    session_start();

}



// Inclusions des dépendances

require_once '../models/Article.php';

require_once '../models/Comment.php';

require_once '../models/CommentShare.php';

require_once '../models/User.php';

require_once '../models/Database.php';



class ArticleController {

    private $db;

    private $articleModel;

    private $commentShareModel;



    public function __construct(PDO $db) {

        $this->db = $db;

        $this->articleModel = new Article($this->db);

        $this->commentShareModel = new CommentShare($this->db);

    }

   

    /**

     * Vérifie si l'ID utilisateur existe dans la table users.

     */

    private function userExists(int $user_id): bool {

        // Cette vérification est toujours utile comme ultime garde-fou

        $query = "SELECT id_user FROM users WHERE id_user = :id LIMIT 1";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount() > 0;

    }





    // Affiche la liste des articles (Front Office).

    public function list() {

        if (isset($_SESSION['searched_articles'])) {

            $articles = $_SESSION['searched_articles'];

            unset($_SESSION['searched_articles']);

        } else {

            $articles = $this->articleModel->readAll();

        }

       

        $commentModel = new Comment($this->db);

       

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



        $commentModel = new Comment($this->db);

        $comments = $commentModel->readByArticleId($id);



        // ✅ CORRECTION : Utilisation de l'ID de session si disponible

        $current_user_id = $_SESSION['id_user'] ?? 1;



        // Récupérer la liste des utilisateurs pour le partage

        $userModel = new User($this->db);

        $recipients = $userModel->readAllUsers($current_user_id); // Exclut l'utilisateur actuel

       

        include '../views/article/show.php';

    }



    // Affiche le tableau de bord d'administration (Back Office).

    public function dashboard() {

        $stats = [

            'totalArticles' => $this->articleModel->countTotalArticles(),

            'totalComments' => $this->articleModel->countTotalComments(),

            'uniqueAuthors' => $this->articleModel->countUniqueAuthors(),

            'publishedToday' => $this->articleModel->countPublishedToday()

        ];

       

        $articles = $this->articleModel->readDashboardArticles();

       

        $success = $_SESSION['success'] ?? null;

        unset($_SESSION['success']);

       

        include '../views/article/dashboard.php';

    }

   

    // Affiche le formulaire de création (C - Create).

    public function create() {

        include '../views/article/create.php';

    }

   

    // Traite la soumission du formulaire de création (C - Create/Store)

    public function store() {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

             header('Location: ArticleController.php?action=create');

             exit;

        }

       

        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS);

        $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT) ?: 1;

        $imagePath = null;

       

        $errors = [];

       

        // 1. Validation de base (PHP only)

        if (empty($title)) {

            $errors['title'] = "Le titre est obligatoire.";

        } elseif (strlen($title) > 255) {

             $errors['title'] = "Le titre ne doit pas dépasser 255 caractères.";

        }

       

        if (empty($content)) {

            $errors['content'] = "Le contenu est obligatoire.";

        } elseif (strlen($content) < 50) {

             $errors['content'] = "Le contenu doit contenir au moins 50 caractères.";

        }



        // 2. Traitement et validation de l'image (PHP only)

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

            $file = $_FILES['image'];

            $uploadDir = __DIR__ . '/../public/uploads/';

           

            if (!is_dir($uploadDir)) {

                mkdir($uploadDir, 0777, true);

            }



            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

            $maxSize = 5 * 1024 * 1024; // 5MB



            if (!in_array($file['type'], $allowedTypes)) {

                $errors['image'] = "Type de fichier non autorisé. Seuls JPG, PNG et GIF sont acceptés.";

            }

            if ($file['size'] > $maxSize) {

                $errors['image'] = "Le fichier est trop volumineux (max 5MB).";

            }

           

            if (!isset($errors['image'])) {

                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);

                $newFileName = uniqid() . '.' . $extension;

                $destination = $uploadDir . $newFileName;

               

                if (move_uploaded_file($file['tmp_name'], $destination)) {

                    $imagePath = 'public/uploads/' . $newFileName;

                } else {

                    $errors['image'] = "Erreur interne lors du déplacement du fichier.";

                }

            }

        } elseif (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {

             $errors['image'] = "Erreur lors du téléchargement de l'image (Code: " . $_FILES['image']['error'] . ").";

        }





        // 3. Gestion de l'échec de la validation

        if (count($errors) > 0) {

            $_SESSION['article_errors'] = $errors;

            $_SESSION['article_input'] = $_POST;

            $_SESSION['error'] = "Erreur de validation. Veuillez corriger les champs.";

           

            header('Location: ArticleController.php?action=create');

            exit;

        }

       

        // 4. Succès: Création de l'article

        if ($this->articleModel->create($title, $content, $user_id, $imagePath)) {

            $_SESSION['success'] = "✅ NOUVEL ARTICLE! L'article '{$title}' vient d'être publié" . ($imagePath ? " avec une photo ! " : ".") . " Découvrez-le !";

        } else {

            $_SESSION['error'] = "Erreur lors de la création de l'article dans la base de données.";

            if ($imagePath && file_exists(__DIR__ . '/../' . $imagePath)) {

                unlink(__DIR__ . '/../' . $imagePath);

            }

        }

       

        header('Location: ArticleController.php?action=list');

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

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

             header('Location: ArticleController.php?action=dashboard');

             exit;

        }

       

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_SPECIAL_CHARS);

        $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $user_id = 1;

       

        if (!$id) {

            $_SESSION['success'] = "Erreur: ID de l'article à modifier est invalide.";

            header('Location: ArticleController.php?action=dashboard');

            exit;

        }

       

        $errors = [];

       

        // 1. Validation de base (PHP only)

        if (empty($title)) {

            $errors['title'] = "Le titre est obligatoire.";

        } elseif (strlen($title) > 255) {

             $errors['title'] = "Le titre ne doit pas dépasser 255 caractères.";

        }

       

        if (empty($content)) {

            $errors['content'] = "Le contenu est obligatoire.";

        } elseif (strlen($content) < 50) {

             $errors['content'] = "Le contenu doit contenir au moins 50 caractères.";

        }

       

        // 2. Gestion de l'échec de la validation

        if (count($errors) > 0) {

            $_SESSION['article_errors'] = $errors;

            $_SESSION['article_input'] = $_POST;

            $_SESSION['error'] = "Erreur de validation. Veuillez corriger les champs.";

           

            header("Location: ArticleController.php?action=edit&id=" . $id);

            exit;

        }

       

        // 3. Succès: Mise à jour

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

            $article = $this->articleModel->readOne($id);



            $commentModel = new Comment($this->db);

            // Supprimer d'abord les partages liés au commentaire pour éviter les erreurs FK

            $this->commentShareModel->deleteSharesByArticleId($id);

            $commentModel->deleteByArticleId($id);

           

            if ($this->articleModel->delete($id)) {

                // Supprimer le fichier image

                if (!empty($article['image_path']) && file_exists(__DIR__ . '/../' . $article['image_path'])) {

                    unlink(__DIR__ . '/../' . $article['image_path']);

                }

                $_SESSION['success'] = "L'article ID {$id} a été supprimé avec succès.";

            } else {

                $_SESSION['error'] = "Erreur lors de la suppression de l'article ID {$id}.";

            }

        }

       

        header('Location: ArticleController.php?action=dashboard');

        exit;

    }

   

    // Metier 2 : Traite le formulaire de tri par date (Recherche).

    public function searchByDate() {

        $search_date = filter_input(INPUT_GET, 'search_date', FILTER_SANITIZE_FULL_SPECIAL_CHARS);



        // CONTRÔLE DE SAISIE CÔTÉ SERVEUR (PHP only)

        if (empty($search_date)) {

            $_SESSION['error'] = "Veuillez entrer une date pour effectuer la recherche.";

        } elseif (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $search_date)) {

             $_SESSION['error'] = "Erreur de format de date. Utilisez le format AAAA-MM-JJ (ex: 2024-01-30).";

        }

       

        if (isset($_SESSION['error'])) {

            header('Location: ArticleController.php?action=list');

            exit;

        }



        $articles = $this->articleModel->readByDate($search_date);

       

        if (empty($articles)) {

             $_SESSION['error'] = "Aucun article trouvé pour la date du " . htmlspecialchars($search_date) . ".";

        } else {

            $_SESSION['searched_articles'] = $articles;

            $_SESSION['success'] = "Affichage des articles pour la date du " . htmlspecialchars($search_date) . ".";

        }

       

        header('Location: ArticleController.php?action=list');

        exit;

    }



    // Metier Avancé : Affiche la liste des commentaires partagés (pour l'utilisateur ID 1)

    public function sharedComments() {

        // ✅ CORRECTION (329) : Renommage de la variable pour la cohérence

        $id_user_destinataire = $_SESSION['id_user'] ?? 1;



        $sharedComments = $this->commentShareModel->getSharedCommentsForUser($id_user_destinataire);

       

        $success = $_SESSION['success'] ?? null;

        unset($_SESSION['success']);

       

        include '../views/comment/shared.php';

    }



    // Metier Avancé : Traite la soumission du formulaire de partage de commentaire

    public function shareComment() {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

            header('Location: ArticleController.php?action=list');

            exit;

        }

       

        // RAPPEL BD : id_commentaire, id_user_emetteur, id_user_destinataire

       

        // Récupération des ID du POST

        $id_commentaire = filter_input(INPUT_POST, 'comment_id', FILTER_VALIDATE_INT); // ID Commentaire

        $id_user_destinataire = filter_input(INPUT_POST, 'recipient_id', FILTER_VALIDATE_INT); // ID Destinataire

        $article_id = filter_input(INPUT_POST, 'article_id', FILTER_VALIDATE_INT);

        $recipient_input = $_POST['recipient_id'] ?? null;

       

        // ID Émetteur (l'utilisateur connecté)

        $id_user_emetteur = $_SESSION['id_user'] ?? 1;

       

        $errors = [];

       

        // 1. Validation PHP (ID destinataire)

        if (!$id_user_destinataire) {

             if (empty($recipient_input)) {

                 $errors['recipient_id'] = "L'ID du destinataire est obligatoire.";

             } else {

                 $errors['recipient_id'] = "L'ID du destinataire doit être un nombre entier valide.";

             }

        } elseif ($id_user_emetteur === $id_user_destinataire) {

             $errors['recipient_id'] = "Vous ne pouvez pas vous partager un commentaire.";

        }

        // VÉRIFICATION : L'utilisateur destinataire existe-t-il ?

        elseif (!$this->userExists($id_user_destinataire)) {

             $errors['recipient_id'] = "L'utilisateur ID {$id_user_destinataire} n'existe pas. Partage impossible.";

        }

       

        // 2. Validation PHP (ID commentaire/article)

        if (!$id_commentaire || !$article_id) {

            $errors['general'] = "Erreur: ID de commentaire ou d'article invalide.";

        }



        if (!empty($errors)) {

            // Affichage de l'erreur la plus pertinente

            $_SESSION['error'] = $errors['general'] ?? $errors['recipient_id'] ?? "Erreur de validation.";

        } else {

            // Tentative de partage

            // ✅ CORRECTION (Ligne 379 dans la trace) : Utilisation des variables dans l'ordre du modèle (id_commentaire, id_user_emetteur, id_user_destinataire)

            if ($this->commentShareModel->shareComment($id_commentaire, $id_user_emetteur, $id_user_destinataire)) {

                $_SESSION['success'] = "Le commentaire a été partagé avec succès à l'utilisateur ID {$id_user_destinataire}.";

            } else {

                $_SESSION['error'] = "Erreur lors de l'enregistrement du partage en base de données (peut-être déjà partagé).";

            }

        }



        // Redirection vers l'article du commentaire

        if ($article_id) {

            header("Location: ArticleController.php?action=show&id=" . $article_id);

        } else {

            header('Location: ArticleController.php?action=list');

        }

        exit;

    }

}



// -------------------------------------------------------------



## ROUTAGE ET INITIALISATION



try {

    $db = Database::getInstance()->getConnection();



    if (!$db instanceof PDO) {

        throw new Exception("L'instance de connexion à la base de données (PDO) n'est pas disponible.");

    }

   

    $controller = new ArticleController($db);

   

} catch (Exception $e) {

    http_response_code(500);

    echo "<h1>Erreur critique de connexion</h1>";

    echo "<p>Veuillez vérifier vos paramètres de base de données. Message d'erreur : " . htmlspecialchars($e->getMessage()) . "</p>";

    exit;

}



// Détermination de l'action à exécuter

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

   

    case 'searchByDate':

        $controller->searchByDate();

        break;

       

    case 'sharedComments':

        $controller->sharedComments();

        break;

       

    case 'shareComment':

        $controller->shareComment();

        break;

       

    case 'list':

    default:

        $controller->list();

        break;

}