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

    public function __construct(
        $id_event = null,
        $title_event = null,
        $desc_event = null,
        $temp_event = null,
        $date_event = null,
        $loc_event = null,
        $cap_event = null
    ) {
        $this->id_event = $id_event;
        $this->title_event = $title_event;
        $this->desc_event = $desc_event;
        $this->temp_event = $temp_event;
        $this->date_event = $date_event;
        $this->loc_event = $loc_event;
        $this->cap_event = $cap_event;
    }

    // Getters
    public function getIdEvent() { return $this->id_event; }
    public function getTitleEvent() { return $this->title_event; }
    public function getDescEvent() { return $this->desc_event; }
    public function getTempEvent() { return $this->temp_event; }
    public function getDateEvent() { return $this->date_event; }
    public function getLocEvent() { return $this->loc_event; }
    public function getCapEvent() { return $this->cap_event; }

    // Setters
    public function setIdEvent($id_event) { $this->id_event = $id_event; }
    public function setTitleEvent($title_event) { $this->title_event = $title_event; }
    public function setDescEvent($desc_event) { $this->desc_event = $desc_event; }
    public function setTempEvent($temp_event) { $this->temp_event = $temp_event; }
    public function setDateEvent($date_event) { $this->date_event = $date_event; }
    public function setLocEvent($loc_event) { $this->loc_event = $loc_event; }
    public function setCapEvent($cap_event) { $this->cap_event = $cap_event; }
}




class EventModel {
    private $conn;

    public function __construct() {
        $this->conn = config::getConnexion();
        if ($this->conn) {
            echo "<p style='color: green;'>Connexion à la base de données réussie!</p>";
        } else {
            echo "<p style='color: red;'>Échec de la connexion à la base de données!</p>";
        }
    }
    public function getAllEvents1() {
        $query = "SELECT id_event, title_event, desc_event, temp_event, date_event, cap_event, loc_event FROM event";
        $stmt = $this->conn->prepare($query);
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
                $row['cap_event']
            );
        }
        return $events;
    }
    
    
    // Fetch all events with pagination
    public function getAllEvents($limit = 100, $offset = 0) {
        $stmt = $this->conn->prepare("SELECT * FROM event LIMIT :limit OFFSET :offset");
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    
        try {
            $stmt->execute();
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Débogage: Vérifiez les résultats de la requête
            if (!$events) {
                echo "<p style='color: red;'>Aucun événement trouvé dans la base de données.</p>";
            } else {
                echo "<p style='color: green;'>Nombre d'événements trouvés: " . count($events) . "</p>";
                print_r($events); // Affiche les résultats de la requête
            }
    
            // Convertir les résultats en objets Event
            $eventObjects = [];
            foreach ($events as $event) {
                $eventObjects[] = new Event(
                    $event['id_event'],
                    $event['title_event'],
                    $event['desc_event'],
                    $event['temp_event'],
                    $event['date_event'],
                    $event['loc_event'],
                    $event['cap_event']
                );
            }
    
            return $eventObjects;
        } catch (PDOException $e) {
            echo "<p style='color: red;'>Erreur lors de la récupération des événements: " . $e->getMessage() . "</p>";
            return [];
        }
    }
    

    // Fetch event by ID
    public function getEventById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM event WHERE id_event = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $event = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($event) {
            return new Event(
                $event['id_event'],
                $event['title_event'],
                $event['desc_event'],
                $event['temp_event'],
                $event['date_event'],
                $event['loc_event'],
                $event['cap_event']
            );
        }
        return null;
    }

    // Create new event
    public function createEvent(Event $event) {
        $stmt = $this->conn->prepare("INSERT INTO event (title_event, desc_event, temp_event, date_event, loc_event, cap_event) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $event->getTitleEvent(),
            $event->getDescEvent(),
            $event->getTempEvent(),
            $event->getDateEvent(),
            $event->getLocEvent(),
            $event->getCapEvent()
        ]);
    }

    // Update an existing event
    public function updateEvent(Event $event) {
        $stmt = $this->conn->prepare("UPDATE event SET title_event = ?, desc_event = ?, temp_event = ?, date_event = ?, loc_event = ?, cap_event = ? WHERE id_event = ?");
        return $stmt->execute([
            $event->getTitleEvent(),
            $event->getDescEvent(),
            $event->getTempEvent(),
            $event->getDateEvent(),
            $event->getLocEvent(),
            $event->getCapEvent(),
            $event->getIdEvent()
        ]);
    }

    // Delete event by ID
    public function deleteEvent($id) {
        $stmt = $this->conn->prepare("DELETE FROM event WHERE id_event = ?");
        return $stmt->execute([$id]);
    }
}
?>
