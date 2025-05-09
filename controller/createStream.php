<?php
require_once '../config.php';

header('Content-Type: application/json');

try {
    $db = config::getConnexion();
    
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    $title = $data['title'] ?? 'Untitled Stream';
    $description = $data['description'] ?? '';
    $userId = 1; // Replace with actual user ID from session
    
    // Create stream record
    $sql = "INSERT INTO streams (user_id, title, description, status, created_at) 
            VALUES (:user_id, :title, :description, 'active', NOW())";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':user_id' => $userId,
        ':title' => $title,
        ':description' => $description
    ]);
    
    $streamId = $db->lastInsertId();
    
    echo json_encode([
        'success' => true,
        'streamId' => $streamId
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 