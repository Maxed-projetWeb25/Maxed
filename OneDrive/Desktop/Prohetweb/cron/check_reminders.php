<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../Model/EmailNotification.php';

try {
    $emailNotification = new EmailNotification();
    
    // Log start time
    error_log("Starting reminder check at " . date('Y-m-d H:i:s'));
    
    // Check and send reminders
    $result = $emailNotification->checkAndSendReminders();
    
    // Log result
    if ($result) {
        error_log("Reminder check completed successfully");
    } else {
        error_log("Reminder check completed with errors");
    }
    
} catch (Exception $e) {
    error_log("Error in reminder check: " . $e->getMessage());
}
?> 