<?php
require_once __DIR__ . '/../Models/ReportModel.php';
require_once __DIR__ . '/../Models/WeatherService.php';

class ApiController {
    
    public function handleReports() {
        if (!isset($_SESSION['user_id'])) { http_response_code(403); echo json_encode(['error'=>'Auth']); exit; }
        
        $method = $_SERVER['REQUEST_METHOD'];
        $model = new ReportModel();

        header('Content-Type: application/json');

        // GET: Načtení markerů pro mapu
        if ($method === 'GET') {
            echo json_encode($model->getApproved());
            exit;
        }

        // POST: Nové hlášení
        if ($method === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            if ($model->create($_SESSION['user_id'], $_SESSION['username'], $input['lat'], $input['lon'], $input['desc'])) {
                echo json_encode(['success' => true, 'message' => 'Odesláno ke schválení.']);
            } else {
                echo json_encode(['error' => 'Chyba ukládání']);
            }
            exit;
        }

        // PUT: Admin akce (schválení/smazání)
        if ($method === 'PUT') {
            if ($_SESSION['role'] !== 'admin') { echo json_encode(['error'=>'Role']); exit; }
            
            $input = json_decode(file_get_contents('php://input'), true);
            if ($input['action'] === 'approve') {
                $model->updateStatus($input['id'], 'approved');
            } elseif ($input['action'] === 'delete') {
                $model->delete($input['id']);
            }
            echo json_encode(['success' => true]);
            exit;
        }
    }

    public function handleWeather() {
        if (!isset($_SESSION['user_id'])) { http_response_code(403); exit; }
        header('Content-Type: application/json');
        
        $lat = $_GET['lat'] ?? null;
        $lon = $_GET['lon'] ?? null;

        if (!$lat || !$lon) { echo json_encode(['error' => 'No coords']); exit; }

        $service = new WeatherService();
        echo json_encode($service->getWeather($lat, $lon));
    }
}