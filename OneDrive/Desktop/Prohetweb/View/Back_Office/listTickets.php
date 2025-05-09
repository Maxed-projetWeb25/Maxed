<!DOCTYPE html>
<html>
<head>
    <title>Ticket List</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        th { background-color: #f4f4f4; }
        .action-button { padding: 5px 10px; margin: 5px; cursor: pointer; }
        .add-button { background-color: #4CAF50; color: white; border: none; }
        .edit-button { background-color: #ffa500; color: white; border: none; }
        .delete-button { background-color: #f44336; color: white; border: none; }
    </style>
</head>
<body>

<h2>List of Tickets</h2>

<!-- Button to Add New Ticket -->
<a href="index.php?controller=ticket&action=addTicket">
    <button class="action-button add-button">Add New Ticket</button>
</a>
<br><br>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Event ID</th>
            <th>User ID</th>
            <th>Type</th>
            <th>Price</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        
            require_once '../../Controller/TicketController.php';

        // Exemple typique d'initialisation dans un contrôleur ou en haut de la page
        $controller = new TicketController();
        $tickets = $controller->getAllTickets(); // Cette méthode doit renvoyer un tableau

        foreach ($tickets as $ticket): ?>
            <tr>
                <td><?= htmlspecialchars($ticket['id']) ?></td>
                <td><?= htmlspecialchars($ticket['event_id']) ?></td>
                <td><?= htmlspecialchars($ticket['user_id']) ?></td>
                <td><?= htmlspecialchars($ticket['type']) ?></td>
                <td><?= htmlspecialchars($ticket['price']) ?></td>
                <td><?= htmlspecialchars($ticket['status']) ?></td>
                <td>
                    <!-- Edit Button -->
                    <a href="index.php?controller=ticket&action=editTicket&id=<?= $ticket['id'] ?>">Edit</a>
                    <button class="action-button edit-button">Edit</button>
                    </a>


                    <!-- Delete Button -->
                    <a href="index.php?controller=ticket&action=deleteTicket&id=<?= $ticket['id'] ?>" onclick="return confirm('Are you sure you want to delete this ticket?');">
                        <button class="action-button delete-button">Delete</button>
                        
                    </a>

                </td>

                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
