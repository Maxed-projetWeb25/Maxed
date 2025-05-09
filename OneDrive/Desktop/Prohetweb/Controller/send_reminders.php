<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../Model/NotificationModel.php';

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get event ID from POST data
    $event_id = isset($_POST['event_id']) ? (int)$_POST['event_id'] : 0;
    
    if ($event_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid event ID']);
        exit;
    }

    // Create notification model instance
    $notificationModel = new NotificationModel();
    
    // Send reminders
    $result = $notificationModel->sendEventReminders($event_id);
    
    // Log the result for debugging
    error_log("Send reminders result for event $event_id: " . print_r($result, true));
    
    if (isset($result['success'])) {
        echo json_encode([
            'success' => $result['success'],
            'message' => $result['message'],
            'details' => [
                'total' => $result['total'] ?? 0,
                'sent' => $result['sent'] ?? 0,
                'errors' => $result['errors'] ?? []
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Unexpected error occurred while sending reminders'
        ]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?> 