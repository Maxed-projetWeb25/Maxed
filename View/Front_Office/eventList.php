<?php
// Include config file for database connection
require_once 'C:/xampp/htdocs/projet web/config/config.php';  // Ensure the correct path to your config file

// Get the database connection
$database = config::getConnexion();

// Fetch events from the database
$query = $database->query("SELECT * FROM event");
$events = $query->fetchAll();

// Check if events are available
if (empty($events)) {
    echo "<p>No events available.</p>";
    exit; // Stop further script execution if no events
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Event List</title>

    <!-- Stylesheets -->
    <link href="/projet%20web/View/Front_Office/assets/css/bootstrap.css" rel="stylesheet">
    <link href="/projet%20web/View/Front_Office/assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="/projet%20web/View/Front_Office/assets/css/meanmenu.min.css">
    <link href="/projet%20web/View/Front_Office/assets/css/responsive.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,600;1,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Favicon -->
    <link rel="shortcut icon" href="View/Front_Office/assets/images/favicon.png" type="image/x-icon">
    <link rel="icon" href="View/Front_Office/assets/images/favicon.png" type="image/x-icon">

    <!-- Responsive Meta Tags -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">

    <!-- Inline CSS to enforce white text color and background adjustments -->
    <style>
        body {
            background-color: #333333; /* Dark background for contrast */
            color: #ffffff; /* White text */
            font-family: 'Inter', sans-serif; /* Font for readability */
        }
        .event-item {
            background-color: #444444; /* Slightly lighter background for event items */
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 8px;
        }
        .event-item h3 {
            color: #ffffff; /* Ensure event title is white */
        }
        .event-item p {
            color: #dddddd; /* Slightly off-white for descriptions */
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
    </style>
</head>

<body>
<div class="page-wrapper">

    <!-- Cursor -->
    <div class="cursor"></div>
    <div class="cursor-follower"></div>

    <!-- Main Header -->
    <header class="main-header header-style-two alternate">
        <div class="header-lower">
            <div class="auto-container">
                <div class="inner-container">
                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                        <div class="nav-outer d-flex align-items-center flex-wrap">
                            <div class="logo-box">
                                <div class="logo"><a href="index.php"><img src="assets/images/logo.svg" alt="logo" title="Logo"></a></div>
                            </div>

                            <!-- Main Menu -->
                            <nav class="main-menu navbar-expand-md">
                                <div class="navbar-header">
                                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent">
                                        <span class="icon-bar"></span>
                                        <span class="icon-bar"></span>
                                        <span class="icon-bar"></span>
                                    </button>
                                </div>
                                <div class="navbar-collapse collapse clearfix" id="navbarSupportedContent">
                                    <ul class="navigation clearfix">
                                        <li><a href="index.php">Home</a></li>
                                        <li><a href="about.php">About</a></li>
                                        <li><a href="contact.php">Contact</a></li>
                                    </ul>
                                </div>
                            </nav>
                        </div>

                        <div class="outer-box d-flex align-items-center flex-wrap">
                            <!-- Button Box -->
                            <div class="main-header_buttons">
                                <a href="login.php" class="btn-wrap">
                                    <span class="text-one">Login</span>
                                    <span class="text-two">Login</span>
                                </a>
                                <a href="signup.php" class="btn-wrap">
                                    <span class="text-one">Join now</span>
                                    <span class="text-two">Join now</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- End Main Header -->

    <!-- Page Title -->
    <section class="page-title">
        <div class="auto-container">
            <h2>Event List</h2>
        </div>
    </section>
    <!-- End Page Title -->

<!-- Event List -->
<section class="event-list">
    <div class="auto-container">
        <div class="row">
            <?php foreach ($events as $event): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="event-item">
                        <h3><?php echo htmlspecialchars($event['title_event']); ?></h3>
                        <a href="eventDetail.php?id=<?php echo $event['id_event']; ?>" class="btn btn-primary">View Details</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- End Event List -->

    <!-- Footer -->
    <footer>
        <div class="auto-container">
            <div class="row">
                <div class="col-lg-6">
                    <p>&copy; 2025 Your Company. All Rights Reserved.</p>
                </div>
                <div class="col-lg-6 text-right">
                    <ul class="social-links">
                        <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                        <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                        <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>
    <!-- End Footer -->

</div>

<!-- JavaScript Files -->
<script src="/projet%20web/View/Front_Office/assets/js/jquery.js"></script>
<script src="/projet%20web/View/Front_Office/assets/js/bootstrap.min.js"></script>
<script src="/projet%20web/View/Front_Office/assets/js/meanmenu.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.10.0/gsap.min.js"></script>
<script src="/projet%20web/View/Front_Office/assets/js/script.js"></script>

</body>
</html>
