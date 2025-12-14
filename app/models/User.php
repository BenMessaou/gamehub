<?php
// app/models/User.php
require_once __DIR__ . "/../../config/database.php";

class UserModel {
    private $conn;

    public function __construct() {
        // Database class we used earlier exposes getConnection()
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function findByUsername($username) {
        $sql = "SELECT * FROM users WHERE username = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return false;
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $stmt->close();
        return $row ?: false;
    }
}
