<?php
require_once 'models/Quizz.php';
require_once 'models/Question.php';
require_once 'models/Answer.php';

if (isset($_GET['id'])) {
    $quizzId = $_GET['id'];

    $quizzModel = new Quizz();
    $quiz = $quizzModel->getWithDetails($quizzId);

    if ($quiz) {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>View Quizz</title>
            <link rel="stylesheet" href="style1.css">
        </head>
        <body>

        
            <div class="quiz-container">
                <h1><?php echo htmlspecialchars($quiz['titre']); ?></h1>
                <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($quiz['description'])); ?></p>
                <p><strong>Category:</strong> <?php echo htmlspecialchars($quiz['categorie']); ?></p>
                <p><strong>Date Created:</strong> <?php echo htmlspecialchars($quiz['date_creation']); ?></p>

                <h2>Questions and Answers</h2>
                <?php foreach ($quiz['questions'] as $question): ?>
                    <div class="question-block">
                        <p><strong>Question:</strong> <?php echo nl2br(htmlspecialchars($question['question_text'])); ?></p>
                        <ul>
                            <?php foreach ($question['answers'] as $answer): ?>
                                <li>
                                    <?php echo nl2br(htmlspecialchars($answer['answer_text'])); ?>
                                    <?php if ($answer['is_correct']): ?>
                                        <span class="correct">[Correct]</span>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </div>
              <!-- Return to Add Quizz button -->
    <form action="../quizz_crud/Front_Office/quiz.html" method="get">
      <button type="submit">Return to Quizz home</button>
    </form>
        </body>
        </html>
        <?php
    } else {
        echo "<p>Quiz not found.</p>";
    }
} else {
    echo "<p>No quiz ID specified.</p>";
}
?>
