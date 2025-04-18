<?php
require_once '../../Model/EventModel.php';

$model = new EventModel();

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $event = $model->getEventById($id);

    if (!$event) {
        echo "<div class='alert alert-danger m-3'>Event not found.</div>";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event = new Event(
        $_POST['id_event'],
        $_POST['title_event'],
        $_POST['desc_event'],
        $_POST['time_event'], // changed from temp_event
        $_POST['date_event'],
        $_POST['loc_event'],
        $_POST['cap_event']
    );

    if ($model->updateEvent($event)) {
        header('Location: event-datatable.php');
        exit;
    } else {
        $error = "Failed to update event.";
    }
}
?>

<!doctype html>
<html lang="en" data-bs-theme="blue-theme">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Update Event | Admin Dashboard</title>
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
</head>

<body>

<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Update Event</h4>
        </div>
        <div class="card-body">

            <form method="POST" class="needs-validation" novalidate>
                <input type="hidden" name="id_event" value="<?php echo htmlspecialchars($event->getIdEvent()); ?>">

                <div class="mb-3">
                    <label for="title_event" class="form-label">Event Title</label>
                    <input type="text" class="form-control" id="title_event" name="title_event" value="<?php echo htmlspecialchars($event->getTitleEvent()); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="desc_event" class="form-label">Event Description</label>
                    <textarea class="form-control" id="desc_event" name="desc_event" rows="3" required><?php echo htmlspecialchars($event->getDescEvent()); ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="time_event" class="form-label">Event Time (in minutes)</label>
                    <input type="text" class="form-control" id="time_event" name="time_event" value="<?php echo htmlspecialchars($event->getTempEvent()); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="date_event" class="form-label">Event Date</label>
                    <input type="date" class="form-control" id="date_event" name="date_event" value="<?php echo htmlspecialchars($event->getDateEvent()); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="loc_event" class="form-label">Event Location</label>
                    <input type="text" class="form-control" id="loc_event" name="loc_event" value="<?php echo htmlspecialchars($event->getLocEvent()); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="cap_event" class="form-label">Event Capacity</label>
                    <input type="number" class="form-control" id="cap_event" name="cap_event" value="<?php echo htmlspecialchars($event->getCapEvent()); ?>" required>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-success">Update Event</button>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger mt-3"><?php echo $error; ?></div>
                <?php endif; ?>
            </form>

        </div>
    </div>
</div>

<!-- JavaScript: Contrôle de saisie -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const capEventInput = document.getElementById('cap_event');
    const timeEventInput = document.getElementById('time_event');

    capEventInput.addEventListener('input', function() {
        capEventInput.value = capEventInput.value.replace(/[^0-9]/g, '');
    });

    timeEventInput.addEventListener('input', function() {
        timeEventInput.value = timeEventInput.value.replace(/[^0-9]/g, '');
    });
});
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    const capEventInput = document.getElementById('cap_event');
    const timeEventInput = document.getElementById('time_event');

    // Fonction utilitaire pour afficher un message d'erreur
    function showError(input, message) {
        let errorDiv = input.nextElementSibling;
        if (!errorDiv || !errorDiv.classList.contains('invalid-feedback')) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback d-block';
            input.parentNode.appendChild(errorDiv);
        }
        errorDiv.textContent = message;
        input.classList.add('is-invalid');
    }

    // Fonction utilitaire pour effacer les erreurs
    function clearError(input) {
        let errorDiv = input.nextElementSibling;
        if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
            errorDiv.textContent = '';
        }
        input.classList.remove('is-invalid');
    }

    // Validation en live pour les champs numériques
    function validateNumeric(input, name) {
        input.addEventListener('input', function () {
            input.value = input.value.replace(/[^0-9]/g, '');
            if (input.value.trim() === '') {
                showError(input, name + ' is required.');
            } else {
                clearError(input);
            }
        });
    }

    validateNumeric(capEventInput, 'Capacity');
    validateNumeric(timeEventInput, 'Time');

    // Empêche la soumission si un champ est vide ou invalide
    form.addEventListener('submit', function (e) {
        let hasError = false;

        if (capEventInput.value.trim() === '') {
            showError(capEventInput, 'Capacity is required.');
            hasError = true;
        }

        if (timeEventInput.value.trim() === '') {
            showError(timeEventInput, 'Time is required.');
            hasError = true;
        }

        if (hasError) {
            e.preventDefault();
        }
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    const timeEventInput = document.getElementById('time_event');

    // Fonction pour afficher l'erreur
    function showError(input, message) {
        let errorDiv = input.nextElementSibling;
        if (!errorDiv || !errorDiv.classList.contains('invalid-feedback')) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback d-block';
            input.parentNode.appendChild(errorDiv);
        }
        errorDiv.textContent = message;
        input.classList.add('is-invalid');
    }

    // Fonction pour retirer l'erreur
    function clearError(input) {
        let errorDiv = input.nextElementSibling;
        if (errorDiv && errorDiv.classList.contains('invalid-feedback')) {
            errorDiv.textContent = '';
        }
        input.classList.remove('is-invalid');
    }

    // Validation en temps réel pour le champ time_event
    timeEventInput.addEventListener('input', function () {
        timeEventInput.value = timeEventInput.value.replace(/[^0-9]/g, '');

        const value = parseInt(timeEventInput.value);
        if (isNaN(value) || value <= 0) {
            showError(timeEventInput, "Le temps de l'événement doit être un nombre supérieur à 0.");
        } else {
            clearError(timeEventInput);
        }
    });

    // Vérification finale avant soumission
    form.addEventListener('submit', function (e) {
        const value = parseInt(timeEventInput.value);
        if (isNaN(value) || value <= 0) {
            showError(timeEventInput, "Veuillez entrer un temps valide supérieur à 0.");
            e.preventDefault();
        }
    });
});
</script>

</script>


</body>
</html>
