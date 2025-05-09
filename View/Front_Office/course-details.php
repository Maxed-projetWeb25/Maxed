<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/launcher/Controller/CoursController.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/launcher/Controller/ChapitreController.php';

$coursController = new CoursController();
$chapitreController = new ChapitreController();

// Get course ID from URL
$courseId = isset($_GET['id']) ? $_GET['id'] : null;

// Fetch course details
$course = $coursController->getCoursById($courseId);

// Fetch chapters for this course
$chapters = $chapitreController->getChapitresByCoursId($courseId);

if (!$course) {
    header('Location: course.php');
    exit;
}

// Function to convert URLs to clickable links
function makeLinksClickable($text) {
    return preg_replace_callback(
        '/(https?:\/\/[^\s]+)/',
        function ($matches) {
            $url = $matches[1];

            // YouTube
            if (preg_match('#(?:youtube\.com/watch\?v=|youtu\.be/)([^\s&]+)#', $url, $id)) {
                $videoId = htmlspecialchars($id[1]);
                return '<div class="video-responsive"><iframe src="https://www.youtube.com/embed/' . $videoId . '" frameborder="0" allowfullscreen></iframe></div>';
            }

            // Instagram
            if (strpos($url, 'instagram.com') !== false) {
                return '<blockquote class="instagram-media" data-instgrm-permalink="' . $url . '" data-instgrm-version="14"></blockquote>';
            }

            // Twitter
            if (strpos($url, 'twitter.com') !== false) {
                return '<blockquote class="twitter-tweet"><a href="' . $url . '"></a></blockquote>';
            }

            // Twitch
            if (preg_match('#twitch\.tv/([^/]+)(/v/(\d+)|/videos/(\d+))?#', $url, $matches)) {
                $channel = htmlspecialchars($matches[1]);
                if (!empty($matches[3]) || !empty($matches[4])) {
                    $videoId = !empty($matches[3]) ? $matches[3] : $matches[4];
                    return '<iframe src="https://player.twitch.tv/?video=v' . $videoId . '&parent=yourdomain.com" frameborder="0" allowfullscreen></iframe>';
                } else {
                    return '<iframe src="https://player.twitch.tv/?channel=' . $channel . '&parent=yourdomain.com" frameborder="0" allowfullscreen></iframe>';
                }
            }

            // Fallback: clickable link
            return '<a href="' . $url . '" target="_blank">' . $url . '</a>';
        },
        $text
    );
}



?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title><?= htmlspecialchars($course['titre']) ?> | Braine Digital</title>
<!-- Stylesheets -->
<link href="assets/css/bootstrap.css" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- Instagram -->
<script async src="//www.instagram.com/embed.js"></script>

<!-- Twitter -->
<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>


<style>
    /* Link Styling */
    .content-text a {
        color: #0066cc;
        text-decoration: none;
        word-break: break-all;
    }
    .content-text a:hover {
        text-decoration: underline;
    }
    
    /* Original Page Title Styling */
    .page-title {
        padding: 60px 0;
        position: relative;
        background: linear-gradient(135deg, #0066cc 0%, #004d99 100%);
    }
    .page-title h2 {
        color: #ffffff;
        position: relative;
    }
    .page-title-icon {
        position: absolute;
        left: 50px;
        top: 50px;
        width: 70px;
        height: 70px;
        background-size: contain;
        background-repeat: no-repeat;
    }
    .page-title-icon-two {
        position: absolute;
        right: 50px;
        bottom: 50px;
        width: 70px;
        height: 70px;
        background-size: contain;
        background-repeat: no-repeat;
    }
    .page-title-shadow {
        position: absolute;
        left: 0;
        bottom: 0;
        width: 100%;
        height: 100%;
        background-size: cover;
        background-repeat: no-repeat;
        opacity: 0.1;
    }
    .page-title-shadow_two {
        position: absolute;
        right: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-size: cover;
        background-repeat: no-repeat;
        opacity: 0.1;
    }
    .bread-crumb li {
        display: inline-block;
        color: rgba(255,255,255,0.7);
        font-size: 15px;
    }
    .bread-crumb li a {
        color: #ffffff;

    }
    .video-responsive {
    position: relative;
    padding-bottom: 56.25%;
    padding-top: 25px;
    height: 0;
}
.video-responsive iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

</style>

</head>

<body>
<!-- Cursor -->
<div class="cursor"></div>
<div class="cursor-follower"></div>
<!-- Cursor End -->
<script src="assets/js/gsap.min.js"></script>
<script src="assets/js/ScrollTrigger.min.js"></script>
<div class="page-wrapper">
    
    <!-- Header Content -->
    <header class="main-header">
        <!-- Your existing header content -->
    </header>
    
    <!-- Original Page Title Section -->
    <section class="page-title">
        <div class="page-title-icon" style="background-image:url(assets/images/icons/page-title_icon-1.png)"></div>
        <div class="page-title-icon-two" style="background-image:url(assets/images/icons/page-title_icon-2.png)"></div>
        <div class="page-title-shadow" style="background-image:url(assets/images/background/page-title-1.png)"></div>
        <div class="page-title-shadow_two" style="background-image:url(assets/images/background/page-title-2.png)"></div>
        <div class="auto-container">
            <h2><?= htmlspecialchars($course['titre']) ?></h2>
            <ul class="bread-crumb clearfix">
                <li><a href="index.html">Home</a></li>
                <li><a href="course.php">Courses</a></li>
                <li><?= htmlspecialchars($course['titre']) ?></li>
            </ul>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-4">
        <div class="auto-container">
            <div class="row">
                <!-- Chapters List -->
                <div class="col-lg-8 mx-auto">
                    <div class="chapter-list">
                        <div class="p-3 border-bottom">
                            <h4 class="mb-0"><i class="fas fa-list-ul me-2 text-primary"></i> Course Chapters</h4>
                        </div>
                        
                        <?php if (!empty($chapters)): ?>
                            <?php foreach ($chapters as $chapter): ?>
                            <div class="chapter-item">
                                <a href="#chapter-<?= $chapter['id_chapitre'] ?>" class="chapter-link" data-bs-toggle="collapse">
                                    <div class="chapter-icon">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                    <div class="chapter-title">
                                        <?= htmlspecialchars($chapter['titre']) ?>
                                    </div>
                                    <div class="chapter-meta">
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                </a>
                                <div id="chapter-<?= $chapter['id_chapitre'] ?>" class="collapse">
                                    <div class="p-3 pt-1">
                                        <div class="content-text">
                                            <?= nl2br(makeLinksClickable(htmlspecialchars($chapter['contenu']))) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="p-3 text-center text-muted">
                                <i class="fas fa-book-open fa-2x mb-2"></i>
                                <p>No chapters available yet</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
<br><br><br><br><br><br><br><br><br><br><br>
    <footer class="main-footer">
        <div class="footer_pattern" style="background-image: url(assets/images/background/footer-pattern.png)"></div>
        <div class="auto-container">
            <div class="inner-container">
                <!-- Widgets Section -->
                <div class="widgets-section">
                    <div class="row clearfix">
                        
                        <!-- Big Column -->
                        <div class="big-column col-lg-5 col-md-12 col-sm-12">
                            <div class="footer-newsletter">
                                <h5 class="footer-title">Newsletter</h5>
                                <div class="footer-newsletter_text">Lorem ipsum dolor sit amet consectetur adipiscing vitae mattis tellus. Nullam quis mattis.</div>
                                <div class="newsletter-box">
                                    <form method="post" action="contact.html">
                                        <div class="form-group">
                                            <span class="icon fa-regular fa-envelope fa-fw"></span>
                                            <input type="email" name="search-field" value="" placeholder="Enter your mail" required>
                                            <button type="submit" class="template-btn btn-style-one">
                                                <span class="btn-wrap">
                                                    <span class="text-one">Subscribe</span>
                                                    <span class="text-two">Subscribe</span>
                                                </span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Big Column -->
                        <div class="big-column col-lg-7 col-md-12 col-sm-12">
                            <div class="footer-lists_outer">
                                <div class="row clearfix">
                                    <!-- Column -->
                                    <div class="column col-lg-5 col-md-4 col-sm-6">
                                        <h5 class="footer-title">Services</h5>
                                        <ul class="footer-pages_list">
                                            <li><a href="#">AI-powered copywriting</a></li>
                                            <li><a href="#">Blog post generation</a></li>
                                            <li><a href="#">Social media content</a></li>
                                            <li><a href="#">Product descriptions</a></li>
                                            <li><a href="#">Email campaigns</a></li>
                                            <li><a href="#">Copy writings</a></li>
                                            <li><a href="#">SEO specialist</a></li>
                                        </ul>
                                    </div>
                                    <!-- Column -->
                                    <div class="column col-lg-3 col-md-4 col-sm-6">
                                        <h5 class="footer-title">resources</h5>
                                        <ul class="footer-pages_list">
                                            <li><a href="#">Blog</a></li>
                                            <li><a href="#">FAQs</a></li>
                                            <li><a href="#">Help center</a></li>
                                            <li><a href="#">case studies</a></li>
                                            <li><a href="#">whitepapers</a></li>
                                            <li><a href="#">Services</a></li>
                                        </ul>
                                    </div>
                                    <!-- Column -->
                                    <div class="column col-lg-4 col-md-4 col-sm-6">
                                        <h5 class="footer-title">about us</h5>
                                        <ul class="footer-pages_list">
                                            <li><a href="#">Our story</a></li>
                                            <li><a href="#">Team</a></li>
                                            <li><a href="#">Careers</a></li>
                                            <li><a href="#">Testimonials</a></li>
                                            <li><a href="#">Error 404</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="auto-container">
                <div class="inner-container d-flex justify-content-between align-items-center flex-wrap">
                    <div class="footer-logo"><a href="index.html"><img src="assets/images/logo.svg" alt="" title=""></a></div>
                    <div class="footer-copyright">&copy; 2024 <a href="index.html">Braine.</a> All rights reserved.</div>
                    <!-- Social Box -->
                    <div class="footer-social_box">
                        <a href="https://facebook.com/"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="https://twitter.com/"><i class="fa-brands fa-twitter"></i></a>
                        <a href="https://youtube.com/"><i class="fa-brands fa-youtube"></i></a>
                        <a href="https://instagram.com/"><i class="fa-brands fa-instagram"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

</div>

<!-- JavaScript -->
<script src="assets/js/jquery.js"></script>
<script src="assets/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    // Toggle chapter content
    $('.chapter-link').click(function(e) {
        if ($(this).attr('href').startsWith('#')) {
            e.preventDefault();
            const target = $(this).attr('href');
            $(target).collapse('toggle');
            $(this).find('.fa-chevron-down').toggleClass('fa-rotate-180');
        }
    });
    
    // Open first chapter by default
    $('.chapter-item:first-child .chapter-link').trigger('click');
});
</script>

</body>
<script>
// Cursor Animation
if ($('body').length) {
    const cursor = document.querySelector('.cursor');
    const cursorFollower = document.querySelector('.cursor-follower');
    const links = document.querySelectorAll('a, button, .chapter-link, input[type="submit"], input[type="button"]');

    let posX = 0,
        posY = 0,
        mouseX = 0,
        mouseY = 0;

    if (cursor && cursorFollower) {
        gsap.to({}, {
            repeat: -1,
            duration: 0.016,
            onRepeat: function() {
                posX += (mouseX - posX) / 9;
                posY += (mouseY - posY) / 9;

                gsap.set(cursor, {
                    css: {
                        left: mouseX,
                        top: mouseY
                    }
                });

                gsap.set(cursorFollower, {
                    css: {
                        left: posX - 12,
                        top: posY - 12
                    }
                });
            }
        });

        document.addEventListener('mousemove', function(e) {
            mouseX = e.clientX;
            mouseY = e.clientY;
        });

        links.forEach(link => {
            link.addEventListener('mouseenter', () => {
                cursor.classList.add('cursor-active');
                cursorFollower.classList.add('cursor-follower-active');
            });

            link.addEventListener('mouseleave', () => {
                cursor.classList.remove('cursor-active');
                cursorFollower.classList.remove('cursor-follower-active');
            });
        });
    }
}
</script>
</html>