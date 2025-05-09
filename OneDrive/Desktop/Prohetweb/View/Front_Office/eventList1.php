<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event List</title>
</head>
<body>
    <h1>Event List</h1>
    <table border="1">
        <thead>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>TempS</th>
                <th>Date</th>
                <th>Location</th>
                <th>Capacity</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($events as $event): ?>
                <tr>
                    <td><?= htmlspecialchars($event->getTitleEvent()) ?></td>
                    <td><?= htmlspecialchars($event->getDescEvent()) ?></td>
                    <td><?= htmlspecialchars($event->getTempEvent()) ?></td>
                    <td><?= htmlspecialchars($event->getDateEvent()) ?></td>
                    <td><?= htmlspecialchars($event->getLocEvent()) ?></td>
                    <td><?= htmlspecialchars($event->getCapEvent()) ?></td>
                    
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
