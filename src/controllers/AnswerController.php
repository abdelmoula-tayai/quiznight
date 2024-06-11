<?php
class AnswerController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addAnswer($questionId, $content, $isCorrect) {
        $stmt = $this->conn->prepare("INSERT INTO answer (question_id, content, is_correct) VALUES (:questionId, :content, :isCorrect)");
        $stmt->bindParam(':questionId', $questionId);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':isCorrect', $isCorrect);
    
        return $stmt->execute();
    }
    
    public function getAnswersByQuestionId($questionId) {
        $stmt = $this->conn->prepare("SELECT * FROM answer WHERE question_id = :questionId");
        $stmt->bindParam(':questionId', $questionId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
