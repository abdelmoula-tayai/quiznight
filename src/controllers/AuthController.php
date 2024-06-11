<?php
require_once '../src/services/AuthService.php';

class AuthController {
    private $authService;

    public function __construct($db) {
        $this->authService = new AuthService($db);
    }

    public function register($username, $password, $email) {
        return $this->authService->register($username, $password, $email);
    }

    public function login($username, $password) {
        return $this->authService->login($username, $password);
    }

    public function logout() {
        $this->authService->logout();
    }
}
?>
