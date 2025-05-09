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

// Set page title
$pageTitle = 'Add Event';

// Start output buffering
ob_start();
?>

<div class="card">
    <div class="card-body">
        <div class="d-flex align-items-center mb-3">
            <h5 class="mb-0">Add New Event</h5>
        </div>
        
        <form method="post" action="add_event.php">
            <div class="row mb-3">
                <div class="col-12 col-md-6">
                    <label for="titleEvent" class="form-label">Event Title</label>
                    <input type="text" class="form-control" id="titleEvent" name="titleEvent" required>
                </div>
                <div class="col-12 col-md-6">
                    <label for="locEvent" class="form-label">Location</label>
                    <input type="text" class="form-control" id="locEvent" name="locEvent" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12 col-md-6">
                    <label for="dateEvent" class="form-label">Date</label>
                    <input type="date" class="form-control" id="dateEvent" name="dateEvent" required>
                </div>
                <div class="col-12 col-md-6">
                    <label for="tempEvent" class="form-label">Time</label>
                    <input type="time" class="form-control" id="tempEvent" name="tempEvent" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="descEvent" class="form-label">Description</label>
                <textarea class="form-control" id="descEvent" name="descEvent" rows="4" required></textarea>
            </div>

            <div class="mb-3">
                <label for="capEvent" class="form-label">Capacity</label>
                <input type="number" class="form-control" id="capEvent" name="capEvent" required>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="material-icons-outlined">add</i> Add Event
                </button>
                <a href="event-datatable.php" class="btn btn-secondary">
                    <i class="material-icons-outlined">arrow_back</i> Back to List
                </a>
            </div>
        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once 'event-template.php';
?>
