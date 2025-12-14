<?php
// controllers/PDFController.php

// Démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclusions des dépendances
require_once '../models/Article.php';
require_once '../models/Database.php';

// 🚀 INCLUSION DE DOMPDF VIA COMPOSER 🚀
// Le fichier autoload.php charge toutes les classes nécessaires (Dompdf, Options, etc.)
require_once '../vendor/autoload.php'; 

use Dompdf\Dompdf;
use Dompdf\Options; 

class PDFController {
    private $db;
    private $articleModel;

    public function __construct(PDO $db) {
        $this->db = $db;
        $this->articleModel = new Article($this->db); 
    }

    /**
     * Génère et force le téléchargement d'un article au format PDF.
     */
    public function downloadArticle() {
        // 1. Récupérer l'ID de l'article
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        
        if (!$id) {
            die("Erreur: ID d'article manquant.");
        }

        // 2. Récupérer les données de l'article (contient 'author_name')
        $article = $this->articleModel->readOne($id);
        
        if (!$article || !isset($article['id'])) {
            die("Erreur: Article non trouvé pour l'ID $id.");
        }

        // 3. Génération du Contenu HTML pour le PDF
        $html = $this->generateArticleHtml($article);

        // 4. Configuration et Génération du PDF
        
        // --- VÉRIFICATION DE DOMPDF ---
        
        // La classe Dompdf est censée exister maintenant grâce à l'autoload.
        if (class_exists(Dompdf::class)) {
            
            // CODE ACTIF DE TÉLÉCHARGEMENT
            $options = new Options();
            $options->set('defaultFont', 'Courier'); 
            // Autorise le chargement des images via URL absolue
            $options->set('isRemoteEnabled', true); 

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            
            // Nom de fichier sécurisé
            // Utilise l'ID de l'article et une version courte du titre
            $title_safe = substr(urlencode($article['title'] ?? 'article'), 0, 50); 
            $filename = 'article_' . $id . '_' . $title_safe . '.pdf';
            
            // Envoi du PDF au navigateur pour le téléchargement
            $dompdf->stream($filename, ["Attachment" => true]);
            exit;
        } else {
            // Message d'erreur de secours (ne devrait plus s'afficher)
            echo "<h1>ERREUR CRITIQUE: DOMPDF NON DÉTECTÉ</h1>";
            echo "<p>Malgré l'autoload, la classe Dompdf reste introuvable. Vérifiez que le dossier 'vendor' est correctement placé.</p>";
            echo "Contenu HTML généré (débogage):<hr>";
            echo $html;
            exit;
        }
    }

    /**
     * Crée la structure HTML propre à l'article pour la conversion PDF.
     */
    private function generateArticleHtml(array $article): string {
        // Le chemin d'image doit être une URL absolue pour que Dompdf puisse la charger (isRemoteEnabled = true)
        // Assurez-vous que l'URL 'http://' . $_SERVER['HTTP_HOST'] . '/gamehub/' fonctionne dans votre navigateur
        $image_url = 'http://' . $_SERVER['HTTP_HOST'] . '/gamehub/' . ($article['image_path'] ?? '');
        
        // Utilisation de 'author_name' (la clé corrigée)
        $author_name = htmlspecialchars($article['author_name'] ?? 'Auteur Inconnu'); 
        
        $html = '
        <html>
        <head>
            <style>
                body { font-family: sans-serif; margin: 40px; }
                h1 { color: #333; border-bottom: 2px solid #eee; padding-bottom: 10px; }
                .meta { color: #777; font-size: 0.9em; margin-bottom: 20px; }
                .content { line-height: 1.6; margin-top: 30px; }
                img { max-width: 100%; height: auto; display: block; margin: 20px auto; }
            </style>
        </head>
        <body>
            <h1>' . htmlspecialchars($article['title'] ?? 'Titre Manquant') . '</h1>
            <div class="meta">
                Publié par ' . $author_name . ' le ' . date('d/m/Y', strtotime($article['created_at'] ?? date('Y-m-d'))) . '
            </div>';

        if (!empty($article['image_path'])) {
             $html .= '<img src="' . $image_url . '" alt="Image de l\'article">';
        }

        $html .= '
            <div class="content">
                ' . nl2br(htmlspecialchars($article['content'] ?? 'Contenu Manquant')) . '
            </div>
            
            <p style="text-align: right; margin-top: 50px;">--- Article généré par GameHub ---</p>

        </body>
        </html>';
        
        return $html;
    }
}


// --- ROUTAGE ET INITIALISATION ---

try {
    $db = Database::getInstance()->getConnection(); 

    if (!$db instanceof PDO) {
        throw new Exception("L'instance de connexion à la base de données (PDO) n'est pas disponible.");
    }
    
    $controller = new PDFController($db);
    
} catch (Exception $e) {
    http_response_code(500);
    echo "<h1>Erreur critique de connexion</h1>";
    echo "<p>Message: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}

// Exécute la fonction de téléchargement
$controller->downloadArticle();