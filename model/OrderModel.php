<?php

class OrderModel {

    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // créer une commande
    public function createOrder($total) {
        $stmt = $this->pdo->prepare("INSERT INTO orders (total) VALUES (?)");
        $stmt->execute([$total]);
        return $this->pdo->lastInsertId();
    }

    // ajouter un produit dans une commande
    public function addItem($order_id, $game_id, $quantity, $price) {
        $stmt = $this->pdo->prepare("
            INSERT INTO order_items (order_id, game_id, quantity, price)
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([$order_id, $game_id, $quantity, $price]);
    }

    // afficher toutes les commandes
    public function getOrders() {
        return $this->pdo->query("SELECT * FROM orders ORDER BY created_at DESC")
                         ->fetchAll();
    }

    // afficher les articles d'une commande
    public function getOrderItems($orderId) {
        $stmt = $this->pdo->prepare("
            SELECT oi.*, g.name, g.image
            FROM order_items oi
            JOIN games g ON g.id = oi.game_id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }

    // supprimer une commande
    public function deleteOrder($id) {
        // Supprimer d'abord les items (cascade souvent géré par la DB mais on assure ici)
        $stmt = $this->pdo->prepare("DELETE FROM order_items WHERE order_id = ?");
        $stmt->execute([$id]);

        // Supprimer la commande
        $stmt = $this->pdo->prepare("DELETE FROM orders WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
