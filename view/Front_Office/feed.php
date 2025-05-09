<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'C:\xampp\htdocs\projet_webww\config.php';
require_once 'C:\xampp\htdocs\projet_webww\controller\PostController.php';
require_once 'C:\xampp\htdocs\projet_webww\controller\CommentController.php';

session_start(); // Ensure the session is started

$db = config::getConnexion();
$postC = new PostController($db);
$commentC = new CommentController($db);

// Debug information
error_log("Starting feed.php");
var_dump($_POST); // Check POST data
var_dump($posts); // Check posts array

try {
    $testQuery = $db->query("SELECT 1");
    error_log("Database connection test: successful");
} catch (Exception $e) {
    error_log("Database connection test failed: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_post'])) {
    try {
        $description = $_POST['description'] ?? '';
        $posttype = $_POST['posttype'] ?? 'text';
        $visibility = $_POST['visibility'] ?? 'public';
        $media = null;
        $media_type = null;

        // Handle file upload
        if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['media'];
            $media = file_get_contents($file['tmp_name']);
            $media_type = $file['type'];
        }

        $postData = [
            'userid' => 1, // Replace with actual user ID from session
            'description' => $description,
            'media' => $media,
            'media_type' => $media_type,
            'posttype' => $posttype,
            'visibility' => $visibility
        ];

        $success = $postC->createPost($postData);
        if ($success) {
            header("Location: feed.php");
            exit;
        } else {
            $error = "Failed to create post. Please try again. " . (isset($e) ? $e->getMessage() : "");
            if (!$success) {
                $errorInfo = $db->errorInfo();
                error_log("DB Error Info: " . print_r($errorInfo, true));
            }
        }
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment'])) {
    $comment_postid = $_POST['comment_postid'];
    $comment_userid = 1; // Replace with actual user ID from session
    $comment_content = trim($_POST['comment_content']);
    if ($comment_content !== '') {
        $commentC->addComment($comment_postid, $comment_userid, $comment_content);
        header("Location: feed.php");
        exit;
    }
}

// Handle reaction toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_like'])) {
    $post_id = $_POST['post_id'];
    $user_id = 1; // Replace with actual user ID from session when you implement user system
    $postC->togglePostLike($post_id, $user_id);
    header("Location: feed.php");
    exit;
}

// Fetch existing posts
$posts = $postC->getPosts();

// Debug information
error_log("Number of posts fetched: " . count($posts));
if (empty($posts)) {
    error_log("No posts found in database");
} else {
    error_log("Posts found: " . print_r($posts, true));
}

function renderRichMedia($text) {
    $text = preg_replace_callback(
        '/(https?:\/\/[^\s]+)/i',
        function ($matches) {
            $url = $matches[0];

            // YouTube
            if (preg_match('/youtu\.be\/([^\s\/]+)|youtube\.com\/watch\?v=([^\s&]+)/', $url, $yt)) {
                $videoId = !empty($yt[1]) ? $yt[1] : (!empty($yt[2]) ? $yt[2] : '');
                if ($videoId) {
                    return '<div class="embed-responsive embed-responsive-16by9 mb-2">
                        <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/' . htmlspecialchars($videoId) . '" allowfullscreen></iframe>
                    </div>';
                }
            }

            // TikTok (improved)
            if (preg_match('/tiktok\.com\/@([^\/]+)\/video\/(\d+)/', $url, $tt)) {
                $username = $tt[1];
                $videoId = $tt[2];
                return '<blockquote class="tiktok-embed" cite="https://www.tiktok.com/@' . htmlspecialchars($username) . '/video/' . htmlspecialchars($videoId) . '" data-video-id="' . htmlspecialchars($videoId) . '" style="max-width: 605px;min-width: 325px;"><section></section></blockquote>
                <script async src="https://www.tiktok.com/embed.js"></script>';
            }

            // Twitter
            if (preg_match('/twitter\.com\/[^\/]+\/status\/(\d+)/', $url, $tw)) {
                return '<blockquote class="twitter-tweet"><a href="' . htmlspecialchars($url) . '"></a></blockquote>
                <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>';
            }

            // Facebook
            if (preg_match('/facebook\.com\/[^\/]+\/posts\/(\d+)/', $url) || preg_match('/facebook\.com\/photo\.php\?fbid=/', $url)) {
                return '<div class="fb-post" data-href="' . htmlspecialchars($url) . '" data-width="500"></div>
                <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v12.0"></script>';
            }

            // Instagram
            if (preg_match('/instagram\.com\/p\/([A-Za-z0-9_-]+)/', $url)) {
                return '<blockquote class="instagram-media" data-instgrm-permalink="' . htmlspecialchars($url) . '" data-instgrm-version="14"></blockquote>
                <script async src="//www.instagram.com/embed.js"></script>';
            }

            // Image/GIF
            if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $url)) {
                return '<img src="' . htmlspecialchars($url) . '" class="img-fluid mb-2" alt="Embedded Image">';
            }

            // Otherwise, just link
            return '<a href="' . htmlspecialchars($url) . '" target="_blank" rel="noopener noreferrer">' . htmlspecialchars($url) . '</a>';
        },
        $text
    );

    return nl2br($text);
}

function getYouTubeId($url) {
    preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^\&\?\/]+)/', $url, $matches);
    return isset($matches[1]) ? $matches[1] : null;
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Braine Digital Agency Business HTML-5 Template | Blog Classic</title>
<!-- Stylesheets -->
<link href="assets/css/bootstrap.css" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet">
<link href="assets/css/meanmenu.min.css" rel="stylesheet">
<link href="assets/css/responsive.css" rel="stylesheet">

<link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,600;1,700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

<link rel="shortcut icon" href="assets/images/favicon.png" type="image/x-icon">
<link rel="icon" href="assets/images/favicon.png" type="image/x-icon">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- Responsive -->
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">

<style>
.btn-outline-primary .fa-heart {
    transition: color 0.3s ease;
}

.btn-outline-primary .fa-heart.text-danger {
    color: #dc3545;
}

.like-count {
    margin-left: 5px;
    transition: all 0.3s ease;
}

.like-button {
    transition: all 0.3s ease;
}

.like-button:hover {
    transform: scale(1.05);
}

.like-button .fa-heart {
    transition: color 0.3s ease;
}

.like-button .fa-heart.text-danger {
    color: #dc3545 !important;
}

.like-count {
    margin-left: 5px;
}

/* Feed Post Card Styling */
.feed-post-card {
    background: rgba(30, 18, 54, 0.97);
    border-radius: 18px;
    box-shadow: 0 4px 24px rgba(80,0,160,0.10), 0 1.5px 4px rgba(0,0,0,0.10);
    border: 1.5px solid #3a225c;
    margin-bottom: 2rem;
    padding: 1.5rem 2rem;
    transition: box-shadow 0.2s, border 0.2s;
}
.feed-post-card:hover {
    box-shadow: 0 8px 32px rgba(120,0,255,0.18), 0 3px 8px rgba(0,0,0,0.12);
    border: 1.5px solid #a259ff;
}
.feed-post-header {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
}
.feed-post-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    margin-right: 1rem;
    border: 2px solid #a259ff;
    object-fit: cover;
    background: #1e1236;
}
.feed-post-username {
    font-weight: 600;
    color: #a259ff;
    font-size: 1.1rem;
}
.feed-post-date {
    color: #b8a9d1;
    font-size: 0.95rem;
}
.feed-post-body {
    font-size: 1.08rem;
    color: #f3eaff;
    margin-bottom: 1rem;
}
.feed-post-meta {
    display: flex;
    gap: 1rem;
    margin-bottom: 0.5rem;
}
.feed-post-meta .badge {
    background: #2d1a4d;
    color: #a259ff;
    border: 1px solid #3a225c;
}
.feed-post-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 0.5rem;
}
.feed-post-actions .btn {
    border-radius: 8px;
    font-size: 0.98rem;
    padding: 0.35rem 1.1rem;
    background: #2d1a4d;
    color: #a259ff;
    border: 1.5px solid #a259ff;
    transition: background 0.2s, color 0.2s, border 0.2s;
}
.feed-post-actions .btn:hover, 
.feed-post-actions .btn:focus {
    background: #a259ff;
    color: #fff;
    border: 1.5px solid #fff;
}
.feed-post-actions .btn-outline-success {
    border-color: #00e6a2;
    color: #00e6a2;
    background: #1e1236;
}
.feed-post-actions .btn-outline-success:hover, 
.feed-post-actions .btn-outline-success:focus {
    background: #00e6a2;
    color: #1e1236;
    border: 1.5px solid #00e6a2;
}
.feed-post-actions .btn-outline-info {
    border-color: #00cfff;
    color: #00cfff;
    background: #1e1236;
}
.feed-post-actions .btn-outline-info:hover, 
.feed-post-actions .btn-outline-info:focus {
    background: #00cfff;
    color: #1e1236;
    border: 1.5px solid #00cfff;
}

/* Create Post Card Styling */
.create-post-card {
    background: rgba(30, 18, 54, 0.97);
    border-radius: 18px;
    box-shadow: 0 4px 24px rgba(80,0,160,0.10), 0 1.5px 4px rgba(0,0,0,0.10);
    border: 1.5px solid #3a225c;
    margin-bottom: 2rem;
    padding: 1.5rem 2rem;
    color: #f3eaff;
}
.create-post-card .form-control,
.create-post-card .form-select {
    background: #2d1a4d;
    color: #f3eaff;
    border: 1.5px solid #3a225c;
}
.create-post-card .form-control:focus,
.create-post-card .form-select:focus {
    border-color: #a259ff;
    background: #2d1a4d;
    color: #fff;
}
.create-post-card .btn-primary {
    background: #a259ff;
    border: 1.5px solid #a259ff;
    color: #fff;
}
.create-post-card .btn-primary:hover {
    background: #fff;
    color: #a259ff;
    border: 1.5px solid #a259ff;
}

/* Comment Section Styling */
.comments-section {
    background: #241a36;
    border-radius: 12px;
    margin-top: 1.5rem;
    padding: 1rem 1.2rem;
    border: 1px solid #3a225c;
}
.comments-section .comment-form .form-control {
    background: #2d1a4d;
    color: #f3eaff;
    border: 1.5px solid #3a225c;
}
.comments-section .comment-form .form-control:focus {
    border-color: #a259ff;
    background: #2d1a4d;
    color: #fff;
}
.comments-section .comment-form .btn-primary {
    background: #a259ff;
    border: 1.5px solid #a259ff;
    color: #fff;
}
.comments-section .comment-form .btn-primary:hover {
    background: #fff;
    color: #a259ff;
    border: 1.5px solid #a259ff;
}
.comments-section .comments-list .comment-item {
    background: #2d1a4d;
    border-radius: 8px;
    padding: 0.7rem 1rem;
    margin-bottom: 0.7rem;
    color: #e0d6f7;
    border: 1px solid #3a225c;
}
.comments-section .comments-list .comment-item strong {
    color: #a259ff;
}
.comments-section .comments-list .comment-item small {
    color: #b8a9d1;
}

body.high-contrast,
body.high-contrast .feed-post-card,
body.high-contrast .create-post-card,
body.high-contrast .comments-section {
    background: #000 !important;
    color: #fff !important;
}
body.high-contrast .feed-post-card,
body.high-contrast .create-post-card,
body.high-contrast .comments-section {
    border-color: #fff !important;
}
body.high-contrast .btn,
body.high-contrast .form-control,
body.high-contrast .form-select {
    background: #000 !important;
    color: #fff !important;
    border-color: #fff !important;
}
body.high-contrast .badge {
    background: #fff !important;
    color: #000 !important;
}

/* Add these styles to your existing CSS */
.stream-preview {
    background: #2d1a4d;
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1rem;
}

.stream-preview video {
    width: 100%;
    border-radius: 8px;
    background: #1e1236;
}

.stream-info {
    margin-top: 1rem;
    color: #f3eaff;
}

.stream-chat {
    background: #2d1a4d;
    border-radius: 12px;
    padding: 1rem;
}

.chat-messages {
    height: 300px;
    overflow-y: auto;
    background: #1e1236;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
}

.chat-input {
    display: flex;
    gap: 0.5rem;
}

.chat-input input {
    flex: 1;
}

.active-stream-card {
    background: #2d1a4d;
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 1rem;
    cursor: pointer;
    transition: transform 0.2s;
}

.active-stream-card:hover {
    transform: translateY(-2px);
}

.active-stream-card video {
    width: 100%;
    border-radius: 8px;
    background: #1e1236;
}

.active-stream-card .stream-info {
    margin-top: 0.5rem;
}

.livestream-controls {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
}

#startStream {
    background: #dc3545;
    border: none;
}

#startStream:hover {
    background: #c82333;
}

#stopStream {
    background: #6c757d;
    border: none;
}

#stopStream:hover {
    background: #5a6268;
}

.read-aloud-controls {
    display: inline-flex;
    margin-left: 0.5rem;
}

.read-aloud-controls .btn {
    padding: 0.25rem 0.5rem;
}

.read-aloud-controls .btn i {
    font-size: 0.875rem;
}

.reading {
    background-color: rgba(162, 89, 255, 0.1);
    border-color: #a259ff;
}

.reading .fa-volume-up {
    color: #a259ff;
}

.paused {
    background-color: rgba(255, 193, 7, 0.1);
    border-color: #ffc107;
}

.paused .fa-pause {
    color: #ffc107;
}

/* Add these styles to your existing CSS */
.comment-item {
    background: #2d1a4d;
    border-radius: 8px;
    padding: 0.7rem 1rem;
    margin-bottom: 0.7rem;
    color: #e0d6f7;
    border: 1px solid #3a225c;
    transition: all 0.3s ease;
}

.comment-item:hover {
    border-color: #a259ff;
    transform: translateX(5px);
}

.comment-text {
    word-break: break-word;
}

.comment-date {
    color: #b8a9d1;
    font-size: 0.8rem;
}

.is-invalid {
    border-color: #dc3545 !important;
}

.comment-form .input-group {
    position: relative;
}

.comment-form .input-group .btn {
    z-index: 0;
}

.comments-list {
    max-height: 400px;
    overflow-y: auto;
    padding-right: 5px;
}

.comments-list::-webkit-scrollbar {
    width: 6px;
}

.comments-list::-webkit-scrollbar-track {
    background: #1e1236;
    border-radius: 3px;
}

.comments-list::-webkit-scrollbar-thumb {
    background: #a259ff;
    border-radius: 3px;
}

.comments-list::-webkit-scrollbar-thumb:hover {
    background: #8a4dff;
}
</style>

</head>

<body>

<div class="page-wrapper">
	
	<!-- Cursor -->
	<div class="cursor"></div>
	<div class="cursor-follower"></div>
	<!-- Cursor End -->
 	
	<!-- Preloader -->
	
	
	<!-- Main Header -->
	<header class="main-header header-style-two alternate">
		
		<!-- Header Lower -->
		<div class="header-lower">
			<div class="auto-container">
				<div class="inner-container">
					<div class="d-flex align-items-center justify-content-between flex-wrap">
						
						<div class="nav-outer d-flex align-items-center flex-wrap">
							<div class="logo-box">
								<div class="logo"><a href="index.html"><img src="assets/images/logo.svg" alt="" title=""></a></div>
							</div>
							<!-- Main Menu -->
							<nav class="main-menu navbar-expand-md">
								<div class="navbar-header">
									<!-- Toggle Button -->    	
									<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
										<span class="icon-bar"></span>
										<span class="icon-bar"></span>
										<span class="icon-bar"></span>
									</button>
								</div>
								
								<div class="navbar-collapse collapse clearfix" id="navbarSupportedContent">
									<ul class="navigation clearfix">
										<!--<li class="dropdown">-->
											<li><a href="index.html">Home</a></li>
											<!--<ul>
												<li><a href="index.html">HomePage 01</a></li>
												<li><a href="index-2.html">HomePage 02</a></li>
												<li><a href="index-3.html">HomePage 03</a></li>
												<li class="dropdown"><a href="#">Header Style</a>
													<ul>
														<li><a href="index.html">Header 01</a></li>
														<li><a href="index-2.html">Header 02</a></li>
														<li><a href="index-3.html">Header 03</a></li>
													</ul>
												</li>
											</ul>-->
										<!--</li>-->
										<li><a href="about.html">About</a></li>
										<li class="dropdown"><a href="#">options</a>
											<ul>
												<li><a href="faq.html">Faq</a></li>
												<li><a href="pricing.html">Price</a></li>
												<li><a href="testimonial.html">Testimonial</a></li>
												<!--<li><a href="login.html">Login</a></li>
												<li><a href="register.html">Register</a></li>
												<li><a href="reset.html">Forgot password</a></li>-->
												<li class="dropdown"><a href="#">Team</a>
													<ul>
														<li><a href="team-detail.html">Team detail</a></li>
													</ul>
												</li>
											</ul>
										</li>
										<li class="dropdown"><a href="#">Services</a>
											<ul>
												<li><a href="#">Events</a></li>
												<li><a href="course.html">Courses</a></li>
												<li><a href="feed.html">Feed</a></li>
												<li><a href="quiz.html">Quiz</a></li>
												<li><a href="index-3.html">Services detail</a></li>
											</ul>
										</li>
									
										<li><a href="contact.html">Contact</a></li>
									</ul>
								</div>
							</nav>
						</div>

						<!-- Main Menu End-->
						<div class="outer-box d-flex align-items-center flex-wrap">

							<!-- Language DropDown -->
							<div class="language-dropdown">
								<button class="btn dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
									<span class="flag"><img src="assets/images/icons/flag.png" alt="" /></span>&nbsp;<span class="fa-solid fa-angle-down fa-fw"></span>
								  </button>
								<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
									<li><a class="dropdown-item" href="#"><span class="flag"><img src="assets/images/icons/flag.png" alt="" /></span> English</a></li>
									<li><a class="dropdown-item" href="#"><span class="flag"><img src="assets/images/icons/arabic.png" alt="" /></span> Arbic</a></li>
									<li><a class="dropdown-item" href="#"><span class="flag"><img src="assets/images/icons/germany.png" alt="" /></span> German</a></li>
									<li><a class="dropdown-item" href="#"><span class="flag"><img src="assets/images/icons/france.png" alt="" /></span> French</a></li>
								</ul>
							</div>

							<!-- Button Box -->
							<div class="main-header_buttons">
								<a href="#" class="template-btn btn-style-two">
									<span class="btn-wrap">
										<span class="text-one">Login</span>
										<span class="text-two">Login</span>
									</span>
								</a>
								<a href="#" class="template-btn btn-style-one">
									<span class="btn-wrap">
										<span class="text-one">Join now</span>
										<span class="text-two">Join now</span>
									</span>
								</a>
							</div>

							<!-- Mobile Navigation Toggler -->
							<div class="mobile-nav-toggler">
								<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-menu-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 6l16 0" /><path d="M4 12l16 0" /><path d="M4 18l16 0" /></svg>
							</div>

						</div>

					</div>
				</div>
			</div>
		</div>
		<!--End Header Lower-->
		
		<!-- Mobile Menu  -->
		<div class="mobile-menu">
			<div class="menu-backdrop"></div>
			<div class="close-btn"><span class="icon fa-solid fa-xmark fa-fw"></span></div>
			
			<nav class="menu-box">
				<div class="nav-logo"><a href="index.html"><img src="assets/images/mobile-logo.svg" alt="" title=""></a></div>
				<div class="menu-outer"><!--Here Menu Will Come Automatically Via Javascript / Same Menu as in Header--></div>
			</nav>
		</div>
		<!-- End Mobile Menu -->
	
	</header>
	<!-- End Main Header -->
	
	<!-- Page Title -->
    <section class="page-title">
		<div class="page-title-icon" style="background-image:url(assets/images/icons/page-title_icon-1.png)"></div>
		<div class="page-title-icon-two" style="background-image:url(assets/images/icons/page-title_icon-2.png)"></div>
		<div class="page-title-shadow" style="background-image:url(assets/images/background/page-title-1.png)"></div>
		<div class="page-title-shadow_two" style="background-image:url(assets/images/background/page-title-2.png)"></div>
        <div class="auto-container">
			<h2>Feed</h2>
			<ul class="bread-crumb clearfix">
				<li><a href="index.html">Home</a></li>
				<li>Feed</li>
			</ul>
        </div>
    </section>
    <!-- End Page Title -->
        
    <!-- Sidebar Page Container -->
    <div class="sidebar-page-container">
        <div class="auto-container">
            <div class="row clearfix">
                <!-- Content Side -->
                <div class="content-side col-lg-8 col-md-12 col-sm-12">
                    <div class="blog-classic">
                        <!-- Add Post Section -->
                        <div class="container mt-5">
                            <!-- Livestream Section -->
                            <div class="create-post-card mb-4">
                                <div class="feed-post-header">
                                    <img src="assets/images/default-avatar.png" alt="User Avatar" class="feed-post-avatar">
                                    <h5 class="mb-0" style="color:#a259ff;">Start Livestream</h5>
                                </div>
                                <div class="livestream-controls">
                                    <button id="startStream" class="btn btn-danger">
                                        <i class="fas fa-video"></i> Start Livestream
                                    </button>
                                    <button id="stopStream" class="btn btn-secondary" style="display: none;">
                                        <i class="fas fa-stop"></i> End Stream
                                    </button>
                                </div>
                                <div id="streamContainer" class="mt-3" style="display: none;">
                                    <div class="stream-preview">
                                        <video id="localVideo" autoplay muted playsinline></video>
                                        <div class="stream-info">
                                            <h6>Your Livestream</h6>
                                            <p class="viewer-count">Viewers: <span id="viewerCount">0</span></p>
                                        </div>
                                    </div>
                                    <div class="stream-chat mt-3">
                                        <div id="chatMessages" class="chat-messages"></div>
                                        <div class="chat-input">
                                            <input type="text" id="chatInput" placeholder="Type a message..." class="form-control">
                                            <button id="sendMessage" class="btn btn-primary">Send</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Active Livestreams Section -->
                            <div class="active-streams mb-4">
                                <h5 class="mb-3" style="color:#a259ff;">Active Livestreams</h5>
                                <div id="activeStreamsList" class="row">
                                    <!-- Active streams will be populated here -->
                                </div>
                            </div>

                            <div class="create-post-card">
                                <div class="feed-post-header">
                                    <img src="assets/images/default-avatar.png" alt="User Avatar" class="feed-post-avatar">
                                    <h5 class="mb-0" style="color:#a259ff;">Create Post</h5>
                                </div>
                                <form method="POST" enctype="multipart/form-data" action="feed.php">
                                    <div class="input-group mb-3">
                                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="What's on your mind?" required></textarea>
                                        <button type="button" class="btn btn-outline-info" id="voice-to-text-btn" aria-label="Dictate post with voice">
                                            <i class="fas fa-microphone"></i>
                                        </button>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="posttype" class="form-label">Post Type</label>
                                                <select class="form-select" id="posttype" name="posttype" required>
                                                    <option value="text">Text Only</option>
                                                    <option value="image">Image</option>
                                                    <option value="video">Video</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="visibility" class="form-label">Visibility</label>
                                                <select class="form-select" id="visibility" name="visibility" required>
                                                    <option value="public">Public</option>
                                                    <option value="private">Private</option>
                                                    <option value="friends">Friends Only</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3" id="mediaUploadSection" style="display: none;">
                                        <label for="media" class="form-label">Upload Media</label>
                                        <input type="file" class="form-control" id="media" name="media" accept="image/*,video/*">
                                        <small class="text-muted">Max file size: 5MB. Supported formats: JPG, PNG, MP4</small>
                                    </div>
                                    <?php if (isset($error)): ?>
                                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                                    <?php endif; ?>
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" name="create_post" class="btn btn-primary">
                                            <i class="fas fa-paper-plane"></i> Post
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Display Posts Section -->
                        <div class="container mt-4">
                            <div class="row">
                                <?php if (!empty($posts)): ?>
                                    <?php foreach ($posts as $post): ?>
                                        <div class="col-12">
                                            <div class="feed-post-card">
                                                <div class="feed-post-header">
                                                    <img src="assets/images/default-avatar.png" alt="User Avatar" class="feed-post-avatar">
                                                    <div>
                                                        <div class="feed-post-username">User #<?php echo htmlspecialchars($post['userid']); ?></div>
                                                        <div class="feed-post-date"><?php echo date('F j, Y g:i a', strtotime($post['dateposted'])); ?></div>
                                                    </div>
                                                </div>
                                                <div class="feed-post-body">
                                                    <?php echo renderRichMedia(htmlspecialchars($post['description'])); ?>
                                                    
                                                    <?php if (!empty($post['media'])): ?>
                                                        <div class="post-media mb-3">
                                                            <?php if ($post['posttype'] === 'image'): ?>
                                                                <img src="data:<?php echo htmlspecialchars($post['media_type']); ?>;base64,<?php echo base64_encode($post['media']); ?>" 
                                                                     class="img-fluid rounded" alt="Post image"
                                                                     style="max-width: 100%; height: auto;">
                                                            <?php elseif ($post['posttype'] === 'video'): ?>
                                                                <video controls class="img-fluid rounded" style="max-width: 100%;">
                                                                    <source src="data:<?php echo htmlspecialchars($post['media_type']); ?>;base64,<?php echo base64_encode($post['media']); ?>" 
                                                                            type="<?php echo htmlspecialchars($post['media_type']); ?>">
                                                                    Your browser does not support the video tag.
                                                                </video>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endif; ?>

                                                    <div class="feed-post-meta">
                                                        <span class="badge bg-light text-dark me-2">
                                                            <i class="fas fa-globe"></i> <?php echo ucfirst(htmlspecialchars($post['visibility'])); ?>
                                                        </span>
                                                        <span class="badge bg-light text-dark">
                                                            <i class="fas fa-image"></i> <?php echo ucfirst(htmlspecialchars($post['posttype'])); ?>
                                                        </span>
                                                    </div>

                                                    <!-- React, Comment, Repost Section -->
                                                    <div class="feed-post-actions">
                                                        <?php 
                                                        $likeCount = $postC->getPostLikesCount($post['postid']);
                                                        $isLiked = $postC->hasUserLikedPost($post['postid'], 1); // Replace 1 with actual user ID
                                                        ?>
                                                        <button type="button" 
                                                                class="btn btn-outline-primary btn-sm like-button" 
                                                                data-post-id="<?php echo htmlspecialchars($post['postid']); ?>"
                                                                data-liked="<?php echo $isLiked ? 'true' : 'false'; ?>">
                                                            <i class="fas fa-heart <?php echo $isLiked ? 'text-danger' : ''; ?>"></i>
                                                            <span class="like-count"><?php echo $likeCount; ?></span>
                                                        </button>
                                                        <button class="btn btn-outline-success btn-sm" disabled>🔁 Repost</button>
                                                        <button class="btn btn-outline-info btn-sm" onclick="showComments(<?php echo htmlspecialchars($post['postid']); ?>)">
                                                            💬 Comment
                                                        </button>
                                                        <div class="btn-group read-aloud-controls">
                                                            <button class="btn btn-outline-secondary btn-sm read-aloud-btn" type="button" aria-label="Read post aloud">
                                                                <i class="fas fa-volume-up"></i> Read
                                                            </button>
                                                            <button class="btn btn-outline-secondary btn-sm pause-read-btn" type="button" style="display: none;" aria-label="Pause reading">
                                                                <i class="fas fa-pause"></i>
                                                            </button>
                                                            <button class="btn btn-outline-secondary btn-sm stop-read-btn" type="button" style="display: none;" aria-label="Stop reading">
                                                                <i class="fas fa-stop"></i>
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <!-- Comments Section -->
                                                    <div class="comments-section mt-3" id="comments-<?php echo htmlspecialchars($post['postid']); ?>" style="display: none;">
                                                        <form method="POST" class="mb-3 comment-form" data-post-id="<?php echo htmlspecialchars($post['postid']); ?>">
                                                            <input type="hidden" name="comment_postid" value="<?php echo htmlspecialchars($post['postid']); ?>">
                                                            <div class="input-group">
                                                                <input type="text" name="comment_content" class="form-control" placeholder="Write a comment..." required>
                                                                <button type="button" class="btn btn-outline-info voice-comment-btn" aria-label="Dictate comment with voice">
                                                                    <i class="fas fa-microphone"></i>
                                                                </button>
                                                                <button type="submit" class="btn btn-primary btn-sm">Post</button>
                                                            </div>
                                                        </form>
                                                        <div class="comments-list" id="comments-list-<?php echo htmlspecialchars($post['postid']); ?>">
                                                            <?php
                                                            $comments = $commentC->getComments($post['postid']);
                                                            if ($comments):
                                                                foreach ($comments as $comment): ?>
                                                                    <div class="comment-item">
                                                                        <strong>User #<?php echo htmlspecialchars($comment['userid']); ?>:</strong>
                                                                        <?php echo htmlspecialchars($comment['contenu']); ?>
                                                                        <br>
                                                                        <small><?php echo htmlspecialchars($comment['date_com']); ?></small>
                                                                    </div>
                                                                <?php endforeach;
                                                            else: ?>
                                                                <div class="text-center text-muted">
                                                                    <small>No comments yet. Be the first to comment!</small>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="col-12">
                                        <div class="feed-post-card text-center py-5">
                                            <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                                            <h5>No Posts Yet</h5>
                                            <p class="text-muted">Be the first to share something!</p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Side -->
                <div class="sidebar-side col-lg-4 col-md-12 col-sm-12">
                    <aside class="sidebar">
                        <div class="sidebar-inner">
                            <!-- Search Widget -->
                            <div class="sidebar-widget search-box">
                                <div class="widget-content">
                                    <h5 class="sidebar-widget_title">Search here</h5>
                                    <form method="post" action="contact.html">
                                        <div class="form-group">
                                            <input type="search" name="search-field" value="" placeholder="Search..." required>
                                            <button type="submit"><span class="icon fa fa-search"></span></button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Popular tags -->
                            <div class="sidebar-widget popular-tags">
                                <div class="widget-content">
                                    <h5 class="sidebar-widget_title">Popular tags</h5>
                                    <a href="#">DigitalAI</a>
                                    <a href="#">TechInnovate</a>
                                    <a href="#">FutureAI</a>
                                    <a href="#">TechBlog</a>
                                    <a href="#">CodingAI</a>
                                </div>
                            </div>

                            <div class="font-size-controls" style="display:inline-block; margin-left:1rem;">
                              <button id="font-smaller" class="btn btn-sm btn-outline-light" aria-label="Decrease font size">A-</button>
                              <button id="font-default" class="btn btn-sm btn-outline-light" aria-label="Default font size">A</button>
                              <button id="font-bigger" class="btn btn-sm btn-outline-light" aria-label="Increase font size">A+</button>
                            </div>

                            <button id="contrast-toggle" class="btn btn-sm btn-outline-light" aria-pressed="false" aria-label="Toggle high contrast mode">
                              <i class="fas fa-adjust"></i> High Contrast
                            </button>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </div>
    <!-- End Sidebar Page Container -->
    
    <!-- CTA One -->
    <section class="cta-one style-two">
        <div class="cta-one_shadow" style="background-image:url(assets/images/background/cta-shadow.png)"></div>
        <div class="auto-container">
            <div class="inner-container">
                <div class="cta-icon_one" style="background-image:url(assets/images/icons/cta-icon-1.png)"></div>
                <div class="cta-icon_two" style="background-image:url(assets/images/icons/cta-icon-2.png)"></div>
                <div class="cta-one_card">
                    <img src="assets/images/icons/cta-card.png" alt="" />
                </div>
                <div class="row clearfix">
                </div>
            </div>
        </div>
    </section>
    <!-- End CTA One -->

    <!-- Main Footer -->
    <footer class="main-footer">
      
    </footer>
    <!-- End Main Footer -->
</div>

<!-- Scripts -->
<script src="assets/js/jquery.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/meanmenu.min.js"></script>
<script src="assets/js/script.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Media upload handling
    var postTypeSelect = document.getElementById('posttype');
    var mediaSection = document.getElementById('mediaUploadSection');
    var mediaInput = document.getElementById('media');

    function toggleMediaInput() {
        if (postTypeSelect.value === 'image' || postTypeSelect.value === 'video') {
            mediaSection.style.display = 'block';
            if (postTypeSelect.value === 'image') {
                mediaInput.accept = 'image/jpeg,image/png,image/jpg';
            } else if (postTypeSelect.value === 'video') {
                mediaInput.accept = 'video/mp4,video/quicktime,video/x-msvideo';
            }
        } else {
            mediaSection.style.display = 'none';
            mediaInput.value = '';
        }
    }

    // Initial check
    toggleMediaInput();

    // On change
    postTypeSelect.addEventListener('change', toggleMediaInput);

    // File size validation
    mediaInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const maxSize = 5 * 1024 * 1024; // 5MB
            if (file.size > maxSize) {
                alert('File size exceeds 5MB limit');
                this.value = '';
            }
        }
    });

    // Like button handling
    $(document).on('click', '.like-button', function() {
        const button = $(this);
        const postId = button.data('post-id');
        const heartIcon = button.find('.fa-heart');
        const likeCount = button.find('.like-count');
        const isCurrentlyLiked = button.attr('data-liked') === 'true';
        
        $.ajax({
            url: '../../controller/handleReaction.php',
            type: 'POST',
            data: {
                post_id: postId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Update like count
                    likeCount.text(response.likeCount);
                    
                    // Update heart icon and button state
                    if (response.isLiked) {
                        heartIcon.addClass('text-danger');
                        button.attr('data-liked', 'true');
                    } else {
                        heartIcon.removeClass('text-danger');
                        button.attr('data-liked', 'false');
                    }
                } else {
                    console.error('Failed to update like:', response.message);
                    // Revert visual changes if the server request failed
                    heartIcon.toggleClass('text-danger', isCurrentlyLiked);
                    button.attr('data-liked', isCurrentlyLiked ? 'true' : 'false');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                // Revert visual changes if the server request failed
                heartIcon.toggleClass('text-danger', isCurrentlyLiked);
                button.attr('data-liked', isCurrentlyLiked ? 'true' : 'false');
            }
        });
    });

    document.querySelectorAll('.read-aloud-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const postElement = this.closest('.feed-post-card');
            const text = getPostText(postElement);

            if (!text) {
                alert('No text content to read.');
                return;
            }

            // Stop any current speech
            stopCurrentSpeech();

            // Start new speech
            currentPost = postElement;
            currentPost.classList.add('reading');
            updateReadAloudButtons(currentPost, true);

            const utterance = new SpeechSynthesisUtterance(text);
            utterance.rate = 1.0;
            utterance.pitch = 1.0;
            utterance.volume = 1.0;

            // Get available voices and set a good default
            const voices = window.speechSynthesis.getVoices();
            const preferredVoice = voices.find(voice => 
                voice.lang.includes('en') && voice.name.includes('Female')
            ) || voices.find(voice => voice.lang.includes('en')) || voices[0];
            
            if (preferredVoice) {
                utterance.voice = preferredVoice;
            }

            utterance.onend = function() {
                stopCurrentSpeech();
            };

            utterance.onerror = function() {
                stopCurrentSpeech();
                alert('Error reading text. Please try again.');
            };

            currentSpeech = utterance;
            window.speechSynthesis.speak(utterance);
        });
    });

    document.querySelectorAll('.pause-read-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            if (!currentSpeech) return;

            if (isPaused) {
                window.speechSynthesis.resume();
                isPaused = false;
            } else {
                window.speechSynthesis.pause();
                isPaused = true;
            }

            if (currentPost) {
                updateReadAloudButtons(currentPost, true, isPaused);
            }
        });
    });

    document.querySelectorAll('.stop-read-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            stopCurrentSpeech();
        });
    });

    // Handle page visibility changes
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            stopCurrentSpeech();
        }
    });

    // Handle tab/window close
    window.addEventListener('beforeunload', function() {
        stopCurrentSpeech();
    });

    // Enhanced text-to-speech functionality
    let currentSpeech = null;
    let isPaused = false;
    let currentPost = null;

    function getPostText(postElement) {
        const postBody = postElement.querySelector('.feed-post-body');
        if (!postBody) return '';

        // Get all text content, excluding buttons and controls
        const textContent = Array.from(postBody.childNodes)
            .filter(node => {
                // Skip comment sections and buttons
                if (node.classList && 
                    (node.classList.contains('comments-section') || 
                     node.classList.contains('feed-post-actions'))) {
                    return false;
                }
                return true;
            })
            .map(node => node.textContent)
            .join(' ')
            .trim();

        return textContent;
    }

    function updateReadAloudButtons(postElement, isReading = false, isPaused = false) {
        const readBtn = postElement.querySelector('.read-aloud-btn');
        const pauseBtn = postElement.querySelector('.pause-read-btn');
        const stopBtn = postElement.querySelector('.stop-read-btn');

        if (isReading) {
            readBtn.style.display = 'none';
            pauseBtn.style.display = 'inline-block';
            stopBtn.style.display = 'inline-block';
            
            if (isPaused) {
                pauseBtn.classList.add('paused');
                pauseBtn.querySelector('i').classList.remove('fa-pause');
                pauseBtn.querySelector('i').classList.add('fa-play');
            } else {
                pauseBtn.classList.remove('paused');
                pauseBtn.querySelector('i').classList.remove('fa-play');
                pauseBtn.querySelector('i').classList.add('fa-pause');
            }
        } else {
            readBtn.style.display = 'inline-block';
            pauseBtn.style.display = 'none';
            stopBtn.style.display = 'none';
            pauseBtn.classList.remove('paused');
            pauseBtn.querySelector('i').classList.remove('fa-play');
            pauseBtn.querySelector('i').classList.add('fa-pause');
        }
    }

    function stopCurrentSpeech() {
        if (currentSpeech) {
            window.speechSynthesis.cancel();
            currentSpeech = null;
            isPaused = false;
            if (currentPost) {
                currentPost.classList.remove('reading');
                updateReadAloudButtons(currentPost, false);
                currentPost = null;
            }
        }
    }

    const voiceBtn = document.getElementById('voice-to-text-btn');
    const descInput = document.getElementById('description');
    if (voiceBtn && descInput && 'webkitSpeechRecognition' in window) {
        const recognition = new webkitSpeechRecognition();
        recognition.lang = 'en-US'; // Change as needed
        recognition.continuous = false;
        recognition.interimResults = false;

        voiceBtn.addEventListener('click', function() {
            recognition.start();
            voiceBtn.classList.add('recording');
            voiceBtn.innerHTML = '<i class="fas fa-microphone-slash"></i>';
        });

        recognition.onresult = function(event) {
            const transcript = event.results[0][0].transcript;
            descInput.value += (descInput.value ? ' ' : '') + transcript;
        };
        recognition.onend = function() {
            voiceBtn.classList.remove('recording');
            voiceBtn.innerHTML = '<i class="fas fa-microphone"></i>';
        };
        recognition.onerror = function() {
            voiceBtn.classList.remove('recording');
            voiceBtn.innerHTML = '<i class="fas fa-microphone"></i>';
            alert('Voice recognition error or not supported.');
        };
    }

    // Voice-to-text for comments
    document.querySelectorAll('.voice-comment-btn').forEach(function(btn) {
        const commentInput = btn.parentElement.querySelector('input[name="comment_content"]');
        
        if ('webkitSpeechRecognition' in window) {
            const recognition = new webkitSpeechRecognition();
            recognition.lang = 'en-US'; // Change as needed
            recognition.continuous = false;
            recognition.interimResults = false;

            btn.addEventListener('click', function() {
                recognition.start();
                btn.classList.add('recording');
                btn.innerHTML = '<i class="fas fa-microphone-slash"></i>';
            });

            recognition.onresult = function(event) {
                const transcript = event.results[0][0].transcript;
                commentInput.value += (commentInput.value ? ' ' : '') + transcript;
            };

            recognition.onend = function() {
                btn.classList.remove('recording');
                btn.innerHTML = '<i class="fas fa-microphone"></i>';
            };

            recognition.onerror = function() {
                btn.classList.remove('recording');
                btn.innerHTML = '<i class="fas fa-microphone"></i>';
                alert('Voice recognition error or not supported.');
            };
        } else {
            btn.style.display = 'none'; // Hide button if voice recognition is not supported
        }
    });

    // Livestream functionality
    const startStreamBtn = document.getElementById('startStream');
    const stopStreamBtn = document.getElementById('stopStream');
    const streamContainer = document.getElementById('streamContainer');
    const localVideo = document.getElementById('localVideo');
    const viewerCount = document.getElementById('viewerCount');
    const chatMessages = document.getElementById('chatMessages');
    const chatInput = document.getElementById('chatInput');
    const sendMessageBtn = document.getElementById('sendMessage');
    const activeStreamsList = document.getElementById('activeStreamsList');

    let localStream = null;
    let peerConnections = {};
    let streamId = null;

    // Initialize WebRTC
    async function initializeStream() {
        try {
            localStream = await navigator.mediaDevices.getUserMedia({
                video: true,
                audio: true
            });
            localVideo.srcObject = localStream;
            streamContainer.style.display = 'block';
            startStreamBtn.style.display = 'none';
            stopStreamBtn.style.display = 'inline-block';
            
            // Create stream in database and get stream ID
            const response = await fetch('../../controller/createStream.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    title: 'New Livestream',
                    description: 'Started a new livestream'
                })
            });
            const data = await response.json();
            streamId = data.streamId;

            // Start WebSocket connection for signaling
            initializeWebSocket();
        } catch (error) {
            console.error('Error accessing media devices:', error);
            alert('Error accessing camera and microphone. Please ensure you have granted the necessary permissions.');
        }
    }

    function initializeWebSocket() {
        const ws = new WebSocket('ws://your-websocket-server');
        
        ws.onopen = () => {
            console.log('WebSocket connected');
            ws.send(JSON.stringify({
                type: 'broadcaster',
                streamId: streamId
            }));
        };

        ws.onmessage = async (event) => {
            const message = JSON.parse(event.data);
            
            switch(message.type) {
                case 'viewer-joined':
                    handleViewerJoined(message.viewerId);
                    break;
                case 'viewer-left':
                    handleViewerLeft(message.viewerId);
                    break;
                case 'chat-message':
                    handleChatMessage(message);
                    break;
            }
        };
    }

    async function handleViewerJoined(viewerId) {
        const peerConnection = new RTCPeerConnection({
            iceServers: [
                { urls: 'stun:stun.l.google.com:19302' }
            ]
        });

        peerConnections[viewerId] = peerConnection;

        // Add local stream to peer connection
        localStream.getTracks().forEach(track => {
            peerConnection.addTrack(track, localStream);
        });

        // Create and send offer
        const offer = await peerConnection.createOffer();
        await peerConnection.setLocalDescription(offer);

        // Send offer to viewer through signaling server
        // Implementation depends on your signaling server
    }

    function handleViewerLeft(viewerId) {
        if (peerConnections[viewerId]) {
            peerConnections[viewerId].close();
            delete peerConnections[viewerId];
        }
        updateViewerCount();
    }

    function handleChatMessage(message) {
        const messageElement = document.createElement('div');
        messageElement.className = 'chat-message';
        messageElement.innerHTML = `
            <strong>${message.username}:</strong> ${message.content}
        `;
        chatMessages.appendChild(messageElement);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function updateViewerCount() {
        viewerCount.textContent = Object.keys(peerConnections).length;
    }

    function stopStream() {
        if (localStream) {
            localStream.getTracks().forEach(track => track.stop());
        }
        
        Object.values(peerConnections).forEach(pc => pc.close());
        peerConnections = {};
        
        streamContainer.style.display = 'none';
        startStreamBtn.style.display = 'inline-block';
        stopStreamBtn.style.display = 'none';
        
        // Notify server that stream has ended
        fetch('../../controller/endStream.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ streamId })
        });
    }

    // Event listeners
    startStreamBtn.addEventListener('click', initializeStream);
    stopStreamBtn.addEventListener('click', stopStream);
    sendMessageBtn.addEventListener('click', () => {
        const message = chatInput.value.trim();
        if (message) {
            // Send chat message through WebSocket
            // Implementation depends on your signaling server
            chatInput.value = '';
        }
    });

    // Load active streams
    async function loadActiveStreams() {
        try {
            const response = await fetch('../../controller/getActiveStreams.php');
            const streams = await response.json();
            
            activeStreamsList.innerHTML = streams.map(stream => `
                <div class="col-md-6">
                    <div class="active-stream-card" data-stream-id="${stream.id}">
                        <video src="${stream.url}" autoplay></video>
                        <div class="stream-info">
                            <h6>${stream.title}</h6>
                            <p>Viewers: ${stream.viewerCount}</p>
                        </div>
                    </div>
                </div>
            `).join('');
        } catch (error) {
            console.error('Error loading active streams:', error);
        }
    }

    // Load active streams periodically
    loadActiveStreams();
    setInterval(loadActiveStreams, 10000); // Refresh every 10 seconds
});

// Comments handling
function showComments(postId) {
    const commentsSection = document.getElementById(`comments-${postId}`);
    if (commentsSection) {
        commentsSection.style.display = commentsSection.style.display === 'none' ? 'block' : 'none';
    }
}

// Form validation
document.querySelector('form[enctype="multipart/form-data"]').addEventListener('submit', function(e) {
    const postType = document.getElementById('posttype').value;
    const mediaInput = document.getElementById('media');
    
    if ((postType === 'image' || postType === 'video') && !mediaInput.files.length) {
        e.preventDefault();
        alert('Please select a file for your post');
    }
});

$(document).ready(function() {
    // Handle comment form submission
    $(document).on('submit', '.comment-form', function(e) {
        e.preventDefault();
        const form = $(this);
        const postId = form.data('post-id');
        const commentInput = form.find('input[name="comment_content"]');
        const commentContent = commentInput.val().trim();
        const commentsList = $(`#comments-list-${postId}`);

        if (!commentContent) {
            alert('Comment cannot be empty');
            return;
        }

        // Show loading state
        const submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: '../../controller/AddComment.php',
            type: 'POST',
            data: {
                comment_postid: postId,
                comment_content: commentContent
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Clear input
                    commentInput.val('');

                    // Reload comments section
                    $.get(`../../controller/getComments.php?post_id=${postId}`, function(html) {
                        commentsList.html(html);
                    });
                } else {
                    alert(response.message || 'Failed to add comment');
                }
            },
            error: function() {
                alert('Error submitting comment. Please try again.');
            },
            complete: function() {
                // Reset button state
                submitBtn.prop('disabled', false).html('Post');
            }
        });
    });
});

document.getElementById('contrast-toggle').addEventListener('click', function() {
    document.body.classList.toggle('high-contrast');
    this.setAttribute('aria-pressed', document.body.classList.contains('high-contrast'));
});
</script>

<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
<script async src="//www.instagram.com/embed.js"></script>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v12.0"></script>
<script async src="https://www.tiktok.com/embed.js"></script>
</body>
</html>
