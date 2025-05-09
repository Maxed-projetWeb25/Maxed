<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/Model/TicketModel.php';

try {
    // 1. First create a test event for today
    $db = Config::getInstance()->getConnection();
    
    // Create an event 35 minutes from now (so we can test the 30-minute notification)
    $eventTime = date('H:i:s', strtotime('+35 minutes'));
    $query = "INSERT INTO event (title_event, desc_event, temp_event, date_event, loc_event, cap_event) 
              VALUES (:title, :desc, :time, CURDATE(), :loc, :cap)";
    
    $stmt = $db->prepare($query);
    $stmt->execute([
        ':title' => 'Test Notification Event',
        ':desc' => 'This is a test event for notifications',
        ':time' => $eventTime,
        ':loc' => 'Test Location',
        ':cap' => 100
    ]);
    
    $eventId = $db->lastInsertId();
    echo "Created test event with ID: $eventId for time: $eventTime\n";

    // 2. Create a ticket for this event (which should trigger a notification)
    $ticketModel = new TicketModel();
    $userId = 1; // Using the test user we created earlier
    $result = $ticketModel->createTicket($eventId, $userId, 'standard', 50.00, 'valid');
    
    if ($result) {
        echo "Successfully created ticket and notification\n";
    }

    // 3. Check the created notification
    $query = "SELECT * FROM notification WHERE event_id = :event_id";
    $stmt = $db->prepare($query);
    $stmt->execute([':event_id' => $eventId]);
    $notification = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($notification) {
        echo "\nNotification details:\n";
        echo "ID: " . $notification['id'] . "\n";
        echo "User ID: " . $notification['user_id'] . "\n";
        echo "Event ID: " . $notification['event_id'] . "\n";
        echo "Notification Time: " . $notification['notification_time'] . "\n";
        echo "Status: " . $notification['status'] . "\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?> 