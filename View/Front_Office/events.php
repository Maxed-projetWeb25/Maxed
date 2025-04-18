<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Braine Digital Agency | About Us</title>
    
    <!-- Stylesheets -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/meanmenu.min.css" rel="stylesheet">
    <link href="assets/css/responsive.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,500;0,600;0,700;1,600;1,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.png" type="image/x-icon">
    <link rel="icon" href="assets/images/favicon.png" type="image/x-icon">
    
    <!-- Responsive -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">

    <!-- Adding GSAP (TweenMax) for animations -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/gsap.min.js"></script>

</head>

<body>
<div class="page-wrapper">

    <!-- Cursor -->
    <div class="cursor"></div>
    <div class="cursor-follower"></div>

    <!-- Preloader -->
    <div class="loader-wrap">
        <div class="preloader">
            <div class="preloader-close">x</div>
            <div id="handle-preloader" class="handle-preloader">
                <div class="animation-preloader">
                    <div class="txt-loading">
                        <?php
                        $loadingText = str_split("LOADING");
                        foreach ($loadingText as $char) {
                            echo "<span data-text-preloader=\"$char\" class=\"letters-loading\">$char</span>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Preloader End -->

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
                                        <li class="dropdown"><a href="#">Options</a>
                                            <ul>
                                                <li><a href="faq.php">FAQ</a></li>
                                                <li><a href="pricing.php">Price</a></li>
                                                <li><a href="testimonial.php">Testimonial</a></li>
                                                <li><a href="reset.php">Forgot password</a></li>
                                                <li class="dropdown"><a href="#">Team</a>
                                                    <ul>
                                                        <li><a href="team-detail.php">Team Detail</a></li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </li>
                                        <li class="dropdown"><a href="#">Services</a>
                                            <ul>
                                                <li><a href="#">Events</a></li>
                                                <li><a href="course.php">Courses</a></li>
                                                <li><a href="feed.php">Feed</a></li>
                                                <li><a href="quiz.php">Quiz</a></li>
                                                <li><a href="index-3.php">Services Detail</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="contact.php">Contact</a></li>
                                    </ul>
                                </div>
                            </nav>
                        </div>

                        <div class="outer-box d-flex align-items-center flex-wrap">
                            <!-- Language Dropdown -->
                            <div class="language-dropdown">
                                <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown">
                                    <span class="flag"><img src="assets/images/icons/flag.png" alt="" /></span>
                                    <span class="fa-solid fa-angle-down fa-fw"></span>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                    <li><a class="dropdown-item" href="#"><span class="flag"><img src="assets/images/icons/flag.png" alt="" /></span> English</a></li>
                                    <li><a class="dropdown-item" href="#"><span class="flag"><img src="assets/images/icons/arabic.png" alt="" /></span> Arabic</a></li>
                                    <li><a class="dropdown-item" href="#"><span class="flag"><img src="assets/images/icons/germany.png" alt="" /></span> German</a></li>
                                    <li><a class="dropdown-item" href="#"><span class="flag"><img src="assets/images/icons/france.png" alt="" /></span> French</a></li>
                                </ul>
                            </div>

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

                            <!-- Mobile Navigation Toggler -->
                            <div class="mobile-nav-toggler">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-menu-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M4 6l16 0" />
                                    <path d="M4 12l16 0" />
                                    <path d="M4 18l16 0" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div class="mobile-menu">
            <div class="menu-backdrop"></div>
            <div class="close-btn"><span class="icon fa-solid fa-xmark fa-fw"></span></div>
            <nav class="menu-box">
                <div class="nav-logo"><a href="index.php"><img src="assets/images/mobile-logo.svg" alt=""></a></div>
                <div class="menu-outer"></div>
            </nav>
        </div>
    </header>
    <!-- End Main Header -->

    <!-- Page Title -->
    <section class="page-title">
        <div class="page-title-icon" style="background-image:url(assets/images/icons/page-title_icon-1.png)"></div>
        <div class="page-title-icon-two" style="background-image:url(assets/images/icons/page-title_icon-2.png)"></div>
        <div class="page-title-shadow" style="background-image:url(assets/images/background/page-title-1.png)"></div>
        <div class="page-title-shadow_two" style="background-image:url(assets/images/background/page-title-2.png)"></div>
        <div class="auto-container">
            <h2>About us</h2>
            <ul class="bread-crumb clearfix">
                <li><a href="index.php">Home</a></li>
                <li>About us</li>
            </ul>
        </div>
    </section>
    <!-- End Page Title -->

</div>

<!-- JavaScript Libraries -->
<script src="assets/js/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.meanmenu/2.0.11/jquery.meanmenu.min.js"></script>
<script src="assets/js/script.js"></script>

</body>
</html>
