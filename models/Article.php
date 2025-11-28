<?php
// models/Article.php 

require_once 'Database.php';

class Article {
    private $conn;
    private $table = 'articles';

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    // Fonction 1 : Lire tous les articles pour le Front Office (list.php)
    public function readAll() {
        $query = 'SELECT a.id, a.title, a.created_at, u.nom as author_name 
                  FROM ' . $this->table . ' a
                  INNER JOIN users u ON a.user_id = u.id_user
                  ORDER BY a.created_at DESC';

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Fonction 2 : Lire tous les articles pour le Back Office (dashboard.php)
    public function readDashboardArticles() {
        $query = 'SELECT 
                    a.id, 
                    a.title, 
                    a.created_at, 
                    u.nom as author_name,
                    u.role as author_role
                  FROM ' . $this->table . ' a
                  INNER JOIN users u ON a.user_id = u.id_user
                  ORDER BY a.created_at DESC';

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Fonction 3 : Créer un nouvel article (C - Create)
    public function create($title, $content, $user_id) {
        $query = 'INSERT INTO ' . $this->table . ' 
                  SET
                    title = :title, 
                    content = :content, 
                    user_id = :user_id';
        
        $stmt = $this->conn->prepare($query);
        $title = htmlspecialchars(strip_tags($title));
        $content = $content; // Laisser les balises si vous utilisez un éditeur WYSIWYG
        
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    // Fonction 4 : Lire un seul article pour l'affichage (R - Read One)
    public function readOne($id) {
        $query = 'SELECT 
                    a.id, 
                    a.title, 
                    a.content, 
                    a.created_at, 
                    a.user_id,
                    u.nom as author_name,
                    u.role as author_role
                  FROM ' . $this->table . ' a
                  INNER JOIN users u ON a.user_id = u.id_user
                  WHERE a.id = :id 
                  LIMIT 0,1';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Fonction 5 : Mettre à jour un article existant (U - Update)
    public function update($id, $title, $content, $user_id) {
        $query = 'UPDATE ' . $this->table . ' 
                  SET
                    title = :title, 
                    content = :content, 
                    user_id = :user_id,
                    updated_at = NOW() 
                  WHERE 
                    id = :id';
        $stmt = $this->conn->prepare($query);
        $title = htmlspecialchars(strip_tags($title));
        $content = $content; 
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    // Fonction 6 : Supprimer un article par son ID (D - Delete)
    public function delete($id) {
        $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    // Fonction 7 : Compte le nombre total d'articles
    public function countTotalArticles() {
        $query = 'SELECT COUNT(*) as total FROM ' . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    // Fonction 8 : Compte le nombre d'auteurs uniques
    public function countUniqueAuthors() {
        $query = 'SELECT COUNT(DISTINCT user_id) as total FROM ' . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    // Fonction 9 : Compte les articles publiés aujourd'hui
    public function countPublishedToday() {
        $query = 'SELECT COUNT(*) as total FROM ' . $this->table . ' WHERE DATE(created_at) = CURDATE()';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    // Fonction 10 : Compte le nombre total de commentaires. (NOUVEAU)
    public function countTotalComments() {
        $comment_table_name = 'commentaires'; 
        
        $query = 'SELECT COUNT(*) as total FROM ' . $comment_table_name; 
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    /**
     * Fonction 11 (Metier 2) : Lit les articles pour une date spécifique (Tri par Date).
     */
    public function readByDate($date) {
        $query = 'SELECT a.id, a.title, a.created_at, u.nom as author_name 
                  FROM ' . $this->table . ' a
                  INNER JOIN users u ON a.user_id = u.id_user
                  WHERE DATE(a.created_at) = :date
                  ORDER BY a.created_at DESC';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':date', $date); 

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}