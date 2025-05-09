<?php
require_once 'vendor/autoload.php';        // for DOMPDF
require_once 'models/Quizz.php';

use Dompdf\Dompdf;


$quizzId  = (int) ($_POST['id'] ?? 0);
$submitted = $_POST['answers'] ?? [];

$quizzModel = new Quizz();
$quiz       = $quizzModel->getWithDetails($quizzId);
if (!$quiz) {
    die("Quiz not found.");
}


$totalQuestions   = count($quiz['questions']);
$correctCount     = 0;
$perQuestionResults = [];
foreach ($quiz['questions'] as $question) {
    $correctIds = array_column(
      array_filter($question['answers'], fn($a) => $a['is_correct']),
      'id'
    );
    $userIds = array_map('intval', $submitted[$question['id']] ?? []);
    sort($userIds);  sort($correctIds);

    if ($userIds === $correctIds) {
        $type = 'all';  $correctCount++;
        $hits = $correctIds;
    } else {
        $hits = array_intersect($userIds, $correctIds);
        $type = count($hits) ? 'some' : 'none';
    }

    $perQuestionResults[$question['id']] = [
      'type' => $type,
      'hits' => $hits,
    ];
}

// 2) Build an HTML string for the PDF
ob_start(); 
?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Results: <?= htmlspecialchars($quiz['titre']) ?></title>
  <style>
    body { font-family: sans-serif; }
    h1, h2 { text-align: center; }
    .correct { color: green; }
    .wrong   { color: red; }
    .question { margin-bottom: 1em; }
  </style>
</head>
<body>
  <h1>Results: <?= htmlspecialchars($quiz['titre']) ?></h1>
  <h2>Your Score: <?= $correctCount ?> / <?= $totalQuestions ?></h2>

  <?php foreach ($quiz['questions'] as $i => $q): 
      $res = $perQuestionResults[$q['id']];
  ?>
    <div class="question">
      <strong>Q<?= $i+1 ?>:</strong> <?= htmlspecialchars($q['question_text']) ?><br>
      <?php foreach ($q['answers'] as $a): ?>
        <div>
          <?= in_array($a['id'], $submitted[$q['id']] ?? []) ? '[x]' : '[ ]' ?>
          <?= htmlspecialchars($a['answer_text']) ?>
          <?php if ($a['is_correct']): ?><span class="correct">✔</span><?php endif; ?>
          <?php if (in_array($a['id'], $submitted[$q['id']] ?? []) && !$a['is_correct']): ?>
            <span class="wrong">✖</span>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>

      <?php if ($res['type']==='all'): ?>
        <div class="correct">All correct!</div>
      <?php elseif ($res['type']==='none'): ?>
        <div class="wrong">None correct.</div>
      <?php else: ?>
        <div>Partially correct: 
          <?= implode(', ', array_map(
                fn($aid) => htmlspecialchars(
                  current(array_filter($q['answers'], fn($x)=>$x['id']===$aid))['answer_text']
                ),
                $res['hits']
              ))
          ?>
        </div>
      <?php endif; ?>
    </div>
    <hr>
  <?php endforeach; ?>
</body>
</html>
<?php
$html = ob_get_clean();

// 3) Instantiate DOMPDF and render
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// 4) Stream to browser
$filename = 'quiz-results-' . $quizzId . '.pdf';
$dompdf->stream($filename, ["Attachment" => true]);
exit;
