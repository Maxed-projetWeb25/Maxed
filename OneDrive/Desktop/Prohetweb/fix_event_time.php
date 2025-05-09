<?php
require_once __DIR__ . '/config/config.php';

try {
    $db = Config::getInstance()->getConnection();
    
    // First, add email column if it doesn't exist
    $query = "ALTER TABLE event ADD COLUMN IF NOT EXISTS email VARCHAR(255)";
    $db->exec($query);
    
    // Update the temp_event column to proper TIME format
    $query = "ALTER TABLE event MODIFY COLUMN temp_event TIME NOT NULL";
    $db->exec($query);
    
    echo "Database structure updated successfully!\n";
    
    // Now let's check current events
    $query = "SELECT id_event, title_event, temp_event, email FROM event";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nCurrent events in database:\n";
    foreach ($events as $event) {
        echo "ID: " . $event['id_event'] . 
             ", Title: " . $event['title_event'] . 
             ", Time: " . $event['temp_event'] . 
             ", Email: " . ($event['email'] ?? 'Not set') . "\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?> 