<?php
// app/models/FeedbackModel.php
require_once __DIR__ . "/../../config/database.php";

class FeedbackModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection(); // getConnection() doit exister dans config/database.php
    }

    public function getAll() {
        $sql = "SELECT * FROM feedback ORDER BY created_at DESC";
        $res = $this->conn->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function add($pseudo, $game, $rating, $message) {
        $sql = "INSERT INTO feedback (pseudo, game, rating, message) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return false;
        $stmt->bind_param("ssis", $pseudo, $game, $rating, $message);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function delete($id) {
        $sql = "DELETE FROM feedback WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return false;
        $stmt->bind_param("i", $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}
