<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Quizz List</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="list-container">
    <h1>All Quizz Entries</h1>
    <a href="index.php?action=create" class="add-link">Add New Quizz</a>
    <br><br>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Titre</th>
          <th>Description</th>
          <th>Date Creation</th>
          <th>Categorie</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = $quizz->fetch(PDO::FETCH_ASSOC)) : ?>
          <tr>
            <td><?php echo htmlspecialchars($row['id']); ?></td>
            <td><?php echo htmlspecialchars($row['titre']); ?></td>
            <td><?php echo htmlspecialchars($row['description']); ?></td>
            <td><?php echo htmlspecialchars($row['date_creation']); ?></td>
            <td><?php echo htmlspecialchars($row['categorie']); ?></td>
           
            <form action="../../../quizz_crud/Back_Office/add-quizz.html" method="get">
    <button type="submit">Return to Add Quizz</button>
</form>


            <td>
              <a href="index.php?action=edit&id=<?php echo $row['id']; ?>" class="action-link">Edit</a> | 
              <a href="index.php?action=delete&id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this entry?');" class="action-link">Delete</a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
