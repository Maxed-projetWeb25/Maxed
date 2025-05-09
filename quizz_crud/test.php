<?php
require_once 'models/Quizz.php';

$quizzId = isset($_GET['id']) ? (int)$_GET['id'] : 15;

$quizzModel = new Quizz();
$quiz = $quizzModel->getWithDetails($quizzId);

if (!$quiz) {
    echo "<p>Quiz not found.</p>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Take Quizz: <?php echo htmlspecialchars($quiz['titre']); ?></title>
  <link rel="stylesheet" href="style1.css">
  <style>
    .error-msg { color: red; font-size: 0.9em; }
    #timer {
      font-size: 1.2em;
      color: red;
      margin-bottom: 15px;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <div class="quiz-container">
    <h1><?php echo htmlspecialchars($quiz['titre']); ?></h1>
    <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($quiz['description'])); ?></p>

    <!-- Timer Display -->
    <div id="timer">Time Remaining: <span id="time">10:00</span></div>

    <form id="quizForm" action="submit-quizz.php" method="post">
      <input type="hidden" name="id" value="<?php echo $quizzId; ?>">

      <?php foreach ($quiz['questions'] as $i => $question): ?>
        <div class="question-block">
          <p><strong>Question <?php echo $i+1; ?>:</strong> <?php echo nl2br(htmlspecialchars($question['question_text'])); ?></p>

          <?php foreach ($question['answers'] as $answer): ?>
            <div class="answer-option">
              <label>
                <input
                  type="checkbox"
                  name="answers[<?php echo $question['id']; ?>][]"
                  value="<?php echo $answer['id']; ?>"
                  class="q-<?php echo $question['id']; ?>"
                >
                <?php echo htmlspecialchars($answer['answer_text']); ?>
              </label>
            </div>
          <?php endforeach; ?>

          <div class="error-msg" id="error-<?php echo $question['id']; ?>"></div>
        </div>
        <hr>
      <?php endforeach; ?>

      <button type="submit">Submit Quizz</button>
    </form>
  </div>

  <script>
    // Timer validation for answers
    document.getElementById('quizForm').addEventListener('submit', function(e) {
      const questions = document.querySelectorAll('.question-block');
      let isValid = true;

      questions.forEach(block => {
        const checkboxes = block.querySelectorAll('input[type="checkbox"]');
        const questionId = checkboxes[0].classList[0].split('-')[1];
        const checked = Array.from(checkboxes).filter(cb => cb.checked);

        const errorEl = document.getElementById('error-' + questionId);
        errorEl.textContent = '';

        if (checked.length < 1) {
          errorEl.textContent = 'Please select at least one answer.';
          isValid = false;
        } else if (checked.length > 2) {
          errorEl.textContent = 'Please select no more than two answers.';
          isValid = false;
        }
      });

      if (!isValid) {
        e.preventDefault();
      }
    });

    // Timer functionality
    let timeLeft = 600; // 10 minutes = 600 seconds
    const timerDisplay = document.getElementById('time');
    const quizForm = document.getElementById('quizForm');

    function formatTime(seconds) {
      const mins = Math.floor(seconds / 60).toString().padStart(2, '0');
      const secs = (seconds % 60).toString().padStart(2, '0');
      return `${mins}:${secs}`;
    }

    const countdown = setInterval(() => {
      timeLeft--;
      timerDisplay.textContent = formatTime(timeLeft);

      if (timeLeft <= 0) {
        clearInterval(countdown);
        alert("Time is up! Submitting the quiz.");
        quizForm.submit();
      }
    }, 1000);
  </script>
</body>
</html>
