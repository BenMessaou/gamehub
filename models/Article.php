// models/Article.php (CODE COMPLET AVEC delete)

<?php
require_once 'Database.php';

class Article {
    private $conn;
    private $table = 'articles';

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }

    // Fonction 1 : Lire tous les articles pour le Front Office
    public function readAll() {
        $query = 'SELECT a.id, a.title, a.created_at, u.nom as author_name 
                  FROM ' . $this->table . ' a
                  INNER JOIN users u ON a.user_id = u.id_user
                  ORDER BY a.created_at DESC';

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Fonction 2 : Lire tous les articles pour le Back Office
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
    
    // Fonction 3 : Crée un nouvel article (C - Create)
    public function create($title, $content, $user_id) {
        $query = 'INSERT INTO ' . $this->table . ' 
                  SET
                    title = :title, 
                    content = :content, 
                    user_id = :user_id';
        $stmt = $this->conn->prepare($query);
        $title = htmlspecialchars(strip_tags($title));
        $content = strip_tags($content); 
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':user_id', $user_id);
        return $stmt->execute();
    }
    
    // Fonction 4 : Lire un seul article par son ID (R - Read One)
    public function readOne($id) {
        $query = 'SELECT 
                    a.id, a.title, a.content, a.user_id, a.created_at,
                    u.nom as author_name 
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
                    user_id = :user_id
                  WHERE 
                    id = :id';
        $stmt = $this->conn->prepare($query);
        $title = htmlspecialchars(strip_tags($title));
        $content = strip_tags($content); 
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    /**
     * Fonction 6 : Supprimer un article par son ID (D - Delete)
     */
    public function delete($id) {
        $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        
        // Liaison sécurisée de l'ID
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
}