<?php
session_start();

require_once '../src/utils/db.php';
require_once '../src/controllers/QuizController.php';

$db = (new Database())->getConnection();
$quizController = new QuizController($db);
$quizzes = $quizController->getAllQuizzes();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Available Quizzes</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <header class="bg-blue-500 text-white p-4 flex justify-between items-center">
        <h1 class="text-2xl">QuizNight</h1>
        <?php if (isset($_SESSION['user'])): ?>
            <a href="admin.php" class="bg-white text-blue-500 rounded p-2">Admin Panel</a>
        <?php endif; ?>
    </header>
    <main class="p-4">
        <?php if (isset($_SESSION['user'])): ?>
            <p>Welcome, <?php echo htmlspecialchars($_SESSION['user']['username']); ?>! <a href="logout.php" class="text-blue-500 underline">Logout</a></p>
        <?php else: ?>
            <p><a href="login.php" class="text-blue-500 underline">Login</a> or <a href="register.php" class="text-blue-500 underline">Register</a> to create a quiz.</p>
        <?php endif; ?>
        <h2 class="text-xl mb-4">Available Quizzes</h2>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($quizzes as $quiz): ?>
                <div class="bg-white shadow rounded p-4">
                    <h3 class="text-lg mb-2">
                        <a href="quiz.php?id=<?php echo $quiz['id']; ?>" class="text-blue-500 underline"><?php echo htmlspecialchars($quiz['title']); ?></a>
                    </h3>
                    <p><?php echo htmlspecialchars($quiz['description']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
</body>
</html>
