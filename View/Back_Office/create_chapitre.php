<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/launcher/Controller/ChapitreController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/launcher/Controller/CoursController.php';

$coursController = new CoursController();
$allCours = $coursController->getAllCours();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'titre' => $_POST['titre'],
        'contenu' => $_POST['contenu'],
        'id_cours' => $_POST['id_cours']
    ];
    
    $chapitreController = new ChapitreController();
    if ($chapitreController->createChapitre($data)) {
        header('Location: cours-datatable.php');
        exit;
    } else {
        echo "<script>alert('Error creating chapter');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Chapter</title>
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="form-container">
        <h1>Add New Chapter</h1>
        <form method="POST" id="chapitreForm">
            <div class="form-group">
                <label for="titre">Title:</label>
                <input type="text" id="titre" name="titre">
            </div>
            <div class="form-group">
                <label for="contenu">Content:</label>
                <textarea id="contenu" name="contenu" rows="4"></textarea>
            </div>
            <div class="form-group">
                <label for="id_cours">Course:</label>
                <select id="id_cours" name="id_cours">
                    <option value="">Select a course</option>
                    <?php foreach ($allCours as $cours): ?>
                        <option value="<?php echo $cours['id_cours']; ?>"><?php echo htmlspecialchars($cours['titre']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <button type="submit" class="btn-create">Add Chapter</button>
                <a href="cours-datatable.php" class="back-link">Cancel</a>
            </div>
        </form>
    </div>

    <script src="../../../assets/js/formval.js"></script>
    <script>
        document.getElementById('chapitreForm').addEventListener('submit', function(e) {
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

    if (!idCours) {
        errors.push("Veuillez sélectionner un cours.");
    }

    if (errors.length > 0) {
        e.preventDefault();
        alert(errors.join("\n"));
    }
});

    </script>
</body>
</html>