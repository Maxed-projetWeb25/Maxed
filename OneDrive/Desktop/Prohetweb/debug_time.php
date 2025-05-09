<?php
require_once __DIR__ . '/config/config.php';

try {
    $db = Config::getInstance()->getConnection();
    
    // Get current time information
    echo "Current Server Time Information:\n";
    echo "--------------------------------\n";
    echo "Current Time: " . date('H:i:s') . "\n";
    echo "Current Date: " . date('Y-m-d') . "\n";
    echo "Current Timestamp: " . date('Y-m-d H:i:s') . "\n\n";

    // Get all events for today
    $query = "SELECT e.*, 
                     TIME_FORMAT(e.temp_event, '%H:%i:%s') as formatted_time,
                     TIMESTAMPDIFF(MINUTE, 
                         CURRENT_TIMESTAMP, 
                         CONCAT(e.date_event, ' ', TIME_FORMAT(e.temp_event, '%H:%i:%s'))
                     ) as minutes_until_event
              FROM event e
              WHERE e.date_event = CURRENT_DATE
              ORDER BY e.temp_event ASC";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Events Today:\n";
    echo "-------------\n";
    foreach ($events as $event) {
        echo "Event: " . $event['title_event'] . "\n";
        echo "Time: " . $event['temp_event'] . " (Formatted: " . $event['formatted_time'] . ")\n";
        echo "Minutes until event: " . $event['minutes_until_event'] . "\n";
        echo "Should show notification: " . 
             ($event['minutes_until_event'] > 0 && $event['minutes_until_event'] <= 60 ? "YES" : "NO") . "\n";
        echo "--------------------------------\n";
    }

    // Test time comparison
    $query = "SELECT CURRENT_TIME as current_time, 
                     ADDTIME(CURRENT_TIME, '01:00:00') as one_hour_later";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $timeTest = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "\nTime Comparison Test:\n";
    echo "--------------------\n";
    echo "Current Time: " . $timeTest['current_time'] . "\n";
    echo "One Hour Later: " . $timeTest['one_hour_later'] . "\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?> 