<?php
require_once '../src/models/User.php';

class UserService {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register($username, $password, $email) {
        $user = new User($this->conn);
        $user->username = $username;
        $user->password = $password;
        $user->email = $email;

        if ($user->create()) {
            return true;
        }
        return false;
    }

    public function login($username, $password) {
        $user = new User($this->conn);
        return $user->authenticate($username, $password);
    }

    public function getUserById($id) {
        $query = "SELECT * FROM user WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
