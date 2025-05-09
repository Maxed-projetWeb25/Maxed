<?php include __DIR__ . '/layout/header.php'; ?>

<main>
    <h2>Edit Event</h2>
    <form method="POST" action="/Prohetweb/event/update/<?= $event['id_event']; ?>">
        <input type="hidden" name="id" value="<?= $event['id_event']; ?>">

        <label for="title">Title:</label>
        <input type="text" name="title" value="<?= htmlspecialchars($event['title_event']); ?>" required><br><br>

        <label for="description">Description:</label>
        <textarea name="description" required><?= htmlspecialchars($event['desc_event']); ?></textarea><br><br>

        <label for="temp">Temperature:</label>
        <input type="text" name="temp" value="<?= htmlspecialchars($event['temp_event']); ?>" required><br><br>

        <label for="date">Date:</label>
        <input type="date" name="date" value="<?= htmlspecialchars($event['date_event']); ?>" required><br><br>

        <label for="location">Location:</label>
        <input type="text" name="location" value="<?= htmlspecialchars($event['loc_event']); ?>" required><br><br>

        <label for="capacity">Capacity:</label>
        <input type="number" name="capacity" value="<?= htmlspecialchars($event['cap_event']); ?>" required><br><br>

        <button type="submit">Update Event</button>
    </form>
</main>

<?php include __DIR__ . '/layout/footer.php'; ?>
