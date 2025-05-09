<?php
require_once('../../Model/EventModel.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $eventModel = new EventModel();
    $event = $eventModel->getEventById($id);

    if ($event):
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Event Details</title>

    <!-- Stylesheets -->
    <link href="/projet%20web/View/Front_Office/assets/css/bootstrap.css" rel="stylesheet">
    <link href="/projet%20web/View/Front_Office/assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="/projet%20web/View/Front_Office/assets/css/meanmenu.min.css">
    <link href="/projet%20web/View/Front_Office/assets/css/responsive.css" rel="stylesheet">
    <!-- Font Awesome for the star icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        .favorite-btn {
            font-size: 24px;
            cursor: pointer;
            color: #ccc;
            transition: color 0.3s ease;
            background: none;
            border: none;
            padding: 10px;
        }
        
        .favorite-btn.active {
            color: #ffd700;
        }
        
        .favorite-btn:hover {
            transform: scale(1.1);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="card shadow-lg">
            <div class="card-header bg-primary text-white">
                <h2 class="mb-0"><?php echo htmlspecialchars($event['title_event']); ?></h2>
                <button class="favorite-btn" id="favoriteBtn" data-event-id="<?php echo $event['id_event']; ?>" 
                        data-event-title="<?php echo htmlspecialchars($event['title_event']); ?>"
                        data-event-date="<?php echo htmlspecialchars($event['date_event']); ?>"
                        data-event-location="<?php echo htmlspecialchars($event['loc_event']); ?>">
                    <i class="fas fa-star"></i>
                </button>
            </div>
            <div class="card-body">
                <p class="card-text"><strong>Description:</strong> <?php echo htmlspecialchars($event['desc_event']); ?></p>
                <p class="card-text"><strong>Date:</strong> <?php echo htmlspecialchars($event['date_event']); ?></p>
                <p class="card-text"><strong>Time:</strong> <?php echo htmlspecialchars($event['temp_event']); ?></p>
                <p class="card-text"><strong>Location:</strong> <?php echo htmlspecialchars($event['loc_event']); ?></p>
                <p class="card-text"><strong>Capacity:</strong> <?php echo htmlspecialchars($event['cap_event']); ?></p>
                <a href="eventList.php" class="btn btn-outline-secondary mt-3">Back to Events</a>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const favoriteBtn = document.getElementById('favoriteBtn');
        const eventId = favoriteBtn.dataset.eventId;
        
        // Function to get favorites from localStorage
        function getFavorites() {
            const favorites = localStorage.getItem('favoriteEvents');
            return favorites ? JSON.parse(favorites) : [];
        }
        
        // Function to save favorites to localStorage
        function saveFavorites(favorites) {
            localStorage.setItem('favoriteEvents', JSON.stringify(favorites));
        }
        
        // Check if this event is already a favorite
        function updateFavoriteButton() {
            const favorites = getFavorites();
            const isFavorite = favorites.some(fav => fav.id === eventId);
            favoriteBtn.classList.toggle('active', isFavorite);
        }
        
        // Initialize button state
        updateFavoriteButton();
        
        // Handle favorite button click
        favoriteBtn.addEventListener('click', function() {
            const favorites = getFavorites();
            const eventData = {
                id: eventId,
                title: this.dataset.eventTitle,
                date: this.dataset.eventDate,
                location: this.dataset.eventLocation
            };
            
            const existingIndex = favorites.findIndex(fav => fav.id === eventId);
            
            if (existingIndex === -1) {
                // Add to favorites
                favorites.push(eventData);
                this.classList.add('active');
            } else {
                // Remove from favorites
                favorites.splice(existingIndex, 1);
                this.classList.remove('active');
            }
            
            saveFavorites(favorites);
        });
    });
    </script>
</body>

</html>
<?php
    else:
        echo "<div style='padding: 2rem; color: red;'>Event not found.</div>";
    endif;
} else {
    echo "<div style='padding: 2rem; color: red;'>No event ID provided.</div>";
}
?>
