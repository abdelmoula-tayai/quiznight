<?php
require_once '../models/Quiz.php';
require_once '../models/Question.php';
require_once '../models/Answer.php';

class QuizController {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createQuiz($title, $description, $user_id) {
        $quiz = new Quiz($this->conn);
        $quiz->title = $title;
        $quiz->description = $description;
        $quiz->user_id = $user_id;

        return $quiz->create();
    }

    public function listQuizzes() {
        $quiz = new Quiz($this->conn);
        return $quiz->listQuizzes();
    }

    public function addQuestion($quiz_id, $text) {
        $question = new Question($this->conn);
        $question->quiz_id = $quiz_id;
        $question->text = $text;

        return $question->create();
    }

    public function addAnswer($question_id, $text, $is_correct) {
        $answer = new Answer($this->conn);
        $answer->question_id = $question_id;
        $answer->text = $text;
        $answer->is_correct = $is_correct;

        return $answer->create();
    }

    public function getQuiz($quiz_id) {
        $quiz = new Quiz($this->conn);
        $questions = (new Question($this->conn))->getQuestionsByQuizId($quiz_id);
        $quiz_data = ['quiz' => $quiz->getQuiz($quiz_id), 'questions' => []];
        
        foreach ($questions as $question) {
            $answers = (new Answer($this->conn))->getAnswersByQuestionId($question['id']);
            $quiz_data['questions'][] = ['question' => $question, 'answers' => $answers];
        }

        return $quiz_data;
    }
}
?>
