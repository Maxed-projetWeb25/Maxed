<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /projet%20web/View/Front_Office/login.php');
    exit;
}

// Include config file for database connection and TicketModel
require_once 'C:/xampp/htdocs/projet web/config/config.php';
require_once 'C:/xampp/htdocs/projet web/Model/TicketModel.php';

// Get the database connection
$db = Config::getInstance()->getConnection();
$ticketModel = new TicketModel();

// Get event details
$event_id = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;
$query = $db->prepare("SELECT * FROM event WHERE id_event = ?");
$query->execute([$event_id]);
$event = $query->fetch();

if (!$event) {
    header('Location: /projet%20web/View/Front_Office/eventList.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $type = $_POST['type'];
        $price = (float)$_POST['price'];
        $status = 'valid';
        $email = $_POST['email'];
        
        // Update user's email in the database
        $updateEmailQuery = "UPDATE user SET email = ? WHERE id = ?";
        $stmt = $db->prepare($updateEmailQuery);
        $stmt->execute([$email, $_SESSION['user_id']]);
        
        // Store email in session
        $_SESSION['user_email'] = $email;
        
        // Create ticket using TicketModel
        $success = $ticketModel->createTicket($event_id, $_SESSION['user_id'], $type, $price, $status);
        
        if ($success) {
            // Redirect to success page
            header('Location: /projet%20web/View/Front_Office/ticketSuccess.php');
            exit;
        } else {
            throw new Exception("Failed to create ticket");
        }
    } catch (Exception $e) {
        header('Location: /projet%20web/View/Front_Office/addTicketF.php?event_id=' . $event_id . '&error=' . urlencode($e->getMessage()));
        exit;
    }
}

// Define ticket prices
$ticket_prices = [
    'regular' => 50.00,
    'vip' => 100.00,
    'premium' => 150.00
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Ticket</title>
    <link href="/projet%20web/View/Front_Office/assets/css/bootstrap.css" rel="stylesheet">
    <link href="/projet%20web/View/Front_Office/assets/css/style.css" rel="stylesheet">
    <style>
        body {
            background-color: #333333;
            color: #ffffff;
            padding: 20px;
        }
        .ticket-form {
            max-width: 600px;
            margin: 50px auto;
            background-color: #444444;
            padding: 20px;
            border-radius: 8px;
        }
        .ticket-info {
            margin: 20px 0;
            padding: 15px;
            background-color: #555555;
            border-radius: 5px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="ticket-form">
            <h2>Purchase Ticket</h2>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>
            
            <div class="ticket-info">
                <h4>Event Details:</h4>
                <p><strong>Event:</strong> <?php echo htmlspecialchars($event['title_event']); ?></p>
                <p><strong>Date:</strong> <?php echo htmlspecialchars($event['date_event']); ?></p>
                <p><strong>Location:</strong> <?php echo htmlspecialchars($event['loc_event']); ?></p>
            </div>
            
            <form method="POST" action="">
                <div class="form-group mb-3">
                    <label for="email">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" required 
                           value="<?php echo isset($_SESSION['user_email']) ? htmlspecialchars($_SESSION['user_email']) : ''; ?>"
                           placeholder="Enter your email for event reminders">
                    <small class="form-text text-muted">We'll send you a reminder before the event.</small>
                </div>

                <div class="form-group mb-3">
                    <label for="type">Ticket Type</label>
                    <select class="form-control" id="type" name="type" required onchange="updatePrice()">
                        <option value="regular">Regular - $50.00</option>
                        <option value="vip">VIP - $100.00</option>
                        <option value="premium">Premium - $150.00</option>
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label for="price">Price</label>
                    <input type="number" class="form-control" id="price" name="price" step="0.01" readonly>
                </div>

                <button type="submit" class="btn btn-primary">Purchase Ticket</button>
                <a href="/projet%20web/View/Front_Office/eventList.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>

    <script src="/projet%20web/View/Front_Office/assets/js/jquery.js"></script>
    <script src="/projet%20web/View/Front_Office/assets/js/bootstrap.min.js"></script>
    <script>
        function updatePrice() {
            const type = document.getElementById('type').value;
            const prices = {
                'regular': 50.00,
                'vip': 100.00,
                'premium': 150.00
            };
            document.getElementById('price').value = prices[type];
        }
        
        // Initialize price on page load
        document.addEventListener('DOMContentLoaded', updatePrice);
    </script>
</body>
</html> 