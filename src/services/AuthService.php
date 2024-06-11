<?php
require_once '../src/models/User.php';

class AuthService {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register($username, $password, $email) {
        $user = new User($this->conn);
        $user->username = $username;
        $user->password = password_hash($password, PASSWORD_DEFAULT);  // Hash the password
        $user->email = $email;

        if ($user->create()) {
            return true;
        }
        return false;
    }

    public function login($username, $password) {
        $user = new User($this->conn);
        $user_data = $user->findByUsername($username);

        if ($user_data && password_verify($password, $user_data['password'])) {
            // Successful login
            $_SESSION['user'] = $user_data;
            return true;
        }
        return false;
    }

    public function logout() {
        session_destroy();
    }
}
?>
