<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once '../config.php';
require_once 'PostController.php';

header('Content-Type: application/json');

try {
    // Debug log
    error_log("Received request: " . print_r($_POST, true));

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    if (!isset($_POST['post_id'])) {
        throw new Exception('Post ID is required');
    }

    $postId = $_POST['post_id'];
    $userId = 1; // Using temporary user ID

    $db = config::getConnexion();
    $postC = new PostController($db);

    // Debug log
    error_log("Toggling like for post: $postId, user: $userId");

    // Toggle the reaction
    $success = $postC->togglePostLike($postId, $userId);

    if ($success) {
        // Get updated counts
        $newCount = $postC->getPostLikesCount($postId);
        $isLiked = $postC->hasUserLikedPost($postId, $userId);

        // Debug log
        error_log("Like toggled successfully. Count: $newCount, IsLiked: " . ($isLiked ? 'true' : 'false'));
        error_log('Post ' . $postId . ' isLiked: ' . ($isLiked ? 'true' : 'false'));

        echo json_encode([
            'success' => true,
            'likeCount' => $newCount,
            'isLiked' => $isLiked
        ]);
    } else {
        error_log("Failed to insert or delete react for post $postId and user $userId");
        throw new Exception('Failed to toggle reaction');
    }
} catch (Exception $e) {
    error_log("Error in handleReaction: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
