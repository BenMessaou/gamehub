<?php
// models/Comment.php

class Comment {
    private $conn;
    private $table = 'comments'; 

    public function __construct(PDO $db) {
        $this->conn = $db;
    }
    
    /**
     * C - Crée un nouveau commentaire.
     */
    public function create($content, $article_id, $user_id) {
        $query = 'INSERT INTO ' . $this->table . ' 
                     SET
                      content = :content,
                      article_id = :article_id,
                      user_id = :user_id';

        $stmt = $this->conn->prepare($query);
        $content = strip_tags($content); 
        
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * R - Lit tous les commentaires pour un article.
     */
    public function readByArticleId($article_id) {
        $query = 'SELECT 
                      c.id,       
                      c.content, 
                      c.created_at, 
                      u.nom as author_name,
                      c.user_id,      
                      c.article_id      
                  FROM ' . $this->table . ' c
                  INNER JOIN users u ON c.user_id = u.id_user 
                  WHERE c.article_id = :article_id 
                  ORDER BY c.created_at DESC';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
        $stmt->execute(); // <-- Ligne 51 (où l'erreur se produit)
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * R - Lit un seul commentaire par ID.
     */
    public function readOne($id) {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE id = :id LIMIT 0,1';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * U - Met à jour un commentaire existant.
     */
    public function update($id, $content) { 
        $query = 'UPDATE ' . $this->table . ' 
                      SET 
                        content = :content,
                        updated_at = NOW() 
                      WHERE id = :id';

        $stmt = $this->conn->prepare($query);
        $content = strip_tags($content); 
        
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * R - Lit tous les commentaires pour la modération (Back Office).
     */
    public function readAllComments() {
          $query = 'SELECT 
                        c.id,       
                        c.content, 
                        c.created_at, 
                        c.article_id, 
                        u.nom as author_name,
                        a.title as article_title
                    FROM ' . $this->table . ' c
                    INNER JOIN users u ON c.user_id = u.id_user 
                    INNER JOIN articles a ON c.article_id = a.id 
                    ORDER BY c.created_at DESC';

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * D - Supprime un commentaire.
     */
    public function delete($id) {
        $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    /**
     * D - Supprime les commentaires liés à un article (utilisé lors de la suppression d'article).
     */
    public function deleteByArticleId($article_id) {
        $query = 'DELETE FROM ' . $this->table . ' WHERE article_id = :article_id'; 
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}