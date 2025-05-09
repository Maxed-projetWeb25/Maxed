<?php
require_once __DIR__.'/../../config/config.php';
require_once __DIR__ . '/../../Model/EmailNotification.php';

if (isset($_POST['event_id'])) {
    try {
        $emailNotification = new EmailNotification();
        $result = $emailNotification->sendManualReminder($_POST['event_id']);
        
        $response = [
            'success' => true,
            'message' => 'Reminder email sent successfully!'
        ];
    } catch (Exception $e) {
        $response = [
            'success' => false,
            'message' => 'Error sending reminder: ' . $e->getMessage()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// If no event_id is provided
header('HTTP/1.1 400 Bad Request');
echo json_encode(['success' => false, 'message' => 'No event ID provided']);
exit;
?> 