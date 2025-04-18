<?php
require_once('../../Model/EventModel.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $eventModel = new EventModel();
    $event = $eventModel->getEventById($id);

    if ($event):
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Event Details</title>

    <!-- Stylesheets -->
    <link href="/projet%20web/View/Front_Office/assets/css/bootstrap.css" rel="stylesheet">
    <link href="/projet%20web/View/Front_Office/assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="/projet%20web/View/Front_Office/assets/css/meanmenu.min.css">
    <link href="/projet%20web/View/Front_Office/assets/css/responsive.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="card shadow-lg">
            <div class="card-header bg-primary text-white">
                <h2 class="mb-0"><?php echo htmlspecialchars($event->getTitleEvent()); ?></h2>
            </div>
            <div class="card-body">
                <p class="card-text"><strong>Description:</strong> <?php echo htmlspecialchars($event->getDescEvent()); ?></p>
                <p class="card-text"><strong>Date:</strong> <?php echo htmlspecialchars($event->getDateEvent()); ?></p>
                <p class="card-text"><strong>Location:</strong> <?php echo htmlspecialchars($event->getLocEvent()); ?></p>
                <a href="17c" class="btn btn-outline-secondary mt-3">Back to Events</a>
            </div>
        </div>
    </div>
</body>

</html>
<?php
    else:
        echo "<div style='padding: 2rem; color: red;'>Event not found.</div>";
    endif;
} else {
    echo "<div style='padding: 2rem; color: red;'>No event ID provided.</div>";
}
?>
