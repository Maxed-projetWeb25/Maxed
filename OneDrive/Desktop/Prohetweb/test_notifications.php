<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/Model/NotificationModel.php';
require_once __DIR__ . '/Model/EventModel.php';
require_once __DIR__ . '/Model/TicketModel.php';

// Create necessary directories
$logsDir = __DIR__ . '/logs';
if (!file_exists($logsDir)) {
    mkdir($logsDir, 0777, true);
}

// Test notification creation
function testNotificationSystem() {
    try {
        echo "Testing notification system...\n";

        // Initialize models
        $notificationModel = new NotificationModel();
        $eventModel = new EventModel();
        $ticketModel = new TicketModel();

        // Create a test event for today
        $event = new Event(
            null,
            "Test Event",
            "Test Description",
            date('H:i', strtotime('+1 hour')), // Event in 1 hour
            date('Y-m-d'),
            "Test Location",
            100
        );

        // Add the event
        if ($eventModel->createEvent($event)) {
            echo "Created test event successfully\n";
            
            // Get all events to find our test event
            $events = $eventModel->getAllEvents();
            $testEvent = end($events); // Get the last created event
            
            if ($testEvent) {
                $eventId = $testEvent->getIdEvent();
                echo "Test event ID: $eventId\n";
                
                // Create a test ticket
                $userId = 1; // Using the first user
                if ($ticketModel->createTicket($eventId, $userId, 'regular', 50.00, 'valid')) {
                    echo "Created test ticket successfully\n";
                } else {
                    echo "Failed to create test ticket\n";
                }
            } else {
                echo "Failed to get test event ID\n";
            }
        } else {
            echo "Failed to create test event\n";
        }

        // Check pending notifications
        $pendingNotifications = $notificationModel->getPendingNotifications();
        echo "Found " . count($pendingNotifications) . " pending notifications\n";

        // Run notification checker
        require_once __DIR__ . '/cron/check_notifications.php';
        $checker = new NotificationChecker();
        $checker->checkAndSendNotifications();

        echo "Notification test completed successfully!\n";
        echo "Please check the logs at " . __DIR__ . "/logs/notifications.log for details.\n";

    } catch (Exception $e) {
        echo "Error testing notification system: " . $e->getMessage() . "\n";
        error_log($e->getMessage());
    }
}

// Run the test
testNotificationSystem();
?> 