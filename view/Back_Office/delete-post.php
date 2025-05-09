<?php
require_once '../../config.php';
require_once '../../controller/PostController.php';

session_start();
$db = config::getConnexion();
$postC = new PostController($db);

error_log('POST DATA: ' . print_r($_POST, true));

file_put_contents('delete_debug.txt', print_r($_POST, true), FILE_APPEND);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'])) {
    $postId = $_POST['post_id'];
    $success = $postC->deletePost($postId);

    if ($success) {
        $_SESSION['delete_message'] = "Post deleted successfully.";
    } else {
        $_SESSION['delete_message'] = "Failed to delete post. Please try again.";
    }
}

header('Location: feed-datatable.php');
exit;
