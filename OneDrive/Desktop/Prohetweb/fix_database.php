<?php
require_once __DIR__ . '/config/config.php';

try {
    $db = Config::getInstance()->getConnection();
    
    // Drop existing notification table if it exists
    $db->exec("DROP TABLE IF EXISTS `notification`");
    
    // Create notification table with correct foreign keys
    $createTableQuery = "CREATE TABLE IF NOT EXISTS `notification` (
        `id` INT PRIMARY KEY AUTO_INCREMENT,
        `user_id` INT NOT NULL,
        `event_id` INT NOT NULL,
        `notification_time` DATETIME NOT NULL,
        `status` ENUM('pending', 'sent', 'read') NOT NULL DEFAULT 'pending',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`event_id`) REFERENCES `event`(`id_event`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $db->exec($createTableQuery);
    echo "Database structure fixed successfully!\n";
    
} catch (PDOException $e) {
    echo "Error fixing database: " . $e->getMessage() . "\n";
}
?> 