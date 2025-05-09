<?php
require_once __DIR__ . '/config/config.php';

try {
    $db = Config::getInstance()->getConnection();
    
    // First, update the existing event times to proper format
    $query = "UPDATE event 
              SET temp_event = TIME_FORMAT(
                  STR_TO_DATE(
                      CONCAT(
                          FLOOR(temp_event), 
                          ':', 
                          (temp_event - FLOOR(temp_event)) * 60,
                          ':00'
                      ),
                      '%H:%i:%s'
                  ),
                  '%H:%i:%s'
              )
              WHERE temp_event IS NOT NULL";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    echo "Updated existing time formats\n";

    // Now alter the table to enforce TIME format
    $query = "ALTER TABLE event MODIFY COLUMN temp_event TIME";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    echo "Modified table structure to use TIME format\n";

    // Verify the changes
    $query = "SELECT id_event, title_event, temp_event FROM event";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "\nCurrent events in database:\n";
    foreach ($events as $event) {
        echo "ID: " . $event['id_event'] . 
             ", Title: " . $event['title_event'] . 
             ", Time: " . $event['temp_event'] . "\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?> 