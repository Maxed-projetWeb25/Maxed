<?php
require_once __DIR__.'/../../config/config.php';
require_once __DIR__.'/../../Model/TicketModel.php';

$ticketModel = new TicketModel();
$pageTitle = 'Delete Ticket';

// Get ticket ID from URL
$ticketId = isset($_GET['id']) ? $_GET['id'] : null;

if (!$ticketId) {
    header('Location: event-datatable.php');
    exit;
}

// Get ticket details
$ticket = $ticketModel->getTicketById($ticketId);

if (!$ticket) {
    header('Location: event-datatable.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($ticketModel->deleteTicket($ticketId)) {
        header('Location: event-datatable.php');
        exit;
    } else {
        $error = "Failed to delete ticket. Please try again.";
    }
}

// Start output buffering
ob_start();
?>

<div class="card">
  <div class="card-body">
    <div class="card-title d-flex align-items-center">
      <div><i class="material-icons-outlined me-1 text-danger">delete</i></div>
      <h5 class="mb-0">Delete Ticket</h5>
    </div>
    <hr>
    
    <?php if (isset($error)): ?>
    <div class="alert alert-danger" role="alert">
      <?php echo $error; ?>
    </div>
    <?php endif; ?>

    <div class="alert alert-warning" role="alert">
      <h4 class="alert-heading">Warning!</h4>
      <p>Are you sure you want to delete this ticket? This action cannot be undone.</p>
    </div>

    <div class="row mb-4">
      <div class="col-md-6">
        <h6>Ticket Details:</h6>
        <ul class="list-unstyled">
          <li><strong>Ticket ID:</strong> <?php echo $ticket['id_ticket']; ?></li>
          <li><strong>Type:</strong> <?php echo $ticket['type_ticket']; ?></li>
          <li><strong>Price:</strong> $<?php echo $ticket['price_ticket']; ?></li>
          <li><strong>Status:</strong> <?php echo $ticket['status_ticket']; ?></li>
        </ul>
      </div>
    </div>

    <form method="POST" action="">
      <div class="row">
        <div class="col">
          <button type="submit" class="btn btn-danger px-4">Delete Ticket</button>
          <a href="event-datatable.php" class="btn btn-secondary px-4">Cancel</a>
        </div>
      </div>
    </form>
  </div>
</div>

<?php
$content = ob_get_clean();
require_once 'ticket-template.php';
?> 