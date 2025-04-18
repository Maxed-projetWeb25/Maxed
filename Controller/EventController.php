<?php
require_once __DIR__ . '/../Model/EventModel.php';

class EventController {
    private $model;


    public function __construct() {
        $this->model = new EventModel();
    }
   
    public function listEvents() {
        $limit = 100;
        $offset = isset($_GET['page']) ? ((int)$_GET['page'] - 1) * $limit : 0;

        $events = $this->model->getAllEvents($limit, $offset);

        // Debugging: Check if events are returned
        if (empty($events)) {
            echo "<p style='color: red;'>Aucun événement trouvé dans le contrôleur.</p>";
        } else {
            echo "<p style='color: green;'>Nombre d'événements récupérés: " . count($events) . "</p>";
        }

        include __DIR__ . '/../View/Back_Office/event-datatable.php';
    }
    // Controller/EventController.php

public function showFrontList() {
    require_once 'Model/EventModel.php';
    $model = new EventModel();
    $events = $model->getAllEvents1(); // This should return an array of Event objects

    require 'View/Front_Office/eventList.php'; // This view will now have access to $events
}


    public function showForm($id = null) {
        $event = null;
        if ($id) {
            $event = $this->model->getEventById($id);
        }
        include __DIR__ . '/../View/Front_Office/eventForm.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $event = new Event(
                null,
                $_POST['title_event'],
                $_POST['desc_event'],
                $_POST['temp_event'],
                $_POST['date_event'],
                $_POST['loc_event'],
                $_POST['cap_event']
            );
            $this->model->createEvent($event);
            header('Location: index.php?controller=event&action=listEvents');
        }
    }

    public function updateEventByTitle() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title_event'])) {
            $event = $this->model->getEventById($_POST['id_event']);
            if ($event) {
                $updatedEvent = new Event(
                    $event->getIdEvent(),
                    $_POST['title_event'],
                    $_POST['desc_event'],
                    $_POST['temp_event'],
                    $_POST['date_event'],
                    $_POST['loc_event'],
                    $_POST['cap_event']
                );
                $this->model->updateEvent($updatedEvent);
                header('Location: index.php?controller=event&action=listEvents');
            }
        } else {
            include __DIR__ . '/../View/Front_Office/eventUpdateForm.php';
        }
    }

    public function delete() {
        if (isset($_GET['id'])) {
            $this->model->deleteEvent($_GET['id']);
            header('Location: index.php?controller=event&action=listEvents');
        }
    }
}
?>
