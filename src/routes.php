<?php
require_once 'controllers/AuthController.php';
require_once 'controllers/QuizController.php';

$db = (new Database())->getConnection();
$authController = new AuthController($db);
$quizController = new QuizController($db);

// Define your routes and their corresponding controllers/actions here
?>
