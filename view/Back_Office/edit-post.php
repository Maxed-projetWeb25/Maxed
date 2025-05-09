<?php
require_once '../../config.php';
require_once '../../controller/PostController.php';

// Create database connection
$db = config::getConnexion();

// Instantiate the PostController
$postC = new PostController($db);

// Get post ID from query parameter
$postId = $_GET['id'] ?? null;

if (!$postId) {
    die('Post ID is required.');
}

// Fetch post details
$post = $postC->getPostById($postId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $description = $_POST['description'];
    $posttype = $_POST['posttype'];
    $visibility = $_POST['visibility'];
    $media = $post['media'];
    $media_type = $post['media_type'] ?? null;

    // Handle new media upload
    if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
        $media = file_get_contents($_FILES['media']['tmp_name']);
        $media_type = $_FILES['media']['type'];
    }

    $updatedData = [
        'description' => $description,
        'media' => $media,
        'media_type' => $media_type,
        'posttype' => $posttype,
        'visibility' => $visibility
    ];

    $success = $postC->updatePost($postId, $updatedData);

    if ($success) {
        header('Location: feed-datatable.php');
        exit;
    } else {
        $error = 'Failed to update post.';
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Edit Post</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>Edit Post</h1>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" class="form-control" required><?php echo htmlspecialchars($post['description']); ?></textarea>
        </div>
        <div class="mb-3">
            <label for="media" class="form-label">Media</label>
            <?php if (!empty($post['media'])): ?>
                <?php if ($post['posttype'] === 'image'): ?>
                    <img src="data:<?php echo htmlspecialchars($post['media_type']); ?>;base64,<?php echo base64_encode($post['media']); ?>" width="100" alt="Current Image"><br>
                <?php elseif ($post['posttype'] === 'video'): ?>
                    <video width="160" controls>
                        <source src="data:<?php echo htmlspecialchars($post['media_type']); ?>;base64,<?php echo base64_encode($post['media']); ?>">
                        Your browser does not support the video tag.
                    </video><br>
                <?php endif; ?>
            <?php endif; ?>
            <input type="file" id="media" name="media" class="form-control mt-2" accept="image/*,video/*">
            <small class="text-muted">Leave blank to keep current media.</small>
        </div>
        <div class="mb-3">
            <label for="posttype" class="form-label">Post Type</label>
            <select id="posttype" name="posttype" class="form-select" required>
                <option value="text" <?php if ($post['posttype'] === 'text') echo 'selected'; ?>>Text Only</option>
                <option value="image" <?php if ($post['posttype'] === 'image') echo 'selected'; ?>>Image</option>
                <option value="video" <?php if ($post['posttype'] === 'video') echo 'selected'; ?>>Video</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="visibility" class="form-label">Visibility</label>
            <select id="visibility" name="visibility" class="form-select" required>
                <option value="public" <?php if ($post['visibility'] === 'public') echo 'selected'; ?>>Public</option>
                <option value="private" <?php if ($post['visibility'] === 'private') echo 'selected'; ?>>Private</option>
                <option value="friends" <?php if ($post['visibility'] === 'friends') echo 'selected'; ?>>Friends Only</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update Post</button>
        <a href="feed-datatable.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>