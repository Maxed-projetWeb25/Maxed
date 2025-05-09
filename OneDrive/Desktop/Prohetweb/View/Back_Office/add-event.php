<?php
require_once __DIR__.'/../../config/config.php';
require_once __DIR__ . '/../../Model/EventModel.php';

$eventModel = new EventModel();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $event = new Event(
            null,
            $_POST['title_event'],
            $_POST['desc_event'],
            $_POST['temp_event'],
            $_POST['date_event'],
            $_POST['loc_event'],
            (int)$_POST['cap_event'],
            $_POST['email']
        );

        if ($eventModel->createEvent($event)) {
            header('Location: event-datatable.php');
            exit;
        }
    } catch (Exception $e) {
        $error = "Error creating event: " . $e->getMessage();
    }
}

// Set page title
$pageTitle = 'Add Event';

// Start output buffering
ob_start();
?>

<div class="card">
    <div class="card-body">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <div class="card-title d-flex align-items-center">
            <div><i class="material-icons-outlined me-1">add_circle</i></div>
            <h5 class="mb-0">Add New Event</h5>
        </div>
        <hr>
        <form method="POST" class="row g-3">
            <div class="col-md-6">
                <label for="title_event" class="form-label">Event Title</label>
                <input type="text" class="form-control" id="title_event" name="title_event" required>
            </div>
            <div class="col-md-6">
                <label for="email" class="form-label">Notification Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter email for notifications">
            </div>
            <div class="col-md-12">
                <label for="desc_event" class="form-label">Description</label>
                <textarea class="form-control" id="desc_event" name="desc_event" rows="3" required></textarea>
            </div>
            <div class="col-md-6">
                <label for="temp_event" class="form-label">Time</label>
                <input type="time" class="form-control" id="temp_event" name="temp_event" required>
            </div>
            <div class="col-md-6">
                <label for="date_event" class="form-label">Date</label>
                <input type="date" class="form-control" id="date_event" name="date_event" required>
            </div>
            <div class="col-md-6">
                <label for="loc_event" class="form-label">Location</label>
                <input type="text" class="form-control" id="loc_event" name="loc_event" required>
            </div>
            <div class="col-md-6">
                <label for="cap_event" class="form-label">Capacity</label>
                <input type="number" class="form-control" id="cap_event" name="cap_event" min="1" required>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="material-icons-outlined">save</i> Save Event
                </button>
                <a href="event-datatable.php" class="btn btn-secondary px-4">
                    <i class="material-icons-outlined">arrow_back</i> Back to List
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Basic validation
        const title = document.getElementById('title_event').value.trim();
        const desc = document.getElementById('desc_event').value.trim();
        const time = document.getElementById('temp_event').value;
        const date = document.getElementById('date_event').value;
        const location = document.getElementById('loc_event').value.trim();
        const capacity = parseInt(document.getElementById('cap_event').value);
        const email = document.getElementById('email').value.trim();

        if (!title || !desc || !time || !date || !location || !capacity) {
            alert('Please fill in all required fields');
            return;
        }

        // Validate email format if provided
        if (email && !email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
            alert('Please enter a valid email address');
            return;
        }

        // Validate date is not in the past
        const selectedDate = new Date(date);
        selectedDate.setHours(0, 0, 0, 0);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (selectedDate < today) {
            alert('Please select a future date');
            return;
        }

        // Validate capacity is positive
        if (capacity <= 0) {
            alert('Capacity must be greater than 0');
            return;
        }

        // If validation passes, submit the form
        this.submit();
    });
});
</script>

<?php
$content = ob_get_clean();
require_once 'event-template.php';
?> 