<?php
require_once __DIR__ . '/Model/TicketModel.php';

$ticketModel = new TicketModel();
$tickets = $ticketModel->getAllTickets();

foreach ($tickets as $ticket) {
    $ticketModel->updateTicket(
        $ticket['id'],
        $ticket['event_id'],
        $ticket['user_id'],
        $ticket['type'],
        $ticket['price'],
        $ticket['status']
    );
}

echo "QR codes regenerated successfully!";