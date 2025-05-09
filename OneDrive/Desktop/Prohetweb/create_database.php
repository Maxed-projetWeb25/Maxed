<?php
require_once __DIR__ . '/config/config.php';

try {
    $db = Config::getInstance()->getConnection();
    
    // Create tables in correct order
    $queries = [
        // Create user table
        "CREATE TABLE IF NOT EXISTS `user` (
            `id` INT PRIMARY KEY AUTO_INCREMENT,
            `name` VARCHAR(255) NOT NULL,
            `email` VARCHAR(255) NOT NULL UNIQUE,
            `password` VARCHAR(255) NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        // Create event table
        "CREATE TABLE IF NOT EXISTS `event` (
            `id_event` INT PRIMARY KEY AUTO_INCREMENT,
            `title_event` VARCHAR(255) NOT NULL,
            `desc_event` TEXT,
            `temp_event` TIME,
            `date_event` DATE,
            `loc_event` VARCHAR(255),
            `cap_event` INT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        // Create ticket table
        "CREATE TABLE IF NOT EXISTS `ticket` (
            `id` INT PRIMARY KEY AUTO_INCREMENT,
            `event_id` INT NOT NULL,
            `user_id` INT NOT NULL,
            `type` VARCHAR(50) NOT NULL,
            `price` DECIMAL(10,2) NOT NULL,
            `status` ENUM('valid', 'cancelled', 'used') NOT NULL DEFAULT 'valid',
            FOREIGN KEY (`event_id`) REFERENCES `event`(`id_event`) ON DELETE CASCADE,
            FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        // Create notification table
        "CREATE TABLE IF NOT EXISTS `notification` (
            `id` INT PRIMARY KEY AUTO_INCREMENT,
            `user_id` INT NOT NULL,
            `event_id` INT NOT NULL,
            `notification_time` DATETIME NOT NULL,
            `status` ENUM('pending', 'sent', 'read') NOT NULL DEFAULT 'pending',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE CASCADE,
            FOREIGN KEY (`event_id`) REFERENCES `event`(`id_event`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

        // Insert sample user
        "INSERT INTO `user` (`name`, `email`, `password`) VALUES 
        ('Test User', 'test@test.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi')",

        // Insert sample event
        "INSERT INTO `event` (`title_event`, `desc_event`, `temp_event`, `date_event`, `loc_event`, `cap_event`) VALUES 
        ('Test Event', 'Test Description', '14:00:00', CURDATE(), 'Test Location', 100)"
    ];

    // Execute each query
    foreach ($queries as $query) {
        $db->exec($query);
        echo "Executed query successfully\n";
    }

    echo "Database setup completed successfully!\n";
    
} catch (PDOException $e) {
    echo "Error setting up database: " . $e->getMessage() . "\n";
}
?> 