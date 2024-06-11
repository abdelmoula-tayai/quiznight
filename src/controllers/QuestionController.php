<?php
class QuestionController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addQuestion($quizId, $content) {
        $stmt = $this->conn->prepare("INSERT INTO question (quiz_id, content) VALUES (:quizId, :content)");
        $stmt->bindParam(':quizId', $quizId);
        $stmt->bindParam(':content', $content);
    
        return $stmt->execute();
    }

public function getQuestionsByQuizId($quizId) {
    $stmt = $this->conn->prepare("SELECT * FROM question WHERE quiz_id = :quizId");
    $stmt->bindParam(':quizId', $quizId);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}
?>
