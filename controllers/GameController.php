<?php
require_once __DIR__ . '/../models/GameModel.php';

class GameController {
    private $model;

    public function __construct($pdo) {
        $this->model = new GameModel($pdo);
    }

    public function index() {
        $games = $this->model->getAllGames();
        // In a real MVC framework, we would "render" the view here.
        // For this simple integration, we will return the data or include the view.
        // Since shop.php will instantiate the controller, we can just return the games.
        return $games;
    }

    public function add($data) {
        // Basic validation
        if (empty($data['name']) || empty($data['price'])) {
            return false;
        }
        
        return $this->model->addGame(
            $data['name'],
            $data['price'],
            $data['image'],
            $data['category'],
            $data['description'],
            $data['rating']
        );
    }

    public function update($id, $data) {
        if (empty($id) || empty($data['name']) || empty($data['price'])) {
            return false;
        }

        return $this->model->updateGame(
            $id,
            $data['name'],
            $data['price'],
            $data['image'],
            $data['category'],
            $data['description'],
            $data['rating']
        );
    }

    public function delete($id) {
        if (empty($id)) {
            return false;
        }
        return $this->model->deleteGame($id);
    }
}
?>
