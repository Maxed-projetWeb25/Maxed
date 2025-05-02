<?php
require_once 'models/Quizz.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
    echo "<p>Invalid request.</p>";
    exit;
}

$quizzId  = (int) $_POST['id'];
$submitted = $_POST['answers'] ?? [];

$quizzModel = new Quizz();
$quiz = $quizzModel->getWithDetails($quizzId);

if (!$quiz) {
    echo "<p>Quiz not found.</p>";
    exit;
}

// Calculate total score
$totalQuestions = count($quiz['questions']);
$correctCount   = 0;
$perQuestionResults = [];

foreach ($quiz['questions'] as $question) {
    // IDs of all correct answers
    $correctIds = array_column(
        array_filter($question['answers'], fn($a) => $a['is_correct']),
        'id'
    );

    // IDs the user selected
    $userIds = array_map('intval', $submitted[$question['id']] ?? []);

    // Count how many of user's picks are exactly the correct set
    sort($userIds);
    sort($correctIds);

    if ($userIds === $correctIds) {
        // Fully correct
        $resultType = 'all';
        $correctCount++;
    } else {
        // Partially or wholly incorrect
        // Find which selected ones are correct
        $intersection = array_intersect($userIds, $correctIds);
        if (count($intersection) === 0) {
            $resultType = 'none';
        } else {
            $resultType = 'some';
            $perQuestionResults[$question['id']] = $intersection;
        }
    }
    $perQuestionResults[$question['id']]['type'] = $resultType;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Results: <?php echo htmlspecialchars($quiz['titre']); ?></title>
  <link rel="stylesheet" href="style1.css">
  <style>
    .correct { color: green; }
    .wrong   { color: red; }
    .notice  { font-style: italic; margin-bottom: 1em; }
  </style>
</head>
<body>
  <div class="quiz-container">

    <!-- 1. Total Score -->
    <h1>Results: <?php echo htmlspecialchars($quiz['titre']); ?></h1>
    <h2>Your Score: <?php echo $correctCount; ?> / <?php echo $totalQuestions; ?></h2>
    <hr>

    <!-- 2. Per-Question Feedback -->
    <?php foreach ($quiz['questions'] as $idx => $question): 
        $resultType = $perQuestionResults[$question['id']]['type'];
    ?>
      <div class="question-block">
        <p>
          <strong>Question <?php echo $idx + 1; ?>:</strong>
          <?php echo nl2br(htmlspecialchars($question['question_text'])); ?>
        </p>

        <ul>
          <?php foreach ($question['answers'] as $answer):
              $sel = in_array($answer['id'], $submitted[$question['id']] ?? []);
              $cor = (bool)$answer['is_correct'];
          ?>
            <li>
              <input type="checkbox" disabled <?php echo $sel ? 'checked' : ''; ?>>
              <?php echo htmlspecialchars($answer['answer_text']); ?>
              <?php if ($cor): ?>
                <span class="correct">✔</span>
              <?php endif; ?>
              <?php if ($sel && !$cor): ?>
                <span class="wrong">✖</span>
              <?php endif; ?>
            </li>
          <?php endforeach; ?>
        </ul>

        <!-- Feedback message -->
        <?php if ($resultType === 'all'): ?>
          <p class="notice correct">All selected answers are correct!</p>
        <?php elseif ($resultType === 'none'): ?>
          <p class="notice wrong">None of your selected answers were correct.</p>
        <?php else: // some correct ?>
          <?php 
            $correctOnes = $perQuestionResults[$question['id']];
            // only IDs, filter out the 'type' key if present
            if (isset($correctOnes['type'])) {
              unset($correctOnes['type']);
            }
          ?>
          <p class="notice">
            the selected  correct answer<?php echo count($correctOnes)>1?'s':''; ?> :
            <?php 
              // display text of each correct selected answer
              $texts = array_map(fn($aid) => 
                htmlspecialchars(
                  current(array_filter(
                    $question['answers'], fn($a) => $a['id']===$aid
                  ))['answer_text']
                )
              , $correctOnes);
              echo implode(', ', $texts);
            ?>
          </p>
        <?php endif; ?>
      </div>
      <hr>
    <?php endforeach; ?>

    <!-- Navigation -->
    <form action="../quizz_crud/Front_Office/quiz.html" method="get">
      <button type="submit">Return to Quizz home</button>
    </form>
  </div>
</body>
</html>
