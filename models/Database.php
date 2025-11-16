// models/Database.php

<?php

/**
 * Classe Database (Connexion PDO)
 * Utilise le pattern Singleton pour garantir une seule instance de connexion.
 */
class Database {
    private static $instance = null;
    private $conn;

    // ATTENTION : LES VALEURS DE CONNEXION CORRECTES
    private $host = 'localhost';
    private $db_name = 'gamehub';     // <<< CORRIGÉ : UTILISE LE NOM DE VOTRE BASE
    private $username = 'root';     // VOTRE NOM D'UTILISATEUR MYSQL
    private $password = '';         // VOTRE MOT DE PASSE MYSQL

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
            // On utilise die() pour arrêter l'exécution si la connexion échoue
            die(); 
        }
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }
}