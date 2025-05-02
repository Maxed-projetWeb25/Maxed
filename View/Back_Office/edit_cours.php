<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/launcher/Controller/CoursController.php';

$coursController = new CoursController();

// Get course ID from URL
$coursId = isset($_GET['id']) ? $_GET['id'] : null;

// Fetch course data
$cours = $coursController->getCoursById($coursId);

if (!$cours) {
    header('Location: cours-datatable.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'titre' => $_POST['titre'],
        'description' => $_POST['description']
    ];
    
    if ($coursController->updateCours($coursId, $data)) {
        header('Location: cours-datatable.php');
        exit;
    } else {
        $error = "Failed to update course";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Course</title>
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="form-container">
        <h1>Edit Course</h1>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger" style="color: #ff5c00;"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" id="editCoursForm">
            <div class="form-group">
                <label for="titre">Title:</label>
                <input type="text" id="titre" name="titre" value="<?php echo htmlspecialchars($cours['titre']); ?>" required>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="4" required><?php echo htmlspecialchars($cours['description']); ?></textarea>
            </div>

            <div class="form-group">
                <button type="submit" class="btn-update">Update Course</button>
                <a href="cours-datatable.php" class="back-link">Cancel</a>
            </div>
        </form>
    </div>
    <script>
        document.getElementById('editCoursForm').addEventListener('submit', function(e) {
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
</body>
</html>