<?php
require_once '../src/models/Quiz.php';

class QuizService {
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
        $quizDetails = $quiz->getQuiz($quiz_id);

        if (!$quizDetails) {
            return null;
        }

        $question = new Question($this->conn);
        $questions = $question->getQuestionsByQuizId($quiz_id);

        $quizDetails['questions'] = [];
        foreach ($questions as $question) {
            $answer = new Answer($this->conn);
            $answers = $answer->getAnswersByQuestionId($question['id']);

            $quizDetails['questions'][] = [
                'question' => $question,
                'answers' => $answers
            ];
        }

        return $quizDetails;
    }

    public function updateQuiz($quiz_id, $title, $description) {
        $quiz = new Quiz($this->conn);
        $quiz->id = $quiz_id;
        $quiz->title = $title;
        $quiz->description = $description;

        return $quiz->update();
    }
}
?>
