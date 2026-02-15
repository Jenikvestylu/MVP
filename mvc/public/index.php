<?php
session_start();

// Načtení konfigurace
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../app/Models/Database.php';

// Jednoduchý "Router"
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

switch ($page) {
    case 'home':
        require_once __DIR__ . '/../app/Controllers/HomeController.php';
        $controller = new HomeController();
        $controller->index();
        break;

    case 'login':
        require_once __DIR__ . '/../app/Controllers/AuthController.php';
        $controller = new AuthController();
        $controller->login();
        break;

    case 'register':
        require_once __DIR__ . '/../app/Controllers/AuthController.php';
        $controller = new AuthController();
        $controller->register();
        break;

    case 'logout':
        require_once __DIR__ . '/../app/Controllers/AuthController.php';
        $controller = new AuthController();
        $controller->logout();
        break;

    case 'admin':
        require_once __DIR__ . '/../app/Controllers/AdminController.php';
        $controller = new AdminController();
        $controller->index();
        break;

    // API Endpointy pro JavaScript
    case 'api_reports':
        require_once __DIR__ . '/../app/Controllers/ApiController.php';
        $controller = new ApiController();
        $controller->handleReports();
        break;
        
    case 'api_weather':
        require_once __DIR__ . '/../app/Controllers/ApiController.php';
        $controller = new ApiController();
        $controller->handleWeather();
        break;

    default:
        echo "404 - Stránka nenalezena";
        break;
}