<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/Model/EventModel.php';

try {
    $eventModel = new EventModel();
    
    // 1. First get an existing event
    $eventId = 16; // Using the event we created earlier
    $event = $eventModel->getEventById($eventId);
    
    echo "Original event:\n";
    print_r($event);
    
    // 2. Update the event with new data
    $updateData = [
        'title_event' => 'Updated Test Event',
        'desc_event' => 'This event has been updated',
        'temp_event' => '16:30:00', // Change time to 4:30 PM
        'date_event' => date('Y-m-d'), // Today's date
        'loc_event' => 'Updated Location',
        'cap_event' => 200
    ];
    
    $result = $eventModel->updateEvent($eventId, $updateData);
    
    if ($result) {
        echo "\nEvent updated successfully!\n";
        
        // 3. Get the updated event
        $updatedEvent = $eventModel->getEventById($eventId);
        echo "\nUpdated event:\n";
        print_r($updatedEvent);
        
        // 4. Check if notification was updated
        $db = Config::getInstance()->getConnection();
        $query = "SELECT notification_time, status FROM notification WHERE event_id = :event_id";
        $stmt = $db->prepare($query);
        $stmt->execute(['event_id' => $eventId]);
        $notification = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "\nUpdated notification:\n";
        print_r($notification);
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?> 