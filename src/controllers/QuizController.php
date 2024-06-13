<?php
class QuizController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createQuiz($title, $description, $userId) {
        $stmt = $this->conn->prepare("INSERT INTO quiz (title, description, user_id) VALUES (:title, :description, :user_id)");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':user_id', $userId);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId(); // Return the new quiz ID
        } else {
            return false;
        }
    }

    public function createQuestion($quizId, $content) {
        $stmt = $this->conn->prepare("INSERT INTO question (quiz_id, content) VALUES (:quiz_id, :content)");
        $stmt->bindParam(':quiz_id', $quizId);
        $stmt->bindParam(':content', $content);
    
        if ($stmt->execute()) {
            return $this->conn->lastInsertId(); // Return the new question ID
        } else {
            return false;
        }
    }
    
    public function createAnswer($questionId, $content, $isCorrect) {
        $stmt = $this->conn->prepare("INSERT INTO answer (question_id, content, is_correct) VALUES (:question_id, :content, :is_correct)");
        $stmt->bindParam(':question_id', $questionId);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':is_correct', $isCorrect);
    
        if ($stmt->execute()) {
            return $this->conn->lastInsertId(); // Return the new answer ID
        } else {
            return false;
        }
    }

    public function getAllQuizzes() {
        $stmt = $this->conn->query("SELECT * FROM quiz");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getQuizById($quizId) {
        $stmt = $this->conn->prepare("SELECT * FROM quiz WHERE id = :quiz_id");
        $stmt->bindParam(':quiz_id', $quizId);
        $stmt->execute();
        $quiz = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($quiz) {
            $stmt = $this->conn->prepare("SELECT * FROM question WHERE quiz_id = :quiz_id");
            $stmt->bindParam(':quiz_id', $quizId);
            $stmt->execute();
            $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            foreach ($questions as $i => $question) {
                $stmt = $this->conn->prepare("SELECT * FROM answer WHERE question_id = :question_id");
                $stmt->bindParam(':question_id', $question['id']);
                $stmt->execute();
                $questions[$i]['answers'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
    
            $quiz['questions'] = $questions;
        }
    
        return $quiz;
    }

    
    
    public function updateQuiz($quizId, $title, $description, $questions) {
        $stmt = $this->conn->prepare("UPDATE quiz SET title = :title, description = :description WHERE id = :quiz_id");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':quiz_id', $quizId);
        $stmt->execute();

        if (is_array($questions)) {
            foreach ($questions as $question) {
                if (isset($question['id'])) {
                    // Update existing question
                    $stmt = $this->conn->prepare("UPDATE question SET content = :content WHERE id = :question_id");
                    $stmt->bindParam(':content', $question['content']);
                    $stmt->bindParam(':question_id', $question['id']);
                    $stmt->execute();
                } else {
                    // Create new question
                    $stmt = $this->conn->prepare("INSERT INTO question (quiz_id, content) VALUES (:quiz_id, :content)");
                    $stmt->bindParam(':quiz_id', $quizId);
                    $stmt->bindParam(':content', $question['content']);
                    $stmt->execute();
                    $question['id'] = $this->conn->lastInsertId(); // Get the last inserted ID
                }

                foreach ($question['answers'] as $answer) {
                    if (isset($answer['id'])) {
                        // Update existing answer
                        $stmt = $this->conn->prepare("UPDATE answer SET content = :content, is_correct = :is_correct WHERE id = :answer_id");
                        $stmt->bindParam(':content', $answer['content']);
                        $stmt->bindParam(':is_correct', $answer['is_correct']);
                        $stmt->bindParam(':answer_id', $answer['id']);
                        $stmt->execute();
                    } else {
                        // Create new answer
                        $stmt = $this->conn->prepare("INSERT INTO answer (question_id, content, is_correct) VALUES (:question_id, :content, :is_correct)");
                        $stmt->bindParam(':question_id', $question['id']); // Use the question ID
                        $stmt->bindParam(':content', $answer['content']);
                        $stmt->bindParam(':is_correct', $answer['is_correct']);
                        $stmt->execute();
                    }
                }
            }
        }

        return true;
    }

    public function getAllQuizzesByUserId($userId) {
        $stmt = $this->conn->prepare("SELECT * FROM quiz WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>