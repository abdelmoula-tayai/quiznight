<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Quiz</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <form action="createQuiz.php" method="post">
        <h2>Create Quiz</h2>
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>
        <label for="description">Description:</label>
        <textarea id="description" name="description" required></textarea>
        <button type="submit">Create Quiz</button>
    </form>
</body>
</html>
