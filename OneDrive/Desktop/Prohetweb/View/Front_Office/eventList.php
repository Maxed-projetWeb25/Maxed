<?php
// Include config file for database connection
require_once 'C:/xampp/htdocs/projet web/config/config.php';
require_once 'C:/xampp/htdocs/projet web/Controller/EventReactionController.php';

// Get the database connection
$db = Config::getInstance()->getConnection();
$reactionController = new EventReactionController();

// Fetch events from the database
$query = $db->query("SELECT * FROM event");
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
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

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
            transition: transform 0.3s ease;
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
        .reaction-button {
            padding: 8px 15px;
            margin: 5px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            opacity: 0.7;
        }

        .like-button {
            background-color: #28a745;
            color: white;
        }

        .dislike-button {
            background-color: #dc3545;
            color: white;
        }

        .reaction-button:hover {
            opacity: 0.9;
            transform: scale(1.05);
        }

        .reaction-button.active {
            opacity: 1;
            transform: scale(1.1);
            font-weight: bold;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .reaction-count {
            font-weight: bold;
            margin-left: 5px;
        }

        .favorite-event {
            border: 2px solid #ffd700;
            background-color: #4a4a4a;
            position: relative;
        }

        .favorite-star {
            position: absolute;
            top: 10px;
            right: 10px;
            color: #ffd700;
            font-size: 20px;
        }

        .favorite-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #ffd700;
        }

        .section-title {
            color: #ffffff;
            margin-bottom: 20px;
            padding-left: 15px;
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
        <!-- Favorites Section -->
        <div id="favoritesSection" class="favorite-section" style="display: none;">
            <h3 class="section-title">⭐ Favorite Events</h3>
            <div class="row" id="favoriteEvents">
                <!-- Favorite events will be inserted here -->
            </div>
        </div>

        <!-- All Events -->
        <h3 class="section-title">All Events</h3>
        <div class="row" id="allEvents">
            <?php foreach ($events as $event): 
                $reactionData = $reactionController->getReactionCounts($event['id_event']);
                $counts = $reactionData['counts'];
                $currentReaction = $reactionData['currentReaction'];
            ?>
                <div class="col-lg-4 col-md-6 event-card" data-event-id="<?php echo $event['id_event']; ?>">
                    <div class="event-item">
                        <h3><?php echo htmlspecialchars($event['title_event']); ?></h3>
                        <p><strong>Date:</strong> <?php echo htmlspecialchars($event['date_event']); ?></p>
                        <p><strong>Location:</strong> <?php echo htmlspecialchars($event['loc_event']); ?></p>
                        <div class="button-group">
                            <a href="eventDetail.php?id=<?php echo $event['id_event']; ?>" class="btn btn-primary">View Details</a>
                            <a href="addTicketF.php?event_id=<?php echo $event['id_event']; ?>" class="btn btn-success">Buy Ticket</a>
                        </div>
                        <div class="reaction-group mt-3">
                            <button class="reaction-button like-button <?php echo $currentReaction === 'like' ? 'active' : ''; ?>" 
                                    data-event-id="<?php echo $event['id_event']; ?>" 
                                    data-type="like">
                                👍 <span class="reaction-count like-count"><?php echo $counts['likes']; ?></span>
                            </button>
                            <button class="reaction-button dislike-button <?php echo $currentReaction === 'dislike' ? 'active' : ''; ?>" 
                                    data-event-id="<?php echo $event['id_event']; ?>" 
                                    data-type="dislike">
                                👎 <span class="reaction-count dislike-count"><?php echo $counts['dislikes']; ?></span>
                            </button>
                        </div>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const reactionButtons = document.querySelectorAll('.reaction-button');
    
    reactionButtons.forEach(button => {
        button.addEventListener('click', async function() {
            const eventId = this.dataset.eventId;
            const reactionType = this.dataset.type;
            const eventItem = this.closest('.event-item');
            
            try {
                const response = await fetch('/projet web/Controller/handle_reaction.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        eventId: eventId,
                        reactionType: reactionType
                    })
                });

                const data = await response.json();
                
                if (data.status === 'error') {
                    console.error('Error:', data.message);
                    return;
                }

                // Update the counts
                eventItem.querySelector('.like-count').textContent = data.counts.likes;
                eventItem.querySelector('.dislike-count').textContent = data.counts.dislikes;

                // Remove active class from all buttons in this event item
                eventItem.querySelectorAll('.reaction-button').forEach(btn => {
                    btn.classList.remove('active');
                });

                // Add active class to the clicked button if it's the current reaction
                if (data.currentReaction === reactionType) {
                    this.classList.add('active');
                }

            } catch (error) {
                console.error('Error:', error);
            }
        });
    });

    // Function to get favorites from localStorage
    function getFavorites() {
        const favorites = localStorage.getItem('favoriteEvents');
        return favorites ? JSON.parse(favorites) : [];
    }

    // Function to create event card HTML
    function createEventCard(event, isFavorite = false) {
        const eventCard = document.querySelector(`.event-card[data-event-id="${event.id}"]`);
        if (eventCard) {
            const clone = eventCard.cloneNode(true);
            const eventItem = clone.querySelector('.event-item');
            
            if (isFavorite) {
                eventItem.classList.add('favorite-event');
                const starIcon = document.createElement('i');
                starIcon.className = 'fas fa-star favorite-star';
                eventItem.appendChild(starIcon);
            }
            
            return clone;
        }
        return null;
    }

    // Function to update the favorites section
    function updateFavoritesList() {
        const favorites = getFavorites();
        const favoritesSection = document.getElementById('favoritesSection');
        const favoriteEventsContainer = document.getElementById('favoriteEvents');
        
        // Clear current favorites
        favoriteEventsContainer.innerHTML = '';
        
        if (favorites.length > 0) {
            favoritesSection.style.display = 'block';
            
            favorites.forEach(favorite => {
                const eventCard = createEventCard(favorite, true);
                if (eventCard) {
                    favoriteEventsContainer.appendChild(eventCard);
                }
            });
        } else {
            favoritesSection.style.display = 'none';
        }
    }

    // Initial update of favorites
    updateFavoritesList();

    // Listen for changes in localStorage
    window.addEventListener('storage', function(e) {
        if (e.key === 'favoriteEvents') {
            updateFavoritesList();
        }
    });
});
</script>

</body>
</html>
