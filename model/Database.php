<?php

/**
 * Classe Database (Connexion PDO)
 * Utilise le pattern Singleton pour garantir une seule instance de connexion.
 */
class Database {
    private static $instance = null;
    private $conn;

    // --- VOS VALEURS DE CONNEXION ---
    private $host = 'localhost';
    private $db_name = 'bdgamehub';     
    private $username = 'root';     
    private $password = '';         // VÉRIFIEZ VOTRE MOT DE PASSE MYSQL

    /**
     * Constructeur privé.
     */
    private function __construct() {
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch (PDOException $exception) {
            echo "Erreur de connexion : " . $exception->getMessage();
            die(); 
        }
    }

    /**
     * Obtient l'instance unique de la connexion.
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    /**
     * Récupère la connexion PDO.
     */
    public function getConnection() {
        return $this->conn;
    }
}