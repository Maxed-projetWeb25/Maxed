<?php
require_once __DIR__ . '/../../Model/TicketModel.php';

$ticketModel = new TicketModel();
$tickets = $ticketModel->getAllTickets();

$regenerated = 0;
$errors = 0;

foreach ($tickets as $ticket) {
    try {
        $success = $ticketModel->updateTicket(
            $ticket['id'],
            $ticket['event_id'],
            $ticket['user_id'],
            $ticket['type'],
            $ticket['price'],
            $ticket['status']
        );
        
        if ($success) {
            $regenerated++;
        } else {
            $errors++;
        }
    } catch (Exception $e) {
        error_log("Error regenerating QR code for ticket {$ticket['id']}: " . $e->getMessage());
        $errors++;
    }
}

echo json_encode([
    'success' => true,
    'message' => "QR codes regeneration complete. Successfully regenerated: $regenerated, Errors: $errors",
    'regenerated' => $regenerated,
    'errors' => $errors
]); 