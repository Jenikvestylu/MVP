<?php
class HomeController {
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?page=login");
            exit;
        }
        $isAdmin = ($_SESSION['role'] === 'admin');
        require __DIR__ . '/../Views/map.php';
    }
}