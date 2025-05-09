<?php
require_once '../config.php';
require_once 'CommentController.php';

try {
    $db = config::getConnexion();
    $commentC = new CommentController($db);

    $post_id = $_GET['post_id'] ?? null;
    
    if (!$post_id) {
        throw new Exception('Post ID is required');
    }

    $comments = $commentC->getComments($post_id);
    
    if ($comments) {
        foreach ($comments as $comment): ?>
            <div class="comment-item">
                <strong>User #<?php echo htmlspecialchars($comment['userid']); ?>:</strong>
                <?php echo htmlspecialchars($comment['contenu']); ?>
                <br>
                <small><?php echo htmlspecialchars($comment['date_com']); ?></small>
            </div>
        <?php endforeach;
    } else { ?>
        <div class="text-center text-muted">
            <small>No comments yet. Be the first to comment!</small>
        </div>
    <?php }
} catch (Exception $e) {
    echo '<div class="text-center text-danger">
            <small>Error loading comments: ' . htmlspecialchars($e->getMessage()) . '</small>
          </div>';
} 