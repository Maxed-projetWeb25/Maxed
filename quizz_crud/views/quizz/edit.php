<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Quizz</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="form-container">
    <h1>Edit Quizz Entry</h1>
    <form action="index.php?action=update&id=<?php echo $quizz['id']; ?>" method="post">
      <div class="form-group">
        <label for="titre">Titre:</label>
        <input type="text" id="titre" name="titre" value="<?php echo htmlspecialchars($quizz['titre']); ?>" required>
      </div>
      <div class="form-group">
        <label for="description">Description:</label>
        <textarea id="description" name="description" required><?php echo htmlspecialchars($quizz['description']); ?></textarea>
      </div>
      <div class="form-group">
        <label for="date_creation">Date Creation:</label>
        <input type="date" id="date_creation" name="date_creation" value="<?php echo htmlspecialchars($quizz['date_creation']); ?>" required>
      </div>
      <div class="form-group">
        <label for="categorie">Categorie:</label>
        <input type="text" id="categorie" name="categorie" value="<?php echo htmlspecialchars($quizz['categorie']); ?>" required>
      </div>
      <button type="submit" class="btn-create">Update</button>
    </form>
    <a href="index.php" class="back-link">Back to List</a>
  </div>
</body>
</html>
