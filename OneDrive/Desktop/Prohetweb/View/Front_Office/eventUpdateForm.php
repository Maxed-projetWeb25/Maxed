<!-- This is very similar to the create form, but separated for clarity -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
</head>
<body>
    <h1>Edit Event</h1>
    <form action="index.php?controller=event&action=updateEventByTitle" method="POST">
        <input type="hidden" name="id_event" value="<?= $event->getIdEvent() ?>">

        <label for="title_event">Title:</label>
        <input type="text" id="title_event" name="title_event" value="<?= htmlspecialchars($event->getTitleEvent()) ?>" required><br>

        <label for="desc_event">Description:</label>
        <textarea id="desc_event" name="desc_event" required><?= htmlspecialchars($event->getDescEvent()) ?></textarea><br>

        <label for="temp_event">Temperature:</label>
        <input type="text" id="temp_event" name="temp_event" value="<?= htmlspecialchars($event->getTempEvent()) ?>" required><br>

        <label for="date_event">Date:</label>
        <input type="date" id="date_event" name="date_event" value="<?= htmlspecialchars($event->getDateEvent()) ?>" required><br>

        <label for="loc_event">Location:</label>
        <input type="text" id="loc_event" name="loc_event" value="<?= htmlspecialchars($event->getLocEvent()) ?>" required><br>

        <label for="cap_event">Capacity:</label>
        <input type="number" id="cap_event" name="cap_event" value="<?= htmlspecialchars($event->getCapEvent()) ?>" required><br>

        <button type="submit">Update Event</button>
    </form>
</body>
</html>
