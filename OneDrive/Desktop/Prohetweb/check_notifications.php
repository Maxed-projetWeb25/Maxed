<?php
require_once __DIR__ . '/config/config.php';

try {
    $db = Config::getInstance()->getConnection();
    
    // Get all notifications with event details
    $query = "SELECT n.*, e.title_event, e.temp_event, e.date_event, u.email 
              FROM notification n
              JOIN event e ON n.event_id = e.id_event
              JOIN user u ON n.user_id = u.id
              ORDER BY n.notification_time ASC";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "All notifications in system:\n\n";
    foreach ($notifications as $notification) {
        echo "Notification ID: " . $notification['id'] . "\n";
        echo "Event: " . $notification['title_event'] . "\n";
        echo "User Email: " . $notification['email'] . "\n";
        echo "Event Date: " . $notification['date_event'] . "\n";
        echo "Event Time: " . $notification['temp_event'] . "\n";
        echo "Notification Time: " . $notification['notification_time'] . "\n";
        echo "Status: " . $notification['status'] . "\n";
        echo "----------------------------------------\n";
    }
    
} catch (PDOException $e) {
    echo "Error checking notifications: " . $e->getMessage() . "\n";
}
?> 