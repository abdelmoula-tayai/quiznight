<?php
class QuizController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createQuiz($title, $description) {
        $stmt = $this->conn->prepare("INSERT INTO quiz (title, description) VALUES (:title, :description)");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId(); // Return the new quiz ID
        } else {
            return false;
        }
    }

    public function getAllQuizzes() {
        $stmt = $this->conn->query("SELECT * FROM quiz");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getQuizById($quizId) {
        $stmt = $this->conn->prepare("SELECT * FROM quiz WHERE id = ?");
        $stmt->bind_param("i", $quizId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
?>
