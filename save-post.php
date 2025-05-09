<?php
require_once 'C:\xampp\htdocs\projet_webww\config.php';
require_once 'C:\xampp\htdocs\projet_webww\controller\PostController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Retrieve and sanitize input
        $userid = isset($_POST['userid']) ? intval($_POST['userid']) : 1;
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        $posttype = isset($_POST['posttype']) ? trim($_POST['posttype']) : 'text';
        $visibility = isset($_POST['visibility']) ? trim($_POST['visibility']) : 'public';
        $media = null;

        // Handle media file upload
        if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
            $mediaTmpPath = $_FILES['media']['tmp_name'];
            $media = base64_encode(file_get_contents($mediaTmpPath));
        }

        // Create post data array
        $postData = [
            'userid' => $userid,
            'description' => $description,
            'media' => $media,
            'posttype' => $posttype,
            'visibility' => $visibility
        ];

        // Initialize database connection and PostController
        $db = config::getConnexion();
        $postC = new PostController($db);

        // Create the post
        $success = $postC->createPost($postData);

        if ($success) {
            header('Location: feed.php');
            exit;
        } else {
            throw new Exception("Failed to create post");
        }
    } catch (Exception $e) {
        error_log("Error in save-post.php: " . $e->getMessage());
        echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
} else {
    header('Location: feed.php');
    exit;
}
?>