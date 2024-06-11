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

    if ($quizId = $quizController->createQuiz($title, $description)) {
        // Redirect to add questions to the newly created quiz
        header("Location: addQuestion.php?quiz_id=$quizId");
        exit();
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
    <form method="POST" action="">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>
        <label for="description">Description:</label>
        <textarea id="description" name="description" required></textarea>
        <button type="submit">Create Quiz</button>
    </form>
</body>
</html>
