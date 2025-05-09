<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailNotification {
    private $db;
    private $mailer;

    public function __construct() {
        $this->db = Config::getInstance()->getConnection();
        $this->initializeMailer();
    }

    private function initializeMailer() {
        try {
            $emailConfig = require __DIR__ . '/../config/email_config.php';
            
            // Validate required configuration
            $requiredFields = ['smtp_username', 'smtp_password', 'from_email'];
            foreach ($requiredFields as $field) {
                if (empty($emailConfig[$field]) || $emailConfig[$field] === 'your.email@gmail.com' || $emailConfig[$field] === 'your-16-digit-app-password') {
                    throw new Exception("Email configuration not set. Please configure email settings in config/email_config_local.php");
                }
            }
            
            $this->mailer = new PHPMailer(true);
            
            // Server settings
            $this->mailer->isSMTP();
            $this->mailer->Host = $emailConfig['smtp_host'];
            $this->mailer->SMTPAuth = $emailConfig['smtp_auth'];
            $this->mailer->Username = $emailConfig['smtp_username'];
            $this->mailer->Password = $emailConfig['smtp_password'];
            $this->mailer->SMTPSecure = $emailConfig['smtp_secure'];
            $this->mailer->Port = $emailConfig['smtp_port'];
            
            // Default sender
            $this->mailer->setFrom($emailConfig['from_email'], $emailConfig['from_name']);
            
            // Enable debug output for troubleshooting
            $this->mailer->SMTPDebug = 0; // Set to 2 for detailed debug output
            
            // Optional settings for better reliability
            $this->mailer->Timeout = 30; // Timeout in seconds
            $this->mailer->CharSet = 'UTF-8';
            
        } catch (Exception $e) {
            error_log("Error initializing mailer: " . $e->getMessage());
            throw new Exception("Failed to initialize email system: " . $e->getMessage());
        }
    }

    public function sendEventReminder($eventId) {
        try {
            // Get event details
            $query = "SELECT * FROM event WHERE id_event = :event_id";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['event_id' => $eventId]);
            $event = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$event || !$event['email']) {
                throw new Exception("Event not found or no email address specified");
            }

            // Prepare email content
            $subject = "Reminder: " . $event['title_event'];
            $body = $this->createEmailBody($event);

            // Send email
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($event['email']);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            $this->mailer->isHTML(true);

            return $this->mailer->send();

        } catch (Exception $e) {
            error_log("Error sending reminder: " . $e->getMessage());
            throw new Exception("Failed to send reminder: " . $e->getMessage());
        }
    }

    private function createEmailBody($event) {
        $time = date('g:i A', strtotime($event['temp_event']));
        $date = date('l, F j, Y', strtotime($event['date_event']));
        
        return "
        <html>
        <body style='font-family: Arial, sans-serif;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <h2 style='color: #333;'>Event Reminder</h2>
                <h3 style='color: #666;'>{$event['title_event']}</h3>
                
                <div style='background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                    <p><strong>Date:</strong> {$date}</p>
                    <p><strong>Time:</strong> {$time}</p>
                    <p><strong>Location:</strong> {$event['loc_event']}</p>
                </div>
                
                <div style='margin-top: 20px;'>
                    <p><strong>Description:</strong></p>
                    <p>{$event['desc_event']}</p>
                </div>
                
                <div style='margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;'>
                    <p style='color: #666; font-size: 14px;'>
                        This is an automated reminder for an upcoming event you're registered for.
                    </p>
                </div>
            </div>
        </body>
        </html>";
    }

    public function checkAndSendReminders() {
        try {
            // Get events happening in the next hour that haven't had reminders sent
            $query = "SELECT e.* 
                     FROM event e 
                     LEFT JOIN notification n ON e.id_event = n.event_id 
                     WHERE e.date_event = CURRENT_DATE 
                     AND e.temp_event BETWEEN CURRENT_TIME 
                     AND ADDTIME(CURRENT_TIME, '01:00:00')
                     AND (n.status IS NULL OR n.status = 'pending')
                     AND e.email IS NOT NULL";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($events as $event) {
                if ($this->sendEventReminder($event['id_event'])) {
                    // Update notification status
                    $updateQuery = "UPDATE notification 
                                  SET status = 'sent' 
                                  WHERE event_id = :event_id";
                    $stmt = $this->db->prepare($updateQuery);
                    $stmt->execute(['event_id' => $event['id_event']]);
                }
            }

            return true;
        } catch (Exception $e) {
            error_log("Error checking reminders: " . $e->getMessage());
            return false;
        }
    }

    public function sendManualReminder($eventId) {
        try {
            return $this->sendEventReminder($eventId);
        } catch (Exception $e) {
            throw new Exception("Failed to send manual reminder: " . $e->getMessage());
        }
    }
}
?> 