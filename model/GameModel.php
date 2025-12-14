<?php
class GameModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllGames() {
        $stmt = $this->pdo->query("SELECT id, name, description, price, rating, image, category FROM games");
        return $stmt->fetchAll();
    }

    public function addGame($name, $price, $image, $category, $description, $rating) {
        $sql = "INSERT INTO games (name, price, image, category, description, rating) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$name, $price, $image, $category, $description, $rating]);
    }

    public function updateGame($id, $name, $price, $image, $category, $description, $rating) {
        $sql = "UPDATE games SET name=?, price=?, image=?, category=?, description=?, rating=? WHERE id=?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$name, $price, $image, $category, $description, $rating, $id]);
    }

    public function deleteGame($id) {
        $sql = "DELETE FROM games WHERE id=?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
}
?>
