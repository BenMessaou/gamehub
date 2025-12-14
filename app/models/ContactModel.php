<?php
require_once __DIR__ . "/../../config/database.php";

class ContactModel {

    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function add($name, $email, $message) {
        $sql = "INSERT INTO contact (name, email, message) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param("sss", $name, $email, $message);
        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }
}
