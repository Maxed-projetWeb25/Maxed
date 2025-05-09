<?php
require_once __DIR__ . '/config/config.php';

header('Content-Type: application/json');

try {
    if (!isset($_POST['event_id'])) {
        throw new Exception('Event ID is required');
    }

    $eventId = intval($_POST['event_id']);
    $db = Config::getInstance()->getConnection();

    // Update notification status to 'read'
    $query = "UPDATE notification 
              SET status = 'read' 
              WHERE event_id = :event_id 
              AND status = 'pending'";
    
    $stmt = $db->prepare($query);
    $stmt->execute([':event_id' => $eventId]);

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
?> 