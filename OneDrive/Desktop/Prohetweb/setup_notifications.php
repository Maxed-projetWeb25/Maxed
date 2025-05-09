<?php
// Create logs directory if it doesn't exist
$logsDir = __DIR__ . '/logs';
if (!file_exists($logsDir)) {
    mkdir($logsDir, 0777, true);
}

// Create cron directory if it doesn't exist
$cronDir = __DIR__ . '/cron';
if (!file_exists($cronDir)) {
    mkdir($cronDir, 0777, true);
}

// Initialize notification system
require_once __DIR__ . '/Model/NotificationModel.php';
$notificationModel = new NotificationModel();

// Create notifications for existing tickets
require_once __DIR__ . '/Model/TicketModel.php';
$ticketModel = new TicketModel();
$tickets = $ticketModel->getAllTickets();

foreach ($tickets as $ticket) {
    $notificationModel->createEventNotification($ticket['event_id'], $ticket['user_id']);
}

echo "Notification system setup completed!\n";
echo "Please ensure you have set up the following cron job:\n\n";
echo "*/5 * * * * php " . __DIR__ . "/cron/check_notifications.php >> " . __DIR__ . "/logs/notifications.log 2>&1\n";
?> 