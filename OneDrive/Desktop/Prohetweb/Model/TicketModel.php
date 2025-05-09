<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Endroid\QrCode\Builder\BuilderInterface;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Writer\PngWriter;

class TicketModel {
    private $db;

    public function __construct() {
        try {
            if (!class_exists('Config')) {
                throw new Exception("Config class not found");
            }
            
            $config = Config::getInstance();
            if (!$config) {
                throw new Exception("Failed to get Config instance");
            }
            
            $this->db = $config->getConnection();
            if (!$this->db) {
                throw new Exception("Failed to get database connection");
            }
        } catch (Exception $e) {
            error_log("Error initializing TicketModel: " . $e->getMessage());
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }

    private function generateQRCode($ticketId, $eventId, $type, $price) {
        try {
            // Get event details
            $query = "SELECT title_event, date_event FROM event WHERE id_event = :event_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':event_id', $eventId);
            $stmt->execute();
            $event = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$event) {
                throw new Exception("Event not found");
            }

            // Create QR code data
            $qrData = json_encode([
                'ticket_id' => $ticketId,
                'event' => $event['title_event'],
                'date' => $event['date_event'],
                'type' => $type,
                'price' => $price
            ]);

            // Create QR code
            $result = (new PngWriter())->write(
                (new QrCode($qrData))
                    ->setSize(300)
                    ->setMargin(10)
                    ->setForegroundColor(new Color(0, 0, 0))
                    ->setBackgroundColor(new Color(255, 255, 255))
                    ->setErrorCorrectionLevel(new ErrorCorrectionLevelHigh())
            );

            return $result->getDataUri();
        } catch (Exception $e) {
            error_log("Error generating QR code: " . $e->getMessage());
            return null;
        }
    }

    public function createTicket($event_id, $user_id, $type, $price, $status) {
        try {
            $this->db->beginTransaction();

            // Insert ticket
            $query = "INSERT INTO ticket (event_id, user_id, type, price, status) 
                     VALUES (:event_id, :user_id, :type, :price, :status)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':event_id', $event_id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':type', $type);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':status', $status);
            $stmt->execute();

            // Get the inserted ticket ID
            $ticketId = $this->db->lastInsertId();

            // Generate QR code
            $qrCode = $this->generateQRCode($ticketId, $event_id, $type, $price);

            // Update ticket with QR code
            if ($qrCode) {
                $query = "UPDATE ticket SET qr_code = :qr_code WHERE id = :id";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':qr_code', $qrCode);
                $stmt->bindParam(':id', $ticketId);
                $stmt->execute();
            }

            // Create notification for the event
            $query = "INSERT INTO notification (user_id, event_id, notification_time) 
                     SELECT ?, ?, 
                            SUBTIME(CONCAT(e.date_event, ' ', e.temp_event), '00:30:00')
                     FROM event e 
                     WHERE e.id_event = ?";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([$user_id, $event_id, $event_id]);

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Error in createTicket: " . $e->getMessage());
            throw new Exception("Failed to create ticket: " . $e->getMessage());
        }
    }

    public function getAllTickets() {
        try {
            $query = "SELECT t.*, e.title_event, e.date_event 
                     FROM ticket t 
                     LEFT JOIN event e ON t.event_id = e.id_event";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getAllTickets: " . $e->getMessage());
            return [];
        }
    }

    public function getTicketById($id) {
        try {
            $query = "SELECT t.*, e.title_event, e.date_event 
                     FROM ticket t 
                     LEFT JOIN event e ON t.event_id = e.id_event 
                     WHERE t.id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getTicketById: " . $e->getMessage());
            return null;
        }
    }

    public function updateTicket($id, $event_id, $user_id, $type, $price, $status) {
        try {
            $this->db->beginTransaction();

            // Update ticket
            $query = "UPDATE ticket 
                     SET event_id = :event_id, 
                         user_id = :user_id, 
                         type = :type, 
                         price = :price, 
                         status = :status 
                     WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':event_id', $event_id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':type', $type);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':status', $status);
            $stmt->execute();

            // Generate new QR code
            $qrCode = $this->generateQRCode($id, $event_id, $type, $price);

            // Update QR code
            if ($qrCode) {
                $query = "UPDATE ticket SET qr_code = :qr_code WHERE id = :id";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':qr_code', $qrCode);
                $stmt->bindParam(':id', $id);
                $stmt->execute();
            }

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Error in updateTicket: " . $e->getMessage());
            return false;
        }
    }

    public function deleteTicket($id) {
        try {
            $query = "DELETE FROM ticket WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error in deleteTicket: " . $e->getMessage());
            return false;
        }
    }

    public function getTicketsByEventId($event_id) {
        try {
            $query = "SELECT t.*, e.title_event, e.date_event 
                     FROM ticket t 
                     LEFT JOIN event e ON t.event_id = e.id_event 
                     WHERE t.event_id = :event_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':event_id', $event_id);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getTicketsByEventId: " . $e->getMessage());
            return [];
        }
    }

    public function getTicketsByUserId($user_id) {
        try {
            $query = "SELECT t.*, e.title_event, e.date_event 
                     FROM ticket t 
                     LEFT JOIN event e ON t.event_id = e.id_event 
                     WHERE t.user_id = :user_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getTicketsByUserId: " . $e->getMessage());
            return [];
        }
    }
}
?>