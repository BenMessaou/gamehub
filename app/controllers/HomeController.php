<?php
// app/controllers/HomeController.php

require_once __DIR__ . "/../models/FeedbackModel.php";

class HomeController {
    private $model;

    public function __construct() {
        $this->model = new FeedbackModel();
    }

    // Affiche la page d'accueil (liste des avis)
    public function index() {
        $feedbacks = $this->model->getAll();
        // include la vue correcte dans app/views/public/home.php
        require_once __DIR__ . "/../views/public/home.php";
    }
}
