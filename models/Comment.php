<?php
// models/Comment.php (MODIFIÉ)

require_once 'Database.php';

class Comment {
    private $conn;
    private $table = 'commentaires';

    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }
    
    /**
     * C - Crée un nouveau commentaire (Front Office).
     */
    public function create($content, $article_id) {
        // !!! IMPORTANT : Remplacez '1' par l'ID de l'utilisateur connecté réel
        $user_id = 1; 

        $query = 'INSERT INTO ' . $this->table . ' 
                  SET
                    content = :content,
                    article_id = :article_id,
                    user_id = :user_id';

        $stmt = $this->conn->prepare($query);
        $content = htmlspecialchars(strip_tags($content)); // Nettoyage strict
        
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * R - Lit tous les commentaires pour un article (Front Office) avec jointure User.
     */
    public function readByArticleId($article_id) {
        $query = 'SELECT 
                    c.id,              
                    c.content,
                    c.created_at,
                    u.nom as author_name  
                  FROM ' . $this->table . ' c
                  INNER JOIN users u ON c.user_id = u.id_user
                  WHERE c.article_id = :article_id
                  ORDER BY c.created_at DESC';
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * R - Lit tous les commentaires pour la modération (Back Office) avec jointures Article et User.
     */
    public function readAllComments() {
        $query = 'SELECT 
                    c.id, 
                    c.content, 
                    c.created_at, 
                    a.title as article_title,
                    u.nom as author_name 
                  FROM ' . $this->table . ' c
                  INNER JOIN articles a ON c.article_id = a.id
                  INNER JOIN users u ON c.user_id = u.id_user
                  ORDER BY c.created_at DESC';

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * D - Supprime un commentaire (Back Office).
     */
    public function delete($id) {
        $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}