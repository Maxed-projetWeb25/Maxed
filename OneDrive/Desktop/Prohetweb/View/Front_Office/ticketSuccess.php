<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: /projet%20web/View/Front_Office/login.php');
    exit;
}

// Include config file for database connection
require_once 'C:/xampp/htdocs/projet web/config/config.php';

// Get the database connection
$db = Config::getInstance()->getConnection();

// Get the latest ticket for the user
$query = $db->prepare("SELECT t.*, e.title_event, e.date_event FROM ticket t 
                      JOIN event e ON t.event_id = e.id_event 
                      WHERE t.user_id = ? 
                      ORDER BY t.id DESC 
                      LIMIT 1");
$query->execute([$_SESSION['user_id']]);
$ticket = $query->fetch();

if (!$ticket) {
    header('Location: /projet%20web/View/Front_Office/eventList.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Purchase Successful</title>
    <link href="/projet%20web/View/Front_Office/assets/css/bootstrap.css" rel="stylesheet">
    <link href="/projet%20web/View/Front_Office/assets/css/style.css" rel="stylesheet">
    <style>
        body {
            background-color: #333333;
            color: #ffffff;
            padding: 20px;
        }
        .success-container {
            max-width: 600px;
            margin: 50px auto;
            background-color: #444444;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        .ticket-details {
            margin: 20px 0;
            padding: 15px;
            background-color: #555555;
            border-radius: 5px;
            text-align: left;
        }
        .qr-code {
            margin: 20px auto;
            max-width: 300px;
            display: none; /* Hide QR code by default */
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            margin-top: 20px;
        }
        .btn-group {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-container">
            <h2>Ticket Purchase Successful!</h2>
            
            <div class="ticket-details">
                <h4>Ticket Details:</h4>
                <p><strong>Event:</strong> <?php echo htmlspecialchars($ticket['title_event']); ?></p>
                <p><strong>Date:</strong> <?php echo htmlspecialchars($ticket['date_event']); ?></p>
                <p><strong>Ticket Type:</strong> <?php echo htmlspecialchars($ticket['type']); ?></p>
                <p><strong>Price:</strong> $<?php echo number_format($ticket['price'], 2); ?></p>
                <p><strong>Ticket ID:</strong> <?php echo htmlspecialchars($ticket['id']); ?></p>
            </div>
            
            <div class="qr-code" id="qrCodeContainer">
                <?php if (!empty($ticket['qr_code'])): ?>
                    <img src="<?php echo htmlspecialchars($ticket['qr_code']); ?>" alt="Ticket QR Code" class="img-fluid">
                    <p>Scan this QR code at the event entrance</p>
                <?php else: ?>
                    <p class="text-warning">QR Code not available</p>
                <?php endif; ?>
            </div>
            
            <div class="btn-group">
                <button class="btn btn-info" onclick="toggleQRCode()">
                    <i class="fas fa-qrcode"></i> View QR Code
                </button>
                <?php if (!empty($ticket['qr_code'])): ?>
                    <button class="btn btn-success" onclick="downloadQRCode()">
                        <i class="fas fa-download"></i> Download QR Code
                    </button>
                <?php endif; ?>
                <a href="/projet%20web/View/Front_Office/eventList.php" class="btn btn-primary">Back to Events</a>
            </div>
        </div>
    </div>

    <script src="/projet%20web/View/Front_Office/assets/js/jquery.js"></script>
    <script src="/projet%20web/View/Front_Office/assets/js/bootstrap.min.js"></script>
    <script>
        function toggleQRCode() {
            const qrContainer = document.getElementById('qrCodeContainer');
            if (qrContainer.style.display === 'none' || !qrContainer.style.display) {
                qrContainer.style.display = 'block';
            } else {
                qrContainer.style.display = 'none';
            }
        }

        function downloadQRCode() {
            const qrImage = document.querySelector('#qrCodeContainer img');
            if (qrImage) {
                const link = document.createElement('a');
                link.download = 'ticket-<?php echo $ticket['id']; ?>-qr.png';
                link.href = qrImage.src;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        }
    </script>
</body>
</html> 