<?php
require_once __DIR__ . '/../controllers/LegacyController.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'index';

$legacy = new LegacyController();
$legacy->page($page);
