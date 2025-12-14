<?php
require_once __DIR__ . '/../model/OrderModel.php';

class OrderController {

    private $model;

    public function __construct($pdo) {
        $this->model = new OrderModel($pdo);
    }

    public function create($total, $items) {

        // créer la commande
        $orderId = $this->model->createOrder($total);

        // ajouter les produits (FIX : utiliser quantity et id)
        foreach ($items as $item) {
            $this->model->addItem(
                $orderId,
                $item["id"],           // game_id OK
                $item["quantity"],     // 🟩 FIX ICI : utiliser "quantity" pas "qty"
                $item["price"]
            );
        }

        return $orderId;
    }

    public function all() {
        return $this->model->getOrders();
    }

    public function details($id) {
        return $this->model->getOrderItems($id);
    }

    public function delete($id) {
        return $this->model->deleteOrder($id);
    }
}
