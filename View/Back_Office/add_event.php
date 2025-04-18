<?php
require_once '../../Model/EventModel.php';

$model = new EventModel();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title_event']);
    $desc = trim($_POST['desc_event']);
    $time = trim($_POST['time_event']);
    $date = $_POST['date_event'];
    $loc = trim($_POST['loc_event']);
    $cap = $_POST['cap_event'];

    // Contrôle de saisie côté serveur
    if (empty($title) || strlen($title) < 3) {
        $errors[] = "Le titre doit contenir au moins 3 caractères.";
    }
    if (empty($desc)) {
        $errors[] = "La description est obligatoire.";
    }
    if (!preg_match("/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/", $time)) {
        $errors[] = "L'heure doit être au format HH:MM (ex : 14:30).";
    }
    if (!strtotime($date)) {
        $errors[] = "La date est invalide.";
    }
    if (empty($loc)) {
        $errors[] = "Le lieu est obligatoire.";
    }
    if (!is_numeric($cap) || intval($cap) <= 0) {
        $errors[] = "La capacité doit être un nombre positif.";
    }

    if (empty($errors)) {
        $event = new Event(null, $title, $desc, $time, $date, $loc, $cap);
        if ($model->createEvent($event)) {
            header('Location: event-datatable.php');
            exit;
        } else {
            $errors[] = "Échec de l'ajout de l'événement.";
        }
    }
}
?>

<<!doctype html>
<html lang="en" data-bs-theme="blue-theme">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Maxton | Admin Dashboard</title>
  <link rel="icon" href="assets/images/favicon-32x32.png" type="image/png">
  <link href="assets/css/pace.min.css" rel="stylesheet">
  <script src="assets/js/pace.min.js"></script>
  <link href="assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/plugins/metismenu/metisMenu.min.css">
  <link rel="stylesheet" href="assets/plugins/metismenu/mm-vertical.css">
  <link rel="stylesheet" href="assets/plugins/simplebar/css/simplebar.css">
  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Material+Icons+Outlined" rel="stylesheet">
  <link href="assets/css/bootstrap-extended.css" rel="stylesheet">
  <link href="sass/main.css" rel="stylesheet">
  <link href="sass/dark-theme.css" rel="stylesheet">
  <link href="sass/blue-theme.css" rel="stylesheet">
  <link href="sass/semi-dark.css" rel="stylesheet">
  <link href="sass/bordered-theme.css" rel="stylesheet">
  <link href="sass/responsive.css" rel="stylesheet">
    <meta charset="UTF-8">
    <title>Ajouter un événement</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Ajouter un événement</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" id="eventForm">
        <div class="mb-3">
            <label for="title_event" class="form-label">Titre :</label>
            <input type="text" name="title_event" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="desc_event" class="form-label">Description :</label>
            <textarea name="desc_event" class="form-control" required></textarea>
        </div>

        <div class="mb-3">
            <label for="time_event" class="form-label">Heure de l'événement :</label>
            <input type="time" name="time_event" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="date_event" class="form-label">Date :</label>
            <input type="date" name="date_event" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="loc_event" class="form-label">Lieu :</label>
            <input type="text" name="loc_event" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="cap_event" class="form-label">Capacité :</label>
            <input type="number" name="cap_event" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Ajouter</button>
    </form>
</div>

<script>
document.getElementById('eventForm').addEventListener('submit', function(e) {
    const errors = [];

    const title = document.querySelector('input[name="title_event"]').value.trim();
    const desc = document.querySelector('textarea[name="desc_event"]').value.trim();
    const time = document.querySelector('input[name="time_event"]').value.trim();
    const date = document.querySelector('input[name="date_event"]').value;
    const loc = document.querySelector('input[name="loc_event"]').value.trim();
    const cap = document.querySelector('input[name="cap_event"]').value.trim();

    if (title.length < 3) errors.push("Le titre doit contenir au moins 3 caractères.");
    if (desc === "") errors.push("La description est obligatoire.");
    if (!/^([01][0-9]|2[0-3]):[0-5][0-9]$/.test(time)) errors.push("L'heure doit être au format HH:MM.");
    if (!date) errors.push("La date est obligatoire.");
    if (loc === "") errors.push("Le lieu est obligatoire.");
    if (isNaN(cap) || parseInt(cap) <= 0) errors.push("La capacité doit être un nombre positif.");

    if (errors.length > 0) {
        e.preventDefault();
        alert(errors.join("\n"));
    }
});
</script>
</body>
</html>
