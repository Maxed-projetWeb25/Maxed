<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create New Quizz</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="form-container">
    <h1>Create New Quizz Entry</h1>

    <form action="store.php" method="post">
      <!-- Quiz Details -->
      <div class="form-group">
        <label for="titre">Titre:</label>
        <input type="text" id="titre" name="titre" required>
      </div>

      <div class="form-group">
        <label for="description">Description:</label>
        <textarea id="description" name="description" required></textarea>
      </div>

      <div class="form-group">
        <label for="date_creation">Date Creation:</label>
        <input type="date" id="date_creation" name="date_creation" required>
      </div>

      <div class="form-group">
        <label for="categorie">Categorie:</label>
        <input type="text" id="categorie" name="categorie" required>
      </div>

      <hr>

      <!-- Questions and Answers -->
      <h2>Questions</h2>
<!--  el question si fiha 4 wla lé -->
      <?php for ($q = 0; $q < 4; $q++): ?> 
        <div class="question-block">
          <label>Question <?= $q + 1 ?>:</label>
          <input type="text" name="questions[<?= $q ?>][text]" required>

          <div class="answers">
            <?php for ($a = 0; $a < 4; $a++): ?>
              <div class="answer-option">
                <label>Answer <?= $a + 1 ?>:</label>
                <input type="text" name="questions[<?= $q ?>][answers][<?= $a ?>][text]" required>
                <label>
                  <input type="checkbox" name="questions[<?= $q ?>][answers][<?= $a ?>][is_correct]" value="1">
                  Correct
                </label>
              </div>
            <?php endfor; ?>
          </div>
        </div>
        <hr>
      <?php endfor; ?>

      <button type="submit" class="btn-create">Create</button>
    </form>

    <a href="index.php" class="back-link">Back to List</a>
  </div>
</body>
</html>
