<?php
// config/database.php
class Database {
    private $host = 'localhost';
    private $user = 'root';
    private $pass = '';
    private $db   = 'feedback_db';
    private $port = 3306;
    private $conn;

    public function getConnection() {
        if ($this->conn instanceof mysqli) return $this->conn;

        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->db, $this->port);
        if ($this->conn->connect_error) {
            die("Erreur connexion MySQL: " . $this->conn->connect_error);
        }
        $this->conn->set_charset('utf8mb4');
        return $this->conn;
    }
}
