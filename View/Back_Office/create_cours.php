<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/launcher/Controller/CoursController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'titre' => $_POST['titre'],
        'description' => $_POST['description']
    ];
    
    $coursController = new CoursController();
    if ($coursController->createCours($data)) {
        header('Location: cours-datatable.php');
        exit;
    } else {
        echo "<script>alert('Error creating course');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Course</title>
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="form-container">
        <h1>Add New Course</h1>
        <form method="POST" id="coursForm">
            <div class="form-group">
                <label for="titre">Title:</label>
                <input type="text" id="titre" name="titre">
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="4"></textarea>
            </div>
            <div class="form-group">
                <button type="submit" class="btn-create">Add Course</button>
                <a href="cours-datatable.php" class="back-link">Cancel</a>
            </div>
        </form>
    </div>
    <script src="../../../assets/js/formval.js"></script>

    <script>
        document.getElementById('coursForm').addEventListener('submit', function(e) {
            const titre = document.getElementById('titre').value.trim();
            const description = document.getElementById('description').value.trim();

            let errors = [];

            if (titre.length < 3) {
                errors.push("Le titre doit contenir au moins 3 caractères.");
            }

            if (description.length < 10) {
                errors.push("La description doit contenir au moins 10 caractères.");
            }

            if (errors.length > 0) {
                e.preventDefault();
                alert(errors.join("\n"));
            }
        });
    </script>
    <script src="../../../assets/js/formval.js"></script>
</body>
</html>
