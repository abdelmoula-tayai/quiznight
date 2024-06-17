<?php
class Answer {
    private $conn;
    private $table = 'answer';

    public $id;
    public $question_id;
    public $text;
    public $is_correct;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " (question_id, text, is_correct) VALUES (:question_id, :text, :is_correct)";
        $stmt = $this->conn->prepare($query);

        $this->question_id = htmlspecialchars(strip_tags($this->question_id));
        $this->text = htmlspecialchars(strip_tags($this->text));
        $this->is_correct = htmlspecialchars(strip_tags($this->is_correct));

        $stmt->bindParam(':question_id', $this->question_id);
        $stmt->bindParam(':text', $this->text);
        $stmt->bindParam(':is_correct', $this->is_correct);

        return $stmt->execute();
    }

    public function getAnswersByQuestionId($question_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE question_id = :question_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':question_id', $question_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
