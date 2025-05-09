<?php
require_once __DIR__.'/../../config/config.php';
require_once __DIR__ . '/../../Model/EventModel.php';

$eventModel = new EventModel();

// Get event ID from URL
$eventId = isset($_GET['id']) ? $_GET['id'] : null;

if (!$eventId) {
    header('Location: event-datatable.php');
    exit;
}

// Delete the event
if ($eventModel->deleteEvent($eventId)) {
    header('Location: event-datatable.php');
    exit;
} else {
    // Set page title
    $pageTitle = 'Error';

    // Start output buffering
    ob_start();
    ?>

    <div class="card">
        <div class="card-body">
            <div class="alert alert-danger">
                <h5 class="mb-0">Error</h5>
                <p class="mb-0">Failed to delete the event. Please try again.</p>
            </div>
            <a href="event-datatable.php" class="btn btn-secondary">
                <i class="material-icons-outlined">arrow_back</i> Back to List
            </a>
        </div>
    </div>

    <?php
    $content = ob_get_clean();
    require_once 'event-template.php';
}
?>
