<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $event ? 'Update' : 'Create' ?> Event</title>
</head>
<body>
    <h1><?= $event ? 'Update' : 'Create' ?> Event</h1>

    <form method="POST">
        <?php if ($event): ?>
            <input type="hidden" name="id_event" value="<?= $event->getIdEvent() ?>">
        <?php endif; ?>
        
        <label for="title_event">Title:</label>
        <input type="text" id="title_event" name="title_event" value="<?= $event ? htmlspecialchars($event->getTitleEvent()) : '' ?>" required><br>

        <label for="desc_event">Description:</label>
        <textarea id="desc_event" name="desc_event" required><?= $event ? htmlspecialchars($event->getDescEvent()) : '' ?></textarea><br>

        <label for="temp_event">Temperature:</label>
        <input type="text" id="temp_event" name="temp_event" value="<?= $event ? htmlspecialchars($event->getTempEvent()) : '' ?>" required><br>

        <label for="date_event">Date:</label>
        <input type="date" id="date_event" name="date_event" value="<?= $event ? htmlspecialchars($event->getDateEvent()) : '' ?>" required><br>

        <label for="loc_event">Location:</label>
        <input type="text" id="loc_event" name="loc_event" value="<?= $event ? htmlspecialchars($event->getLocEvent()) : '' ?>" required><br>

        <label for="cap_event">Capacity:</label>
        <input type="number" id="cap_event" name="cap_event" value="<?= $event ? htmlspecialchars($event->getCapEvent()) : '' ?>" required><br>
    </form>
</body>
</html>
