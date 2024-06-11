<?php
session_start();

require_once '../src/utils/db.php';
require_once '../src/controllers/QuizController.php';

$db = (new Database())->getConnection();
$quizController = new QuizController($db);
$quizzes = $quizController->listQuizzes();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Available Quizzes</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>Available Quizzes</h1>
    <?php if (isset($_SESSION['user'])): ?>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['user']['username']); ?>! <a href="logout.php">Logout</a></p>
        <p><a href="createQuiz.php">Create a New Quiz</a></p>
    <?php else: ?>
        <p><a href="login.php">Login</a> or <a href="register.php">Register</a> to create a quiz.</p>
    <?php endif; ?>
    <ul>
        <?php foreach ($quizzes as $quiz): ?>
            <li>
                <a href="quiz.php?id=<?php echo $quiz['id']; ?>"><?php echo htmlspecialchars($quiz['title']); ?></a>
                <p><?php echo htmlspecialchars($quiz['description']); ?></p>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
