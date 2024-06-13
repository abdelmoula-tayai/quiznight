<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

require_once '../src/utils/db.php';
require_once '../src/controllers/QuizController.php';

$db = (new Database())->getConnection();
$quizController = new QuizController($db);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $quizController->createQuiz($_POST['title'], $_POST['description'], $_SESSION['user']['id']);
}

$quizzes = $quizController->getAllQuizzesByUserId($_SESSION['user']['id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>Admin Panel</h1>
    <form method="POST" action="">
        <label for="title">Quiz Title:</label>
        <input type="text" id="title" name="title" required>
        <label for="description">Quiz Description:</label>
        <textarea id="description" name="description" required></textarea>
        <button type="submit">Create Quiz</button>
    </form>
    <h2>Your Quizzes</h2>
    <ul>
        <?php foreach ($quizzes as $quiz): ?>
            <li>
                <?php echo htmlspecialchars($quiz['title']); ?>
                <a href="editQuiz.php?id=<?php echo $quiz['id']; ?>">Edit</a>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>