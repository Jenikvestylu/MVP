<?php
class ReportModel {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::connect();
    }

    public function create($userId, $username, $lat, $lon, $desc) {
        $stmt = $this->pdo->prepare("INSERT INTO reports (user_id, username, lat, lon, description, status) VALUES (?, ?, ?, ?, ?, 'pending')");
        return $stmt->execute([$userId, $username, $lat, $lon, htmlspecialchars($desc)]);
    }

    public function getPending() {
        $stmt = $this->pdo->query("SELECT * FROM reports WHERE status = 'pending' ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public function getApproved() {
        $stmt = $this->pdo->query("SELECT lat, lon, description, username, created_at FROM reports WHERE status = 'approved'");
        return $stmt->fetchAll();
    }

    public function updateStatus($id, $status) {
        $stmt = $this->pdo->prepare("UPDATE reports SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM reports WHERE id = ?");
        return $stmt->execute([$id]);
    }
}