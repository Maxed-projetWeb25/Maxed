<?php
// delete.php
require_once __DIR__ . '/../../Model/TicketModel.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Create TicketModel instance
    $ticketModel = new TicketModel();
    
    // Call the delete function from TicketModel
    $result = $ticketModel->deleteTicket($id);
    
    if ($result) {
        // Redirect to event datatable after successful deletion
        header('Location: event-datatable.php');
        exit;
    } else {
        echo "Error: Could not delete the ticket.";
    }
} else {
    echo "Ticket ID not provided.";
}
?>
