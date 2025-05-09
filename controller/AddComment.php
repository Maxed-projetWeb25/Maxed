<?php
require_once '../config.php';
require_once 'CommentController.php';

header('Content-Type: application/json');

try {
    $db = config::getConnexion();
    $commentC = new CommentController($db);

    // Get POST data
    $comment_postid = $_POST['comment_postid'] ?? null;
    $comment_userid = 1; // Replace with actual user ID when implementing user system
    $comment_content = trim($_POST['comment_content'] ?? '');

    // Basic validation
    if (empty($comment_content)) {
        throw new Exception('Comment cannot be empty');
    }

    // Add comment
    $success = $commentC->addComment($comment_postid, $comment_userid, $comment_content);
    
    if ($success) {
        echo json_encode([
            'success' => true,
            'message' => 'Comment added successfully'
        ]);
    } else {
        throw new Exception('Failed to add comment');
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
