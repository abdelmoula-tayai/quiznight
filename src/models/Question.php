<?php
class Question {
    private $conn;
    private $table = 'question';

    public $id;
    public $quiz_id;
    public $text;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table . " (quiz_id, text) VALUES (:quiz_id, :text)";
        $stmt = $this->conn->prepare($query);

        $this->quiz_id = htmlspecialchars(strip_tags($this->quiz_id));
        $this->text = htmlspecialchars(strip_tags($this->text));

        $stmt->bindParam(':quiz_id', $this->quiz_id);
        $stmt->bindParam(':text', $this->text);

        return $stmt->execute();
    }

    public function getQuestionsByQuizId($quiz_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE quiz_id = :quiz_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quiz_id', $quiz_id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
