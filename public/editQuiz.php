<?php
session_start();

require_once '../src/utils/db.php';
require_once '../src/controllers/QuizController.php';

$db = (new Database())->getConnection();
$quizController = new QuizController($db);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $quiz_id = $_POST['quiz_id'];
    $quiz_title = $_POST['title'];
    $quiz_description = $_POST['description'];
    $questions = isset($_POST['questions']) ? $_POST['questions'] : [];

    // Ensure that 'content' is set for every question and answer
    foreach ($questions as &$question) {
        if (!isset($question['content']) || trim($question['content']) === '') {
            $question['content'] = 'Default question content';
        }

        if (isset($question['answers'])) {
            foreach ($question['answers'] as &$answer) {
                if (!isset($answer['content']) || trim($answer['content']) === '') {
                    $answer['content'] = 'Default answer content';
                }
                // Update is_correct field
                $answer['is_correct'] = isset($answer['is_correct']) ? 1 : 0;
            }
        }
    }

    $quizController->updateQuiz($quiz_id, $quiz_title, $quiz_description, $questions);
    header("Location: admin.php");
    exit;
}

$quiz_id = isset($_GET['id']) ? $_GET['id'] : null;
if (!$quiz_id) {
    echo "Quiz ID is missing.";
    exit;
}

$quiz = $quizController->getQuizById($quiz_id);
if (!$quiz) {
    echo "Quiz not found.";
    exit;
}

// Check if the quiz was created by the current user
if ($quiz['user_id'] != $_SESSION['user']['id']) {
    header("Location: index.php");
    exit;
}

// Ensure questions key exists and is an array
if (!isset($quiz['questions']) || !is_array($quiz['questions'])) {
    $quiz['questions'] = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Quiz</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <header class="bg-blue-500 text-white p-4">
        <h1 class="text-2xl">Edit Quiz</h1>
    </header>
    <main class="p-4">
        <form method="post" action="editQuiz.php" enctype="multipart/form-data" class="bg-white shadow rounded p-4">
            <input type="hidden" name="quiz_id" value="<?php echo htmlspecialchars($quiz_id); ?>">

            <div class="mb-4">
                <label for="title" class="block text-sm mb-2">Quiz Title:</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($quiz['title']); ?>" required class="block w-full p-2 border rounded">
            </div>

            <div class="mb-4">
                <label for="description" class="block text-sm mb-2">Quiz Description:</label>
                <textarea id="description" name="description" required class="block w-full p-2 border rounded"><?php echo htmlspecialchars($quiz['description']); ?></textarea>
            </div>

            <div id="questions">
                <?php foreach ($quiz['questions'] as $index => $question): ?>
                    <div class="mb-4">
                        <input type="hidden" name="questions[<?php echo $index; ?>][id]" value="<?php echo htmlspecialchars($question['id']); ?>">
                        <h2 class="text-lg mb-2">Question <?php echo $index + 1; ?></h2>
                        <label class="block text-sm mb-2">Question:</label>
                        <input type="text" name="questions[<?php echo $index; ?>][content]" value="<?php echo htmlspecialchars($question['content']); ?>" required class="block w-full p-2 border rounded">
                        <h3 class="text-md mb-2">Answers</h3>
                        <div class="answers">
                            <?php foreach ($question['answers'] as $answerIndex => $answer): ?>
                                <div class="answer mb-2">
                                    <input type="hidden" name="questions[<?php echo $index; ?>][answers][<?php echo $answerIndex; ?>][id]" value="<?php echo htmlspecialchars($answer['id']); ?>">
                                    <input type="text" name="questions[<?php echo $index; ?>][answers][<?php echo $answerIndex; ?>][content]" value="<?php echo htmlspecialchars($answer['content']); ?>" required class="block w-full p-2 border rounded">
                                    <label class="inline-block text-sm mb-2">Correct?</label>
                                    <input type="checkbox" name="questions[<?php echo $index; ?>][answers][<?php echo $answerIndex; ?>][is_correct]" <?php echo $answer['is_correct'] ? 'checked' : ''; ?> class="inline-block ml-2">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" onclick="addAnswer(this, <?php echo $index; ?>)" class="bg-blue-500 text-white rounded p-2">Add Answer</button>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="button" onclick="addQuestion()" class="bg-blue-500 text-white rounded p-2 mb-4">Add Question</button>
            <input type="submit" value="Update Quiz" class="bg-blue-500 text-white rounded p-2">
        </form>
    </main>
    <script>
        let questionIndex = <?php echo count($quiz['questions']); ?>;
        
        function addQuestion() {
            const questionsDiv = document.getElementById('questions');
            const questionDiv = document.createElement('div');
            questionDiv.innerHTML = `
                <h2 class="text-lg mb-2">Question ${questionIndex + 1}</h2>
                <label class="block text-sm mb-2">Question:</label>
                <input type="text" name="questions[${questionIndex}][content]" required class="block w-full p-2 border rounded">
                <h3 class="text-md mb-2">Answers</h3>
                <div class="answer mb-2">
                    <input type="text" name="questions[${questionIndex}][answers][0][content]" required class="block w-full p-2 border rounded">
                    <label class="inline-block text-sm mb-2">Correct?</label>
                    <input type="checkbox" name="questions[${questionIndex}][answers][0][is_correct]" class="inline-block ml-2">
                </div>
                <button type="button" onclick="addAnswer(this, ${questionIndex})" class="bg-blue-500 text-white rounded p-2">Add Answer</button>
            `;
            questionsDiv.appendChild(questionDiv);
            questionIndex++;
        }
        
        function addAnswer(button, questionIndex) {
            const answerDiv = document.createElement('div');
            answerDiv.className = 'answer mb-2';
            const answerIndex = button.parentNode.querySelectorAll('.answer').length;
            answerDiv.innerHTML = `
                <input type="text" name="questions[${questionIndex}][answers][${answerIndex}][content]" required class="block w-full p-2 border rounded">
                <label class="inline-block text-sm mb-2">Correct?</label>
                <input type="checkbox" name="questions[${questionIndex}][answers][${answerIndex}][is_correct]" class="inline-block ml-2">
            `;
            button.parentNode.insertBefore(answerDiv, button);
        }
    </script>
</body>
</html>