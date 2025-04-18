<?php
require_once 'models/Quizz.php';
require_once 'models/Question.php';
require_once 'models/Answer.php';

// lezm  submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $date_creation = $_POST['date_creation'];
    $categorie = $_POST['categorie'];
    $questions = $_POST['questions'];

    
    $quizzModel = new Quizz();
    $questionModel = new Question();
    $answerModel = new Answer();

    // Create quiz
    if ($quizzModel->create($titre, $description, $date_creation, $categorie)) {
        $quizzId = $quizzModel->getLastInsertId(); // get inserted quiz 

        foreach ($questions as $questionData) {
            $questionText = $questionData['text'];
            $questionModel->create($quizzId, $questionText);
            $questionId = $questionModel->getLastInsertId();

            foreach ($questionData['answers'] as $answerData) {
                $answerText = $answerData['text'];
                $isCorrect = isset($answerData['is_correct']) ? 1 : 0;

                $answerModel->create($questionId, $answerText, $isCorrect);
            }
        }

        // Redirect to index
        header('Location: index.php');
        exit;
    } else {
        echo "Failed to create the quiz.";
    }
}
?>
