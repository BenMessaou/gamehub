<?php
// Front controller index.php

$controller = $_GET['controller'] ?? 'home';
$action = $_GET['action'] ?? 'index';

$controllerName = ucfirst($controller) . "Controller";

$controllerFile = __DIR__ . "/controllers/$controllerName.php";

if (file_exists($controllerFile)) {
    require_once $controllerFile;

    if (class_exists($controllerName)) {
        $ctrl = new $controllerName();

        if (method_exists($ctrl, $action)) {
            $ctrl->$action();
        } else {
            header("HTTP/1.0 404 Not Found");
            echo "Action '$action' not found in controller '$controllerName'.";
        }
    } else {
        header("HTTP/1.0 404 Not Found");
        echo "Controller class '$controllerName' not found.";
    }
} else {
    header("HTTP/1.0 404 Not Found");
    echo "Controller file '$controllerFile' not found.";
}
?>
