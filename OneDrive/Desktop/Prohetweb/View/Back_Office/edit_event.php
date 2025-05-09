<?php
require_once __DIR__.'/../../config/config.php';
require_once __DIR__ . '/../../Model/EventModel.php';
require_once __DIR__ . '/../../Model/EmailNotification.php';

$eventModel = new EventModel();
$emailNotification = new EmailNotification();
$message = '';
$messageType = '';

// Handle manual reminder
if (isset($_POST['send_reminder'])) {
    try {
        $emailNotification->sendManualReminder($_POST['id_event']);
        $message = "Reminder email sent successfully!";
        $messageType = "success";
    } catch (Exception $e) {
        $message = "Error sending reminder: " . $e->getMessage();
        $messageType = "danger";
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['send_reminder'])) {
    try {
        $eventId = $_POST['id_event'];
        
        // Prepare update data
        $updateData = [
            'title_event' => $_POST['title_event'],
            'desc_event' => $_POST['desc_event'],
            'temp_event' => $_POST['temp_event'],
            'date_event' => $_POST['date_event'],
            'loc_event' => $_POST['loc_event'],
            'cap_event' => (int)$_POST['cap_event'],
            'email' => $_POST['email']
        ];

        // Update the event
        if ($eventModel->updateEvent($eventId, $updateData)) {
            $message = "Event updated successfully!";
            $messageType = "success";
        }
    } catch (Exception $e) {
        $message = "Error updating event: " . $e->getMessage();
        $messageType = "danger";
    }
}

// Get event ID from URL
$eventId = isset($_GET['id']) ? $_GET['id'] : null;

if (!$eventId) {
    header('Location: event-datatable.php');
    exit;
}

// Get event details
$event = $eventModel->getEventById($eventId);

if (!$event) {
    header('Location: event-datatable.php');
    exit;
}

// Set page title
$pageTitle = 'Edit Event';

// Start output buffering
ob_start();
?>

<div class="card">
    <div class="card-body">
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Edit Event</h5>
            
            <!-- Manual Reminder Form -->
            <?php if (!empty($event['email'])): ?>
            <form method="POST" class="d-inline" onsubmit="return confirm('Send reminder email to <?php echo htmlspecialchars($event['email']); ?>?');">
                <input type="hidden" name="id_event" value="<?php echo htmlspecialchars($event['id_event']); ?>">
                <button type="submit" name="send_reminder" class="btn btn-info">
                    <i class="material-icons-outlined">email</i> Send Reminder
                </button>
            </form>
            <?php endif; ?>
        </div>

        <form method="POST" class="row g-3" id="editEventForm">
            <input type="hidden" name="id_event" value="<?php echo htmlspecialchars($event['id_event']); ?>">
            
            <div class="col-md-6">
                <label for="title_event" class="form-label">Event Title</label>
                <input type="text" class="form-control" id="title_event" name="title_event" 
                       value="<?php echo htmlspecialchars($event['title_event']); ?>" required>
            </div>

            <div class="col-md-6">
                <label for="email" class="form-label">Notification Email</label>
                <input type="email" class="form-control" id="email" name="email" 
                       value="<?php echo htmlspecialchars($event['email'] ?? ''); ?>" 
                       placeholder="Enter email for notifications">
            </div>

            <div class="col-md-12">
                <label for="desc_event" class="form-label">Description</label>
                <textarea class="form-control" id="desc_event" name="desc_event" rows="3" required><?php echo htmlspecialchars($event['desc_event']); ?></textarea>
            </div>

            <div class="col-md-6">
                <label for="temp_event" class="form-label">Time</label>
                <input type="time" class="form-control" id="temp_event" name="temp_event" 
                       value="<?php echo date('H:i', strtotime($event['temp_event'])); ?>" required>
            </div>

            <div class="col-md-6">
                <label for="date_event" class="form-label">Date</label>
                <input type="date" class="form-control" id="date_event" name="date_event" 
                       value="<?php echo $event['date_event']; ?>" required>
            </div>

            <div class="col-md-6">
                <label for="loc_event" class="form-label">Location</label>
                <input type="text" class="form-control" id="loc_event" name="loc_event" 
                       value="<?php echo htmlspecialchars($event['loc_event']); ?>" required>
            </div>

            <div class="col-md-6">
                <label for="cap_event" class="form-label">Capacity</label>
                <input type="number" class="form-control" id="cap_event" name="cap_event" min="1" 
                       value="<?php echo htmlspecialchars($event['cap_event']); ?>" required>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-primary">
                    <i class="material-icons-outlined">save</i> Update Event
                </button>
                <a href="event-datatable.php" class="btn btn-secondary">
                    <i class="material-icons-outlined">arrow_back</i> Back to List
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editEventForm');
    
    form.addEventListener('submit', function(e) {
        // Get form values
        const title = document.getElementById('title_event').value.trim();
        const desc = document.getElementById('desc_event').value.trim();
        const time = document.getElementById('temp_event').value;
        const date = document.getElementById('date_event').value;
        const location = document.getElementById('loc_event').value.trim();
        const capacity = parseInt(document.getElementById('cap_event').value);
        const email = document.getElementById('email').value.trim();

        // Basic validation
        if (!title || !desc || !time || !date || !location || isNaN(capacity)) {
            e.preventDefault();
            alert('Please fill in all required fields');
            return;
        }

        // Validate email format if provided
        if (email && !email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
            e.preventDefault();
            alert('Please enter a valid email address');
            return;
        }

        // Validate date is not in the past
        const selectedDate = new Date(date);
        selectedDate.setHours(0, 0, 0, 0);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (selectedDate < today) {
            e.preventDefault();
            alert('Please select a future date');
            return;
        }

        // Validate capacity
        if (capacity <= 0) {
            e.preventDefault();
            alert('Capacity must be greater than 0');
            return;
        }
    });
});
</script>

<?php
$content = ob_get_clean();
require_once 'event-template.php';
?>
