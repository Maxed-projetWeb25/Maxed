<?php
require_once __DIR__.'/../../config/config.php';
require_once __DIR__.'/../../Model/TicketModel.php';
require_once __DIR__.'/../../Model/EventModel.php';
require_once __DIR__.'/../../Model/UserModel.php';

$ticketModel = new TicketModel();
$eventModel = new EventModel();
$userModel = new UserModel();

// Get ticket ID from URL
$ticketId = isset($_GET['id']) ? $_GET['id'] : null;

if (!$ticketId) {
    header('Location: event-datatable.php');
    exit;
}

// Get ticket data
$ticket = $ticketModel->getTicketById($ticketId);

if (!$ticket) {
    header('Location: event-datatable.php');
    exit;
}

// Get all events and users for dropdowns
$events = $eventModel->getAllEvents();
$users = $userModel->getAllUsers();

// Set page title
$pageTitle = 'Edit Ticket';

// Start output buffering
ob_start();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Ticket</h3>
                </div>
                <div class="card-body">
                    <form action="../../Controller/TicketController.php" method="POST" id="editTicketForm">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?php echo $ticket['id']; ?>">
                        
                        <div class="form-group">
                            <label for="event_id">Event</label>
                            <select class="form-control" id="event_id" name="event_id" required>
                                <option value="">Select Event</option>
                                <?php foreach ($events as $event): ?>
                                    <option value="<?php echo $event->getIdEvent(); ?>" 
                                            <?php echo ($event->getIdEvent() == $ticket['event_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($event->getTitleEvent()); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="user_id">User</label>
                            <select class="form-control" id="user_id" name="user_id" required>
                                <option value="">Select User</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?php echo $user['id']; ?>"
                                            <?php echo ($user['id'] == $ticket['user_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($user['username']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="type">Ticket Type</label>
                            <select class="form-control" id="type" name="type" required>
                                <option value="">Select Type</option>
                                <option value="VIP" <?php echo ($ticket['type'] == 'VIP') ? 'selected' : ''; ?>>VIP</option>
                                <option value="Regular" <?php echo ($ticket['type'] == 'Regular') ? 'selected' : ''; ?>>Regular</option>
                                <option value="Student" <?php echo ($ticket['type'] == 'Student') ? 'selected' : ''; ?>>Student</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="price">Price</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">$</span>
                                </div>
                                <input type="number" class="form-control" id="price" name="price" 
                                       min="0" step="0.01" required
                                       value="<?php echo htmlspecialchars($ticket['price']); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="">Select Status</option>
                                <option value="Available" <?php echo ($ticket['status'] == 'Available') ? 'selected' : ''; ?>>Available</option>
                                <option value="Reserved" <?php echo ($ticket['status'] == 'Reserved') ? 'selected' : ''; ?>>Reserved</option>
                                <option value="Sold" <?php echo ($ticket['status'] == 'Sold') ? 'selected' : ''; ?>>Sold</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="material-icons-outlined">save</i> Update Ticket
                            </button>
                            <a href="event-datatable.php" class="btn btn-secondary">
                                <i class="material-icons-outlined">arrow_back</i> Back to List
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('editTicketForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Get form values
    const eventId = document.getElementById('event_id').value;
    const userId = document.getElementById('user_id').value;
    const type = document.getElementById('type').value;
    const price = document.getElementById('price').value;
    const status = document.getElementById('status').value;
    
    // Validate form
    if (!eventId || !userId || !type || !price || !status) {
        alert('Please fill in all required fields');
        return;
    }
    
    if (price <= 0) {
        alert('Price must be greater than 0');
        return;
    }
    
    // If validation passes, submit the form
    this.submit();
});
</script>

<?php
$content = ob_get_clean();
require_once 'event-template.php';
?> 