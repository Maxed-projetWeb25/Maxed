<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/Model/TicketModel.php';

try {
    $db = Config::getInstance()->getConnection();
    
    // Set event time to 3:00 PM with proper TIME format
    $eventTime = '15:00:00';
    
    // First, delete any existing test events
    $query = "DELETE FROM event WHERE title_event = '3PM Test Event'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    // Create new event
    $query = "INSERT INTO event (title_event, desc_event, temp_event, date_event, loc_event, cap_event) 
              VALUES (:title, :desc, :time, CURDATE(), :loc, :cap)";
    
    $stmt = $db->prepare($query);
    $stmt->execute([
        ':title' => '3PM Test Event',
        ':desc' => 'This is a test event at 3:00 PM',
        ':time' => $eventTime,
        ':loc' => 'Test Location',
        ':cap' => 100
    ]);
    
    $eventId = $db->lastInsertId();
    echo "Created test event with ID: $eventId for time: $eventTime\n";

    // Create a ticket for this event (which will create a notification)
    $ticketModel = new TicketModel();
    $userId = 1; // Using the test user
    $result = $ticketModel->createTicket($eventId, $userId, 'standard', 50.00, 'valid');
    
    if ($result) {
        echo "Successfully created ticket and notification\n";
    }

    // Debug: Check the created event and notification
    $query = "SELECT e.*, n.notification_time, n.status,
                     TIMESTAMPDIFF(MINUTE, 
                         CURRENT_TIMESTAMP, 
                         CONCAT(e.date_event, ' ', e.temp_event)
                     ) as minutes_until_event
              FROM event e 
              LEFT JOIN notification n ON e.id_event = n.event_id 
              WHERE e.id_event = :event_id";
    $stmt = $db->prepare($query);
    $stmt->execute([':event_id' => $eventId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "\nDebug Information:\n";
    echo "Event Time: " . $result['temp_event'] . "\n";
    echo "Event Date: " . $result['date_event'] . "\n";
    echo "Minutes until event: " . $result['minutes_until_event'] . "\n";
    echo "Notification Time: " . ($result['notification_time'] ?? 'No notification time set') . "\n";
    echo "Notification Status: " . ($result['status'] ?? 'No status') . "\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?> 