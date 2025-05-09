<?php
header('Content-Type: application/json');
require_once 'EventReactionController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['eventId']) && isset($data['reactionType'])) {
        $controller = new EventReactionController();
        $result = $controller->handleReaction($data['eventId'], $data['reactionType']);
        echo json_encode($result);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}