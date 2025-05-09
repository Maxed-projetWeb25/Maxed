<?php
require_once __DIR__ . '/../config/config.php';

class Event {
    private $id_event;
    private $title_event;
    private $desc_event;
    private $temp_event;
    private $date_event;
    private $loc_event;
    private $cap_event;
    private $email;

    public function __construct(
        $id_event = null,
        $title_event = null,
        $desc_event = null,
        $temp_event = null,
        $date_event = null,
        $loc_event = null,
        $cap_event = null,
        $email = null
    ) {
        $this->id_event = $id_event;
        $this->title_event = $title_event;
        $this->desc_event = $desc_event;
        // Ensure time is in HH:mm format
        $this->temp_event = $temp_event ? date('H:i', strtotime($temp_event)) : null;
        $this->date_event = $date_event;
        $this->loc_event = $loc_event;
        $this->cap_event = $cap_event;
        $this->email = $email;
    }

    // Getters
    public function getIdEvent() { return $this->id_event; }
    public function getTitleEvent() { return $this->title_event; }
    public function getDescEvent() { return $this->desc_event; }
    public function getTempEvent() { return $this->temp_event; }
    public function getDateEvent() { return $this->date_event; }
    public function getLocEvent() { return $this->loc_event; }
    public function getCapEvent() { return $this->cap_event; }
    public function getEmail() { return $this->email; }

    // Setters
    public function setIdEvent($id_event) { $this->id_event = $id_event; }
    public function setTitleEvent($title_event) { $this->title_event = $title_event; }
    public function setDescEvent($desc_event) { $this->desc_event = $desc_event; }
    public function setTempEvent($temp_event) { $this->temp_event = $temp_event; }
    public function setDateEvent($date_event) { $this->date_event = $date_event; }
    public function setLocEvent($loc_event) { $this->loc_event = $loc_event; }
    public function setCapEvent($cap_event) { $this->cap_event = $cap_event; }
    public function setEmail($email) { $this->email = $email; }
}




class EventModel {
    private $db;

    public function __construct() {
        try {
            $config = Config::getInstance();
            $this->db = $config->getConnection();
        } catch (Exception $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }

    public function getAllEvents1() {
        $query = "SELECT id_event, title_event, desc_event, temp_event, date_event, cap_event, loc_event FROM event";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        $events = [];
        foreach ($rows as $row) {
            $events[] = new Event(
                $row['id_event'],
                $row['title_event'],
                $row['desc_event'],
                $row['temp_event'],
                $row['date_event'],
                $row['loc_event'],
                $row['cap_event'],
                $row['email']
            );
        }
        return $events;
    }
    
    
    // Fetch all events with pagination
    public function getAllEvents($limit = 100, $offset = 0) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM event ORDER BY id_event DESC LIMIT :limit OFFSET :offset");
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $eventObjects = [];
            foreach ($events as $event) {
                $eventObjects[] = new Event(
                    $event['id_event'],
                    $event['title_event'],
                    $event['desc_event'],
                    $event['temp_event'],
                    $event['date_event'],
                    $event['loc_event'],
                    $event['cap_event'],
                    $event['email']
                );
            }
            return $eventObjects;
        } catch (PDOException $e) {
            error_log("Error in getAllEvents: " . $e->getMessage());
            return [];
        }
    }
    

    // Fetch event by ID
    public function getEventById($id) {
        try {
            $query = "SELECT * FROM event WHERE id_event = :id";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Failed to get event: " . $e->getMessage());
        }
    }

    // Create new event
    public function createEvent(Event $event) {
        try {
            $stmt = $this->db->prepare("INSERT INTO event (title_event, desc_event, temp_event, date_event, loc_event, cap_event, email) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $result = $stmt->execute([
                $event->getTitleEvent(),
                $event->getDescEvent(),
                $event->getTempEvent(),
                $event->getDateEvent(),
                $event->getLocEvent(),
                $event->getCapEvent(),
                $event->getEmail()
            ]);

            if ($result) {
                $event->setIdEvent($this->db->lastInsertId());
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error in createEvent: " . $e->getMessage());
            return false;
        }
    }

    // Update an existing event
    public function updateEvent($id, $data) {
        try {
            $this->db->beginTransaction();

            // Debug: Log incoming data
            error_log("Update Event ID: " . $id);
            error_log("Update Data: " . print_r($data, true));

            // Validate and prepare data
            $validFields = ['title_event', 'desc_event', 'temp_event', 'date_event', 'loc_event', 'cap_event', 'email'];
            $updateData = [];

            // Only include fields that exist in the data array
            foreach ($validFields as $field) {
                if (isset($data[$field])) {
                    // Special handling for time format
                    if ($field === 'temp_event') {
                        if (preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $data[$field])) {
                            $updateData[$field] = $data[$field];
                        } elseif (is_numeric($data[$field])) {
                            $hours = floor($data[$field]);
                            $minutes = round(($data[$field] - $hours) * 60);
                            $updateData[$field] = sprintf("%02d:%02d:00", $hours, $minutes);
                        } else {
                            throw new Exception("Invalid time format");
                        }
                    }
                    // Special handling for date format
                    elseif ($field === 'date_event') {
                        $date = date('Y-m-d', strtotime($data[$field]));
                        if (!$date) {
                            throw new Exception("Invalid date format");
                        }
                        $updateData[$field] = $date;
                    }
                    // Handle capacity as integer
                    elseif ($field === 'cap_event') {
                        $updateData[$field] = (int)$data[$field];
                    }
                    // Handle other fields as strings
                    else {
                        $updateData[$field] = $data[$field];
                    }
                }
            }

            if (empty($updateData)) {
                throw new Exception("No valid fields to update");
            }

            // Build the update query
            $setStatements = [];
            foreach ($updateData as $field => $value) {
                $setStatements[] = "$field = :$field";
            }

            $query = "UPDATE event SET " . implode(', ', $setStatements) . " WHERE id_event = :id";
            error_log("Update Query: " . $query);
            error_log("Update Data: " . print_r($updateData, true));

            $stmt = $this->db->prepare($query);

            // Bind parameters
            foreach ($updateData as $field => $value) {
                $type = ($field === 'cap_event') ? PDO::PARAM_INT : PDO::PARAM_STR;
                error_log("Binding $field = " . print_r($value, true));
                $stmt->bindValue(":$field", $value, $type);
            }
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);

            // Execute the update
            $result = $stmt->execute();
            error_log("Execute result: " . ($result ? "true" : "false"));

            if (!$result) {
                error_log("PDO Error Info: " . print_r($stmt->errorInfo(), true));
                throw new Exception("Database update failed");
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            error_log("Error in updateEvent: " . $e->getMessage());
            error_log("Error trace: " . $e->getTraceAsString());
            $this->db->rollBack();
            throw new Exception("Failed to update event: " . $e->getMessage());
        }
    }

    // Delete event by ID
    public function deleteEvent($id) {
        $stmt = $this->db->prepare("DELETE FROM event WHERE id_event = ?");
        return $stmt->execute([$id]);
    }
}
?>
