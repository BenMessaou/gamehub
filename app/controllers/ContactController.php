<?php
require_once __DIR__ . '/../models/ContactModel.php';

class ContactController {

    public function send() {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $message = $_POST['message'] ?? '';

            $model = new ContactModel();
            $ok = $model->add($name, $email, $message);

            if ($ok) {
                echo "<script>alert('Message envoyé avec succès !');</script>";
            } else {
                echo "<script>alert('Erreur, message non envoyé.');</script>";
            }

            header("Location: /feedback-games/public/avis.php");
            exit;
        }
    }
}
