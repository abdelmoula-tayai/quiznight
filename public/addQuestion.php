<?php
session_start();

require_once  '../src/utils/db.php';
require_once  '../src/controllers/QuestionController.php';
require_once  '../src/controllers/AnswerController.php';

$db = (new Database())->getConnection();
$questionController = new QuestionController($db);
$answerController = new AnswerController($db);

$error = '';

if (!isset($_GET['quiz_id'])) {
    header("Location: index.php");
    exit();
}

$quizId = $_GET['quiz_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $questions = $_POST['questions'];
    $answers = $_POST['answers'];
    $correctAnswers = $_POST['correct_answers'];
    
    foreach ($questions as $index => $questionText) {
        if (!empty($questionText)) {
            $questionId = $questionController->addQuestion($quizId, $questionText);
            if ($questionId) {
                foreach ($answers[$index] as $answerIndex => $answerText) {
                    if (!empty($answerText)) {
                        $isCorrect = ($correctAnswers[$index] == $answerIndex) ? 1 : 0;
                        $answerController->addAnswer($questionId, $answerText, $isCorrect);
                    }
                }
            } else {
                $error = 'Failed to add some questions or answers.';
                break;
            }
        }
    }

    if (!$error) {
        header("Location: quiz.php?id=$quizId");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Questions</title>
    <link rel="stylesheet" href="css/styles.css">
    <script>
        function addQuestionField() {
            var questionIndex = document.getElementsByClassName('question-container').length;
            var questionContainer = document.createElement('div');
            questionContainer.className = 'question-container';

            var questionInput = document.createElement('input');
            questionInput.type = 'text';
            questionInput.name = 'questions[]';
            questionInput.placeholder = 'Question';
            questionInput.required = true;

            var answerContainer = document.createElement('div');
            answerContainer.className = 'answers-container';

            for (let i = 0; i < 4; i++) {
                var answerDiv = document.createElement('div');
                var answerInput = document.createElement('input');
                answerInput.type = 'text';
                answerInput.name = 'answers[' + questionIndex + '][]';
                answerInput.placeholder = 'Answer ' + (i + 1);
                answerInput.required = true;

                var correctInput = document.createElement('input');
                correctInput.type = 'radio';
                correctInput.name = 'correct_answers[' + questionIndex + ']';
                correctInput.value = i;
                correctInput.required = true;

                answerDiv.appendChild(answerInput);
                answerDiv.appendChild(correctInput);
                answerContainer.appendChild(answerDiv);
            }

            questionContainer.appendChild(questionInput);
            questionContainer.appendChild(answerContainer);
            
            document.getElementById('questions-container').appendChild(questionContainer);
        }
    </script>
</head>
<body>
    <h1>Add Questions and Answers to Quiz</h1>
    <?php if ($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="POST" action="">
        <div id="questions-container">
            <!-- Initial question and answers field -->
            <div class="question-container">
                <input type="text" name="questions[]" placeholder="Question" required>
                <div class="answers-container">
                    <?php for ($i = 0; $i < 4; $i++): ?>
                        <div>
                            <input type="text" name="answers[0][]" placeholder="Answer <?php echo $i + 1; ?>" required>
                            <input type="radio" name="correct_answers[0]" value="<?php echo $i; ?>" required>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
        <button type="button" onclick="addQuestionField()">Add Another Question</button>
        <button type="submit">Submit All Questions</button>
    </form>
    <br>
    <a href="quiz.php?id=<?php echo $quizId; ?>">View Quiz</a>
</body>
</html>
