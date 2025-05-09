<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class NotificationModel {
    private $db;
    private $mailer;

    public function __construct() {
        $this->db = Config::getInstance()->getConnection();
        $this->ensureNotificationTableExists();
        try {
            $this->initializeMailer();
        } catch (Exception $e) {
            error_log("Failed to initialize mailer: " . $e->getMessage());
        }
    }

    private function initializeMailer() {
        try {
            $this->mailer = new PHPMailer(true);
            
            // Server settings with error logging
            $this->mailer->SMTPDebug = 0; // 0 = off, 1 = client messages, 2 = client and server messages
            $this->mailer->isSMTP();
            $this->mailer->Host = 'smtp.gmail.com';
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = 'yassinearfaoui689@gmail.com';
            $this->mailer->Password = 'eajr uugv arlx dknm'; // App password
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port = 587;
            $this->mailer->CharSet = 'UTF-8';
            
            // Default sender
            $this->mailer->setFrom('yassinearfaoui689@gmail.com', 'Event Team');
            
            // Enable exceptions
            $this->mailer->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
        } catch (Exception $e) {
            error_log("Mailer initialization error: " . $e->getMessage());
            throw $e;
        }
    }

    private function ensureNotificationTableExists() {
        try {
            // Check if table exists
            $query = "SHOW TABLES LIKE 'notification'";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            if ($stmt->rowCount() == 0) {
                // Create notification table if it doesn't exist
                $createTableQuery = "CREATE TABLE IF NOT EXISTS `notification` (
                    `id` INT PRIMARY KEY AUTO_INCREMENT,
                    `user_id` INT NOT NULL,
                    `event_id` INT NOT NULL,
                    `notification_time` DATETIME NOT NULL,
                    `status` ENUM('pending', 'sent', 'read') NOT NULL DEFAULT 'pending',
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE CASCADE,
                    FOREIGN KEY (`event_id`) REFERENCES `event`(`id_event`) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
                
                $this->db->exec($createTableQuery);
            }
        } catch (PDOException $e) {
            error_log("Error ensuring notification table exists: " . $e->getMessage());
        }
    }

    public function createNotification($user_id, $event_id, $notification_time) {
        try {
            $query = "INSERT INTO notification (user_id, event_id, notification_time, status) 
                     VALUES (:user_id, :event_id, :notification_time, 'pending')";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':event_id', $event_id);
            $stmt->bindParam(':notification_time', $notification_time);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error creating notification: " . $e->getMessage());
            return false;
        }
    }

    public function getPendingNotifications() {
        try {
            $query = "SELECT n.*, e.title_event, e.date_event, e.temp_event, u.email 
                     FROM notification n 
                     JOIN event e ON n.event_id = e.id_event 
                     JOIN user u ON n.user_id = u.id 
                     WHERE n.status = 'pending' 
                     AND n.notification_time <= NOW()";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting pending notifications: " . $e->getMessage());
            return [];
        }
    }

    public function markNotificationAsSent($notification_id) {
        try {
            $query = "UPDATE notification SET status = 'sent' WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $notification_id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error marking notification as sent: " . $e->getMessage());
            return false;
        }
    }

    public function markNotificationAsRead($notification_id) {
        try {
            $query = "UPDATE notification SET status = 'read' WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $notification_id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error marking notification as read: " . $e->getMessage());
            return false;
        }
    }

    public function getUserNotifications($user_id) {
        try {
            $query = "SELECT n.*, e.title_event, e.date_event, e.temp_event 
                     FROM notification n 
                     JOIN event e ON n.event_id = e.id_event 
                     WHERE n.user_id = :user_id 
                     ORDER BY n.notification_time DESC";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting user notifications: " . $e->getMessage());
            return [];
        }
    }

    public function createEventNotification($event_id, $user_id) {
        try {
            // Get event details
            $query = "SELECT date_event, temp_event, loc_event FROM event WHERE id_event = :event_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':event_id', $event_id);
            $stmt->execute();
            $event = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($event) {
                // Create DateTime object from event date and time
                $eventDateTime = DateTime::createFromFormat(
                    'Y-m-d H:i',
                    $event['date_event'] . ' ' . $event['temp_event']
                );
                
                if ($eventDateTime === false) {
                    throw new Exception("Invalid date/time format: {$event['date_event']} {$event['temp_event']}");
                }
                
                // Calculate notification time (30 minutes before event)
                $notificationTime = clone $eventDateTime;
                $notificationTime->modify('-30 minutes');

                // Create notification
                return $this->createNotification(
                    $user_id,
                    $event_id,
                    $notificationTime->format('Y-m-d H:i:s')
                );
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error creating event notification: " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            error_log("Error creating event notification: " . $e->getMessage());
            return false;
        }
    }

    public function sendEventReminders($event_id) {
        try {
            // Get all tickets with user emails for this event
            $query = "SELECT t.*, u.email, e.title_event, e.date_event, e.temp_event, e.loc_event 
                     FROM ticket t 
                     JOIN user u ON t.user_id = u.id 
                     JOIN event e ON t.event_id = e.id_event 
                     WHERE t.event_id = :event_id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':event_id', $event_id);
            $stmt->execute();
            $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($tickets)) {
                error_log("No tickets found for event ID: " . $event_id);
                return ['success' => false, 'message' => 'No tickets found for this event'];
            }

            $successCount = 0;
            $errors = [];

            foreach ($tickets as $ticket) {
                try {
                    if (empty($ticket['email'])) {
                        $errors[] = "Missing email for ticket ID: " . $ticket['id'];
                        continue;
                    }

                    // Reset mailer for each email
                    $this->mailer->clearAddresses();
                    $this->mailer->clearAttachments();

                    // Set recipient
                    $this->mailer->addAddress($ticket['email']);

                    // Set email content
                    $this->mailer->isHTML(true);
                    $this->mailer->Subject = 'Event Reminder: ' . $ticket['title_event'];
                    
                    // Create email body
                    $body = $this->createEmailBody($ticket);
                    $this->mailer->Body = $body;
                    $this->mailer->AltBody = strip_tags($body);

                    // Send email
                    if ($this->mailer->send()) {
                        $successCount++;
                    } else {
                        $errors[] = "Failed to send email to {$ticket['email']}: " . $this->mailer->ErrorInfo;
                    }
                } catch (Exception $e) {
                    error_log("Error sending email to {$ticket['email']}: " . $e->getMessage());
                    $errors[] = "Error sending to {$ticket['email']}: " . $e->getMessage();
                }
            }

            $result = [
                'success' => ($successCount > 0),
                'message' => $successCount > 0 
                    ? "Successfully sent {$successCount} reminder(s)" 
                    : "Failed to send reminders",
                'total' => count($tickets),
                'sent' => $successCount,
                'errors' => $errors
            ];

            error_log("Send reminders result: " . print_r($result, true));
            return $result;

        } catch (PDOException $e) {
            error_log("Database error in sendEventReminders: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Database error occurred while sending reminders',
                'error' => $e->getMessage()
            ];
        } catch (Exception $e) {
            error_log("General error in sendEventReminders: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while sending reminders',
                'error' => $e->getMessage()
            ];
        }
    }

    private function createEmailBody($ticket) {
        $eventDate = date('F j, Y', strtotime($ticket['date_event']));
        $eventTime = date('g:i A', strtotime($ticket['temp_event']));
        
        return "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #f8f9fa; padding: 20px; text-align: center; }
                    .content { padding: 20px; }
                    .footer { text-align: center; padding: 20px; font-size: 0.8em; color: #666; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>Event Reminder</h2>
                    </div>
                    <div class='content'>
                        <p>Hello,</p>
                        <p>This is a reminder for the upcoming event:</p>
                        <h3>{$ticket['title_event']}</h3>
                        <p><strong>Date:</strong> {$eventDate}</p>
                        <p><strong>Time:</strong> {$eventTime}</p>
                        <p><strong>Location:</strong> {$ticket['loc_event']}</p>
                        <p><strong>Ticket ID:</strong> {$ticket['id']}</p>
                        <p>Don't forget to bring your ticket or QR code for easy entry.</p>
                        <p>We look forward to seeing you there!</p>
                    </div>
                    <div class='footer'>
                        <p>This is an automated reminder. Please do not reply to this email.</p>
                    </div>
                </div>
            </body>
            </html>
        ";
    }
}
?> 