<?php
require_once __DIR__ . '/../Models/ReportModel.php';

class AdminController {
    public function index() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            die("Přístup odepřen.");
        }
        $model = new ReportModel();
        $pendingReports = $model->getPending();
        require __DIR__ . '/../Views/admin.php';
    }
}