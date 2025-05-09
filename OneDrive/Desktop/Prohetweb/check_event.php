<?php
require_once __DIR__ . '/config/config.php';

try {
    $db = Config::getInstance()->getConnection();
    
    // Check event data
    $query = "SELECT * FROM event";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Events in database:\n\n";
    foreach ($events as $event) {
        echo "Event ID: " . $event['id_event'] . "\n";
        echo "Title: " . $event['title_event'] . "\n";
        echo "Time: " . $event['temp_event'] . "\n";
        echo "Date: " . $event['date_event'] . "\n";
        echo "Location: " . $event['loc_event'] . "\n";
        var_dump($event);
        echo "----------------------------------------\n";
    }
    
} catch (PDOException $e) {
    echo "Error checking events: " . $e->getMessage() . "\n";
}
?> 