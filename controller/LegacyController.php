<?php
// controllers/LegacyController.php
class LegacyController {
    public function page($name = 'index') {
        $config = __DIR__ . '/../model/config.php';
        if (file_exists($config)) {
            require_once $config;
        }

        $file = __DIR__ . '/../views/' . $name . '.php';
        if (file_exists($file)) {
            require $file;
            return;
        }
        http_response_code(404);
        echo "Page non trouvée: " . htmlspecialchars($name);
    }
}
