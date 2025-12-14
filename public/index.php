<?php
session_start();

// Charger l'autoload
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/models/User.php';
require_once __DIR__ . '/../app/models/FeedbackModel.php';

// Router simple
$uri = trim($_SERVER['REQUEST_URI'], '/');

// Route login admin
if ($uri === 'feedback-games/admin') {
    require_once __DIR__ . '/../app/controllers/AuthController.php';
    $controller = new AuthController();
    $controller->login();
    exit;
}

// Route dashboard admin
if ($uri === 'feedback-games/admin/dashboard') {
    require_once __DIR__ . '/../app/controllers/AdminController.php';
    $controller = new AdminController();
    $controller->dashboard();
    exit;
}

// Route avis
if ($uri === 'feedback-games/avis') {
    require_once __DIR__ . '/../app/controllers/FeedbackController.php';
    $controller = new FeedbackController();
    $controller->index();
    exit;
}

// Page d'accueil
require_once __DIR__ . '/../app/controllers/HomeController.php';
$controller = new HomeController();
$controller->index();
