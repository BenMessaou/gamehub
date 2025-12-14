<?php
// models/CommentShare.php

class CommentShare {
    private $db;
    
    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Enregistre le partage d'un commentaire.
     */
    public function shareComment(int $id_commentaire, int $id_user_emetteur, int $id_user_destinataire): bool {
        
        // 1. Vérification pour éviter les doublons
        $checkQuery = "SELECT id_share FROM comment_share WHERE id_comment = :id_commentaire AND id_recipient = :id_user_destinataire"; 
        
        $checkStmt = $this->db->prepare($checkQuery);
        $checkStmt->bindParam(':id_commentaire', $id_commentaire, PDO::PARAM_INT);
        $checkStmt->bindParam(':id_user_destinataire', $id_user_destinataire, PDO::PARAM_INT);
        $checkStmt->execute();
        
        if ($checkStmt->rowCount() > 0) {
            return false; 
        }

        // 2. Insertion du nouveau partage
        $insertQuery = "INSERT INTO comment_share (id_comment, id_user_emetteur, id_recipient) 
                         VALUES (:id_commentaire, :id_user_emetteur, :id_user_destinataire)";
        
        $insertStmt = $this->db->prepare($insertQuery);
        $insertStmt->bindParam(':id_commentaire', $id_commentaire, PDO::PARAM_INT);
        $insertStmt->bindParam(':id_user_emetteur', $id_user_emetteur, PDO::PARAM_INT);
        $insertStmt->bindParam(':id_user_destinataire', $id_user_destinataire, PDO::PARAM_INT);
        
        return $insertStmt->execute(); 
    }
    
    /**
     * Récupère les commentaires partagés avec un utilisateur.
     */
    public function getSharedCommentsForUser(int $id_user_destinataire): array {
        $query = "
            SELECT 
                cs.id_share, 
                cs.shared_at,
                c.content AS comment_content, 
                a.title AS article_title, 
                a.id AS article_id_lien, 
                s.nom AS sender_username 
            FROM 
                comment_share cs
            INNER JOIN 
                comments c ON cs.id_comment = c.id
            INNER JOIN 
                articles a ON c.article_id = a.id 
            INNER JOIN 
                users s ON cs.id_user_emetteur = s.id_user 
            WHERE 
                cs.id_recipient = :id_user_destinataire 
            ORDER BY 
                cs.shared_at DESC
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_user_destinataire', $id_user_destinataire, PDO::PARAM_INT);
        $stmt->execute(); 
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Supprime les enregistrements de partage par ID d'article.
     */
    public function deleteSharesByArticleId(int $article_id): bool {
        $query = "
            DELETE cs FROM comment_share cs
            INNER JOIN comments c ON cs.id_comment = c.id
            WHERE c.article_id = :article_id
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Supprime tous les partages liés à un ID de commentaire donné.
     */
    public function deleteSharesByCommentId(int $id_commentaire): bool {
        $query = "DELETE FROM comment_share WHERE id_comment = :id_commentaire"; 
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_commentaire', $id_commentaire, PDO::PARAM_INT);
        return $stmt->execute();
    }
}