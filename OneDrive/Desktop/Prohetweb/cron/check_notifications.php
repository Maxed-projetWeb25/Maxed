<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../Model/NotificationModel.php';

class NotificationChecker {
    private $notificationModel;

    public function __construct() {
        $this->notificationModel = new NotificationModel();
    }

    public function checkAndSendNotifications() {
        // Get all pending notifications that are due
        $pendingNotifications = $this->notificationModel->getPendingNotifications();

        foreach ($pendingNotifications as $notification) {
            // Send notification (you can implement different notification methods here)
            $this->sendNotification($notification);
            
            // Mark notification as sent
            $this->notificationModel->markNotificationAsSent($notification['id']);
        }
    }

    private function sendNotification($notification) {
        // Format the notification message
        $message = sprintf(
            "Reminder: Your event '%s' starts in 30 minutes at %s on %s. Location: %s",
            $notification['title_event'],
            $notification['temp_event'],
            date('F j, Y', strtotime($notification['date_event'])),
            $notification['loc_event']
        );

        // Send email notification
        $to = $notification['email'];
        $subject = "Event Reminder: " . $notification['title_event'];
        $headers = "From: noreply@yourdomain.com\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        // Log the notification
        error_log("Sending notification to {$notification['email']} for event {$notification['title_event']}");
        
        // Send the email
        mail($to, $subject, $message, $headers);
    }
}

// Run the notification checker
$checker = new NotificationChecker();
$checker->checkAndSendNotifications();
?> 