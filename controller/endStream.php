<?php
require_once '../config.php';

header('Content-Type: application/json');

try {
    $db = config::getConnexion();
    
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    $streamId = $data['streamId'] ?? null;
    
    if (!$streamId) {
        throw new Exception('Stream ID is required');
    }
    
    // Update stream status
    $sql = "UPDATE streams SET status = 'ended', ended_at = NOW() WHERE id = :stream_id";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([':stream_id' => $streamId]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Stream ended successfully'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 