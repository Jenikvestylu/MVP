<?php
require_once __DIR__ . '/../Models/UserModel.php';

class AuthController {
    public function login() {
        $message = "";
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $model = new UserModel();
            $user = $model->login(trim($_POST['username']), $_POST['password']);
            
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                header("Location: index.php"); // Přesměrování na mapu
                exit;
            } else {
                $message = "Špatné jméno nebo heslo.";
            }
        }
        require __DIR__ . '/../Views/login.php';
    }

    public function register() {
        $message = "";
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $model = new UserModel();
            if ($model->register(trim($_POST['username']), $_POST['password'])) {
                header("Location: index.php?page=login&registered=1");
                exit;
            } else {
                $message = "Uživatel již existuje nebo chyba.";
            }
        }
        require __DIR__ . '/../Views/register.php';
    }

    public function logout() {
        session_destroy();
        header("Location: index.php?page=login");
        exit;
    }
}