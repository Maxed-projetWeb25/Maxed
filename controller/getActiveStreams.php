<?php
require_once '../config.php';

header('Content-Type: application/json');

try {
    $db = config::getConnexion();
    
    // Get active streams
    $sql = "SELECT s.*, u.username, 
            (SELECT COUNT(*) FROM stream_viewers WHERE stream_id = s.id) as viewer_count 
            FROM streams s 
            JOIN users u ON s.user_id = u.id 
            WHERE s.status = 'active' 
            ORDER BY s.created_at DESC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $streams = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'streams' => $streams
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 