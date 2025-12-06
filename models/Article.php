<?php
// models/Article.php 

class Article {
    private $conn;
    private $table = 'articles';

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    public function readAll() {
        $query = 'SELECT a.id, a.title, a.image_path, a.created_at, u.nom as author_name 
                  FROM ' . $this->table . ' a
                  INNER JOIN users u ON a.user_id = u.id_user
                  ORDER BY a.created_at DESC';

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function readDashboardArticles() {
        $query = 'SELECT 
                      a.id, 
                      a.title, 
                      a.image_path, 
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
    
    public function create($title, $content, $user_id, $imagePath = null) {
        $query = 'INSERT INTO ' . $this->table . ' 
                  SET
                    title = :title, 
                    content = :content, 
                    user_id = :user_id,
                    image_path = :image_path, 
                    created_at = NOW()';
        
        $stmt = $this->conn->prepare($query);
        $title = htmlspecialchars(strip_tags($title));
        $content = $content; 
        
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':image_path', $imagePath, $imagePath === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        
        return $stmt->execute();
    }
    
    public function readOne($id) {
        $query = 'SELECT 
                      a.id, 
                      a.title, 
                      a.content, 
                      a.image_path, 
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
    
    public function delete($id) {
        $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    public function countTotalArticles() {
        $query = 'SELECT COUNT(*) as total FROM ' . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    public function countUniqueAuthors() {
        $query = 'SELECT COUNT(DISTINCT user_id) as total FROM ' . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    public function countPublishedToday() {
        $query = 'SELECT COUNT(*) as total FROM ' . $this->table . ' WHERE DATE(created_at) = CURDATE()';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    public function countTotalComments() {
        $comment_table_name = 'commentaires'; 
        
        $query = 'SELECT COUNT(*) as total FROM ' . $comment_table_name; 
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    public function readByDate($date) {
        $query = 'SELECT a.id, a.title, a.image_path, a.created_at, u.nom as author_name 
                  FROM ' . $this->table . ' a
                  INNER JOIN users u ON a.user_id = u.id_user
                  WHERE DATE(a.created_at) = :date
                  ORDER BY a.created_at DESC';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':date', $date); 

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // 🚫 La méthode readAllContent a été retirée.
}