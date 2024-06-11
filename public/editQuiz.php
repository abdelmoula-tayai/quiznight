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
    $quiz_id = $_POST['quiz_id'];
    $quiz_title = $_POST['title'];
    $quiz_description = $_POST['description'];

    $quizController->updateQuiz($quiz_id, $quiz_title, $quiz_description);
}

$quiz_id = $_GET['id'];
$quiz = $quizController->getQuiz($quiz_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Quiz</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>Edit Quiz</h1>
    <form method="POST" action="">
        <input type="hidden" name="quiz_id" value="<?php echo htmlspecialchars($quiz['quiz']['id']); ?>">
        <label for="title">Quiz Title:</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($quiz['quiz']['title']); ?>" required>
        <label for="description">Quiz Description:</label>
        <textarea id="description" name="description" required><?php echo htmlspecialchars($quiz['quiz']['description']); ?></textarea>
        <button type="submit">Save Changes</button>
    </form>
    <h2>Questions</h2>
    <ul>
        <?php foreach ($quiz['questions'] as $question): ?>
            <li>
                <?php echo htmlspecialchars($question['question']['text']); ?>
                <ul>
                    <?php foreach ($question['answers'] as $answer): ?>
                        <li><?php echo htmlspecialchars($answer['text']); ?> (<?php echo $answer['is_correct'] ? 'Correct' : 'Incorrect'; ?>)</li>
                    <?php endforeach; ?>
                </ul>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
