<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/components/NotificationLayout.php';

// Initialize the notification layout
$notificationLayout = new NotificationLayout();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Render the notification layout -->
    <?php echo $notificationLayout->render(); ?>

    <div class="container mt-5">
        <h2>Events</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Time</th>
                    <th>Date</th>
                    <th>Location</th>
                    <th>Capacity</th>
                </tr>
            </thead>
            <tbody>
                <?php
                try {
                    $db = Config::getInstance()->getConnection();
                    $query = "SELECT * FROM event ORDER BY date_event, temp_event";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($events as $event) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($event['title_event']) . "</td>";
                        echo "<td>" . htmlspecialchars($event['desc_event']) . "</td>";
                        echo "<td>" . htmlspecialchars($event['temp_event']) . "</td>";
                        echo "<td>" . htmlspecialchars($event['date_event']) . "</td>";
                        echo "<td>" . htmlspecialchars($event['loc_event']) . "</td>";
                        echo "<td>" . htmlspecialchars($event['cap_event']) . "</td>";
                        echo "</tr>";
                    }
                } catch (PDOException $e) {
                    echo "<tr><td colspan='6'>Error loading events: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 