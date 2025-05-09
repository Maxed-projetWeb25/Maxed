<?php
require_once __DIR__ . '/../Model/TicketModel.php';

class EventController {
    private $ticketModel;

    public function __construct() {
        $this->ticketModel = new TicketModel();
    }

    // List all tickets (paginated)
    public function listTickets() {
        $limit = 100;
        $offset = isset($_GET['page']) ? ((int)$_GET['page'] - 1) * $limit : 0;

        $tickets = $this->ticketModel->getAllTickets($limit, $offset);

        // Debugging: Check if tickets are returned
        if (empty($tickets)) {
            echo "<p style='color: red;'>Aucun ticket trouvé dans le contrôleur.</p>";
        } else {
            echo "<p style='color: green;'>Nombre de tickets récupérés: " . count($tickets) . "</p>";
        }

        include __DIR__ . '/../View/Back_Office/ticket-datatable.php';
    }

    // Show tickets on the front-end
    public function showFrontList() {
        $tickets = $this->ticketModel->getAllTickets();
        require 'View/Front_Office/ticketList.php';
    }

    // Show ticket form for creation or update
    public function showForm($id = null) {
        $ticket = null;
        if ($id) {
            $ticket = $this->ticketModel->getTicketById($id);
        }
        include __DIR__ . '/../View/Back_Office/ticketForm.php';
    }

    // Create a new ticket
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->ticketModel->addTicket(
                $_POST['event_id'],
                $_POST['user_id'],
                $_POST['type'],
                $_POST['price'],
                $_POST['status']
            );
            if ($result) {
                header('Location: index.php?controller=event&action=listTickets');
                exit;
            }
        }
    }

    // Update an existing ticket by its ID
    public function updateTicketById() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $result = $this->ticketModel->updateTicket(
                $_POST['id'],
                $_POST['event_id'],
                $_POST['user_id'],
                $_POST['type'],
                $_POST['price'],
                $_POST['status']
            );
            if ($result) {
                header('Location: index.php?controller=event&action=listTickets');
                exit;
            }
        } else {
            include __DIR__ . '/../View/Back_Office/ticketUpdateForm.php';
        }
    }

    // Delete a ticket by ID
    public function delete() {
        if (isset($_GET['id'])) {
            $this->ticketModel->deleteTicket($_GET['id']);
            header('Location: index.php?controller=event&action=listTickets');
            exit;
        }
    }
}
?>
