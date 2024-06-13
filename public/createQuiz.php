<?php
session_start();
$root = dirname(__DIR__);
require_once $root . '/src/utils/db.php';
require_once $root . '/src/controllers/QuizController.php';

$db = (new Database())->getConnection();
$quizController = new QuizController($db);

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $questions = $_POST['questions'];
    $user_id = $_SESSION['user']['id'];

    if ($quizId = $quizController->createQuiz($title, $description, $user_id)) {
        foreach ($questions as $question) {
            $questionContent = $question['content'];
            $answers = $question['answers'];

            if ($questionId = $quizController->createQuestion($quizId, $questionContent)) {
                foreach ($answers as $answer) {
                    $answerContent = $answer['content'];
                    $isCorrect = isset($answer['is_correct']) ? 1 : 0;

                    if (!$quizController->createAnswer($questionId, $answerContent, $isCorrect)) {
                        $error = 'Failed to create answer.';
                        break 2;
                    }
                }
            } else {
                $error = 'Failed to create question.';
                break;
            }
        }

        if (!$error) {
            // Redirect to the quiz page
            header("Location: quiz.php?quiz_id=$quizId");
            exit();
        }
    } else {
        $error = 'Failed to create quiz.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Quiz</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>Create Quiz</h1>
    <?php if ($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form action="createQuiz.php" method="post">
        <label for="title">Quiz Title:</label>
        <input type="text" id="title" name="title" required>

        <label for="description">Quiz Description:</label>
        <textarea id="description" name="description" required></textarea>

        <div id="questions">
            <!-- JavaScript will insert question fields here -->
        </div>

        <button type="button" onclick="addQuestion()">Add Question</button>
        <input type="submit" value="Create Quiz">
    </form>
    <script>
        let questionIndex = 0;

        function addQuestion() {
            const questionsDiv = document.getElementById('questions');
            const questionDiv = document.createElement('div');
            questionDiv.innerHTML = `
                <h2>Question ${questionIndex + 1}</h2>
                <label>Question:</label>
                <input type="text" name="questions[${questionIndex}][content]" required>
                <h3>Answers</h3>
                <div class="answer">
                    <input type="text" name="questions[${questionIndex}][answers][0][content]" required>
                    <label>Correct?</label>
                    <input type="checkbox" name="questions[${questionIndex}][answers][0][is_correct]">
                </div>
                <button type="button" onclick="addAnswer(this, ${questionIndex})">Add Answer</button>
            `;
            questionsDiv.appendChild(questionDiv);
            questionIndex++;
        }

        function addAnswer(button, questionIndex) {
            const answerDiv = document.createElement('div');
            answerDiv.className = 'answer';
            const answerIndex = button.parentNode.querySelectorAll('.answer').length;
            answerDiv.innerHTML = `
                <input type="text" name="questions[${questionIndex}][answers][${answerIndex}][content]" required>
                <label>Correct?</label>
                <input type="checkbox" name="questions[${questionIndex}][answers][${answerIndex}][is_correct]">
            `;
            button.parentNode.insertBefore(answerDiv, button);
        }
    </script>
</body>
</html>