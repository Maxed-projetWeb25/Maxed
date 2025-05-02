<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/launcher/Controller/ChapitreController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/launcher/Controller/CoursController.php';

$chapitreController = new ChapitreController();
$coursController = new CoursController();

// Get chapter ID from URL
$chapitreId = isset($_GET['id']) ? $_GET['id'] : null;

// Fetch chapter data
$chapitre = $chapitreController->getChapitreById($chapitreId);

if (!$chapitre) {
    header('Location: cours-datatable.php');
    exit;
}

// Get all courses for dropdown
$allCours = $coursController->getAllCours();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'titre' => $_POST['titre'],
        'contenu' => $_POST['contenu'],
        'id_cours' => $_POST['id_cours']
    ];
    
    if ($chapitreController->updateChapitre($chapitreId, $data)) {
        header('Location: cours-datatable.php');
        exit;
    } else {
        $error = "Failed to update chapter";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Chapter</title>
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="form-container">
    <h1>Edit Chapter</h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger" style="color: #ff5c00;"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" id="editChapitreForm">
        <div class="form-group">
            <label for="titre">Title:</label>
            <input type="text" id="titre" name="titre" value="<?php echo htmlspecialchars($chapitre['titre']); ?>">
        </div>

        <div class="form-group">
            <label for="contenu">Content:</label>
            <textarea id="contenu" name="contenu" rows="4"><?php echo htmlspecialchars($chapitre['contenu']); ?></textarea>
        </div>

        <div class="form-group">
            <label for="id_cours">Course:</label>
            <select id="id_cours" name="id_cours">
                <?php foreach ($allCours as $cours): ?>
                    <option value="<?php echo $cours['id_cours']; ?>" <?php echo ($cours['id_cours'] == $chapitre['id_cours']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cours['titre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <button type="submit" class="btn-update">Update Chapter</button>
            <a href="cours-datatable.php" class="back-link">Cancel</a>
        </div>
    </form>
</div>
    <script>
        document.getElementById('editChapitreForm').addEventListener('submit', function(e) {
        const titre = document.getElementById('titre').value.trim();
        const contenu = document.getElementById('contenu').value.trim();
        const idCours = document.getElementById('id_cours').value;

        let errors = [];

        if (titre.length < 3) {
            errors.push("Le titre doit contenir au moins 3 caractères.");
        }

        if (contenu.length < 10) {
            errors.push("Le contenu doit contenir au moins 10 caractères.");
        }

        if (errors.length > 0) {
            e.preventDefault();
            alert(errors.join("\n"));
        }
});
    </script>
</body>
</html>