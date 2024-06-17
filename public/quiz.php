<?php
session_start();
$root = dirname(__DIR__);
require_once $root . '/src/utils/db.php';
require_once $root . '/src/controllers/QuizController.php';
require_once $root . '/src/controllers/QuestionController.php';
require_once $root . '/src/controllers/AnswerController.php';

$db = (new Database())->getConnection();
$quizController = new QuizController($db);
$questionController = new QuestionController($db);
$answerController = new AnswerController($db);

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$quizId = $_GET['id'];
$quiz = $quizController->getQuizById($quizId);
$questions = $questionController->getQuestionsByQuizId($quizId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($quiz['title']); ?></title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1><?php echo htmlspecialchars($quiz['title']); ?></h1>
    <p><?php echo htmlspecialchars($quiz['description']); ?></p>

    <?php foreach ($questions as $question): ?>
        <div class="question">
            <h2><?php echo htmlspecialchars($question['content']); ?></h2>
            <?php
                $answers = $answerController->getAnswersByQuestionId($question['id']);
                foreach ($answers as $answer):
            ?>
                <div class="answer">
                    <input type="radio" name="question_<?php echo $question['id']; ?>" value="<?php echo $answer['id']; ?>">
                    <?php echo htmlspecialchars($answer['content']); ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>

    <a href="index.php">Back to Quiz List</a>
</body>
</html>
