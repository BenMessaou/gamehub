<?php
// models/User.php

class User {
    private $conn;
    private $table = 'users';

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    /**
     * Récupère tous les utilisateurs (ID et nom) sauf l'utilisateur spécifié (vous-même).
     * @param int $exclude_id L'ID de l'utilisateur qui partage (à exclure de la liste).
     * @return array Liste des utilisateurs.
     */
    public function readAllUsers($exclude_id = 0) {
        $query = 'SELECT id_user, nom FROM ' . $this->table . ' 
                  WHERE id_user != :exclude_id
                  ORDER BY nom ASC';
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':exclude_id', $exclude_id, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}