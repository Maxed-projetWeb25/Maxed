<?php
require_once __DIR__ . '/../config/Config.php';

class Ticket
{
    private $id;
    private $event_id;
    private $user_id;
    private $type;
    private $price;
    private $status;

    private static $db;

    public static function getInstance() {
        if (!isset(self::$db)) {
            try {
                self::$db = Config::getInstance()->getConnection();
            } catch (Exception $e) {
                error_log("Error initializing Ticket database connection: " . $e->getMessage());
                throw new Exception("Database connection failed");
            }
        }
        return self::$db;
    }

    // Getters and Setters
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getEventId() {
        return $this->event_id;
    }

    public function setEventId($event_id) {
        $this->event_id = $event_id;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function setUserId($user_id) {
        $this->user_id = $user_id;
    }

    public function getType() {
        return $this->type;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function getPrice() {
        return $this->price;
    }

    public function setPrice($price) {
        $this->price = $price;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    // CRUD Methods
    public static function createTicket($event_id, $user_id, $type, $price, $status)
    {
        try {
            $db = self::getInstance();
            $query = "INSERT INTO ticket (event_id, user_id, type, price, status) 
                     VALUES (:event_id, :user_id, :type, :price, :status)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':event_id', $event_id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':type', $type);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':status', $status);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error in createTicket: " . $e->getMessage());
            return false;
        }
    }

    public static function getTicketById($id)
    {
        try {
            $db = self::getInstance();
            $query = "SELECT * FROM ticket WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getTicketById: " . $e->getMessage());
            return null;
        }
    }

    public static function getAllTickets()
    {
        try {
            $db = self::getInstance();
            $query = "SELECT * FROM ticket";
            $stmt = $db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error in getAllTickets: " . $e->getMessage());
            return [];
        }
    }

    public static function updateTicket($id, $event_id, $user_id, $type, $price, $status)
    {
        try {
            $db = self::getInstance();
            $query = "UPDATE ticket 
                     SET event_id = :event_id, 
                         user_id = :user_id, 
                         type = :type, 
                         price = :price, 
                         status = :status 
                     WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':event_id', $event_id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':type', $type);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':status', $status);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error in updateTicket: " . $e->getMessage());
            return false;
        }
    }
    
    public static function deleteTicket($id)
    {
        try {
            $db = self::getInstance();
            $query = "DELETE FROM ticket WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error in deleteTicket: " . $e->getMessage());
            return false;
        }
    }

    // Alias for getAllTickets to maintain compatibility
    public static function getTicket()
    {
        return self::getAllTickets();
    }
}

?>
