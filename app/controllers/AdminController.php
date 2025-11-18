<?php
// app/controllers/AdminController.php
require_once __DIR__ . '/../models/FeedbackModel.php';

class AdminController {
    public function dashboard() {
        $model = new FeedbackModel();
        $feedbacks = $model->getAll();
        // rendre la vue :
        require_once __DIR__ . '/../views/admin/dashboard.php';
    }

    public function delete() {
        // suppression via POST (sécurise selon besoin)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $model = new FeedbackModel();
            $model->delete($id);
        }
        // rediriger vers le dashboard simple
        header('Location: /feedback-games/admin/index.php');
        exit;
    }
}
