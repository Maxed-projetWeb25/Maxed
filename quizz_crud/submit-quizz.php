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
    $correctIds = array_column(
        array_filter($question['answers'], fn($a) => $a['is_correct']),
        'id'
    );

    $userIds = array_map('intval', $submitted[$question['id']] ?? []);

    sort($userIds);
    sort($correctIds);

    if ($userIds === $correctIds) {
        $resultType = 'all';
        $correctCount++;
    } else {
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
    .highlight {
      background-color: red;
      color: white;
      padding: 2px 4px;
      border-radius: 2px;
    }
    .faded {
      opacity: 0.3;
      pointer-events: none;
    }
    #searchInput {
      padding: 8px;
      margin: 20px 0;
      width: 100%;
      max-width: 400px;
      border: 1px solid #ccc;
      border-radius: 4px;
      display: block;
    }
  </style>
</head>
<body>
  <div class="quiz-container">

 
    <input type="text" id="searchInput" placeholder="Search questions and answers..." onkeyup="highlightSearch()">

    <h1>Results: <?php echo htmlspecialchars($quiz['titre']); ?></h1>
    <h2>Your Score: <?php echo $correctCount; ?> / <?php echo $totalQuestions; ?></h2>
    <hr>

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

        <?php if ($resultType === 'all'): ?>
          <p class="notice correct">All selected answers are correct!</p>
        <?php elseif ($resultType === 'none'): ?>
          <p class="notice wrong">None of your selected answers were correct.</p>
        <?php else: // some correct ?>
          <?php 
            $correctOnes = $perQuestionResults[$question['id']];
            if (isset($correctOnes['type'])) unset($correctOnes['type']);
          ?>
          <p class="notice">
            The selected correct answer<?php echo count($correctOnes)>1?'s':''; ?>:
            <?php 
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

   
    <form action="../quizz_crud/Front_Office/quiz.html" method="get">
      <button type="submit" style="padding: 10px 20px; background-color:rgb(9, 14, 103); color: white; border: none; border-radius: 5px; cursor: pointer;">Return to Quizz home</button>
    </form> 






    <form action="download-results.php" method="post" style="display:inline-block;">
      <input type="hidden" name="id" value="<?php echo $quizzId; ?>">
      <?php foreach ($submitted as $qId => $answerIds): ?>
        <?php foreach ($answerIds as $aid): ?>
          <input type="hidden" name="answers[<?= $qId ?>][]" value="<?= $aid ?>">
        <?php endforeach; ?>
      <?php endforeach; ?>
      <button type="submit" style="padding: 10px 20px; background-color:rgb(64, 15, 5); color: white; border: none; border-radius: 5px; cursor: pointer;">
        Download Results as PDF
      </button>
    </form>

  </div>


  <script>
    function highlightSearch() {
      const searchTerm = document.getElementById('searchInput').value.trim().toLowerCase();
      const blocks = document.querySelectorAll('.question-block');

      blocks.forEach(block => {
        let matchFound = false;

        const p = block.querySelector('p');
        const originalP = p.textContent;
        p.innerHTML = originalP;
        if (searchTerm && originalP.toLowerCase().includes(searchTerm)) {
          p.innerHTML = originalP.replace(new RegExp(searchTerm, 'gi'), match =>
            `<span class="highlight">${match}</span>`
          );
          matchFound = true;
        }

        const lis = block.querySelectorAll('li');
        lis.forEach(li => {
          const text = li.textContent;
          li.innerHTML = text;
          if (searchTerm && text.toLowerCase().includes(searchTerm)) {
            li.innerHTML = text.replace(new RegExp(searchTerm, 'gi'), match =>
              `<span class="highlight">${match}</span>`
            );
            matchFound = true;
          }
        });

        if (searchTerm && !matchFound) {
          block.classList.add('faded');
        } else {
          block.classList.remove('faded');
        }
      });
    }
  </script>
</body>
</html>
