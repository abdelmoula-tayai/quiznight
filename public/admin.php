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
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <header class="bg-blue-500 text-white p-4 flex justify-between items-center">
        <h1 class="text-2xl">Admin Panel</h1>
        <a href="index.php" class="bg-white text-blue-500 rounded p-2">Home</a>
    </header>
    <main class="p-4">
        <form method="POST" action="" class="bg-white shadow rounded p-4 mb-4">
            <div class="mb-4">
                <label for="title" class="block text-sm mb-2">Quiz Title:</label>
                <input type="text" id="title" name="title" required class="block w-full p-2 border rounded">
            </div>
            <div class="mb-4">
                <label for="description" class="block text-sm mb-2">Quiz Description:</label>
                <textarea id="description" name="description" required class="block w-full p-2 border rounded"></textarea>
            </div>
            <button type="submit" class="bg-blue-500 text-white rounded p-2">Create Quiz</button>
        </form>
        <h2 class="text-xl mb-4">Your Quizzes</h2>
        <ul>
            <?php foreach ($quizzes as $quiz): ?>
                <li class="mb-2">
                    <?php echo htmlspecialchars($quiz['title']); ?>
                    <a href="editQuiz.php?id=<?php echo $quiz['id']; ?>" class="text-blue-500 underline">Edit</a>
                </li>
            <?php endforeach; ?>
        </ul>
    </main>
</body>
</html>