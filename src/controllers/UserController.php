<?php
require_once '../src/models/Quiz.php';
require_once '../src/models/Question.php';
require_once '../src/models/Answer.php';
require_once '../src/services/QuizService.php';

class QuizController {
    private $quizService;

    public function __construct($db) {
        $this->quizService = new QuizService($db);
    }

    public function createQuiz($title, $description, $user_id) {
        return $this->quizService->createQuiz($title, $description, $user_id);
    }

    public function listQuizzes() {
        return $this->quizService->listQuizzes();
    }

    public function addQuestion($quiz_id, $text) {
        return $this->quizService->addQuestion($quiz_id, $text);
    }

    public function addAnswer($question_id, $text, $is_correct) {
        return $this->quizService->addAnswer($question_id, $text, $is_correct);
    }

    public function getQuiz($quiz_id) {
        return $this->quizService->getQuiz($quiz_id);
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
