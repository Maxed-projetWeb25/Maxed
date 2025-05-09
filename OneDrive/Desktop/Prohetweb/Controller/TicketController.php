<?php
require_once __DIR__ . '/../Model/TicketModel.php';
require_once __DIR__ . '/../config/config.php';

class TicketController {
    private $ticketModel;

    public function __construct() {
        $this->ticketModel = new TicketModel();
    }

    // Default list action
    public function list() {
        header('Location: /projet%20web/View/Front_Office/eventList.php');
        exit;
    }

    // List all tickets
    public function listTickets() {
        $tickets = $this->ticketModel->getAllTickets();
        include __DIR__ . '/../View/Back_Office/listTickets.php';
    }

    // Front office ticket creation
    public function addTicketF() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: /projet%20web/View/Front_Office/login.php');
            exit;
        }

        $event_id = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;
        if (!$event_id) {
            header('Location: /projet%20web/View/Front_Office/eventList.php');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $type = $_POST['type'];
                $price = (float)$_POST['price'];
                $status = 'pending';
                $this->ticketModel->createTicket($event_id, $_SESSION['user_id'], $type, $price, $status);
                header('Location: /projet%20web/View/Front_Office/eventList.php?message=Ticket+created+successfully');
                exit;
            } catch (Exception $e) {
                header('Location: /projet%20web/View/Front_Office/ticketFormF.php?event_id=' . $event_id . '&error=' . urlencode($e->getMessage()));
                exit;
            }
        }
        require_once __DIR__ . '/../View/Front_Office/ticketFormF.php';
    }

    // Back office ticket creation
    public function addTicket() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $event_id = $_POST['event_id'];
                $user_id = $_POST['user_id'];
                $type = $_POST['type'];
                $price = (float)$_POST['price'];
                $status = $_POST['status'];
                $this->ticketModel->createTicket($event_id, $user_id, $type, $price, $status);
                header('Location: /projet%20web/View/Back_Office/event-datatable.php');
                exit;
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        require_once __DIR__ . '/../View/Back_Office/addTicket.php';
    }

    // Show edit form
    public function editTicket() {
        if (!isset($_GET['id'])) {
            header('Location: /projet%20web/View/Back_Office/event-datatable.php');
            exit;
        }

        $ticket = $this->ticketModel->getTicketById($_GET['id']);
        if (!$ticket) {
            header('Location: /projet%20web/View/Back_Office/event-datatable.php');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->ticketModel->updateTicket(
                $_GET['id'],
                $_POST['event_id'],
                $_POST['user_id'],
                $_POST['type'],
                $_POST['price'],
                $_POST['status']
            );
            if ($result) {
                header('Location: /projet%20web/View/Back_Office/event-datatable.php');
                exit;
            }
        }

        include __DIR__ . '/../View/Back_Office/editTicket.php';
    }

    // Delete a ticket
    public function deleteTicket() {
        if (isset($_GET['id'])) {
            $this->ticketModel->deleteTicket($_GET['id']);
        }
        header('Location: /projet%20web/View/Back_Office/event-datatable.php');
        exit;
    }

    public function getTotalTickets() {
        $sql = "SELECT COUNT(*) as total FROM ticket";
        $db = Config::getInstance()->getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute();
            $result = $query->fetch();
            return $result['total'];
        } catch (Exception $e) {
            return 0;
        }
    }

    public function getMonthlyStats() {
        $sql = "SELECT MONTH(created_at) as month, COUNT(*) as count 
                FROM ticket 
                WHERE YEAR(created_at) = YEAR(CURRENT_DATE)
                GROUP BY MONTH(created_at)";
        $db = Config::getInstance()->getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute();
            $results = $query->fetchAll();
            
            // Initialize all months with 0
            $stats = array_fill(1, 12, 0);
            
            // Fill in actual values
            foreach ($results as $row) {
                $stats[$row['month']] = (int)$row['count'];
            }
            
            return array_values($stats); // Return just the values in order
        } catch (Exception $e) {
            return array_fill(0, 12, 0);
        }
    }

    public function getTicketsByStatus() {
        $sql = "SELECT status, COUNT(*) as count 
                FROM ticket 
                GROUP BY status";
        $db = Config::getInstance()->getConnection();
        try {
            $query = $db->prepare($sql);
            $query->execute();
            $results = $query->fetchAll();
            
            $stats = array();
            foreach ($results as $row) {
                $stats[$row['status']] = (int)$row['count'];
            }
            return $stats;
        } catch (Exception $e) {
            return array();
        }
    }
}
?>
