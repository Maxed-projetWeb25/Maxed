<?php
require_once 'C:\xampp\htdocs\projet_web\config.php';
require_once 'C:\xampp\htdocs\projet_web\controller\PostController.php';

session_start();

// Create database connection
$db = config::getConnexion();

// Instantiate PostController
$postC = new PostController($db);

// Fetch posts
$posts = $postC->getPosts(); // Assuming getPosts() fetches all posts

?>

<!DOCTYPE html>
<html>
<head>
    <title>Feed Data Table</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Feed Data Table</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Description</th>
                    <th>Media</th>
                    <th>Post Type</th>
                    <th>Visibility</th>
                    <th>Date Posted</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($posts as $post): ?>
                <tr>
                    <td><?php echo htmlspecialchars($post['userid']); ?></td>
                    <td><?php echo htmlspecialchars($post['description']); ?></td>
                    <td>
                        <?php if ($post['media']): ?>
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($post['media']); ?>" alt="Post Media" width="100">
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($post['posttype']); ?></td>
                    <td><?php echo htmlspecialchars($post['visibility']); ?></td>
                    <td><?php echo htmlspecialchars($post['date-posted']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

