<?php
require_once '../models/User.php';

class AuthService {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register($username, $password, $email) {
        $user = new User($this->conn);
        $user->username = $username;
        $user->password = $password;
        $user->email = $email;

        if($user->create()) {
            return true;
        }
        return false;
    }

    public function login($username, $password) {
        $user = new User($this->conn);
        return $user->authenticate($username, $password);
    }
}
?>
