<?php include __DIR__ . '/layout/header.php'; ?>

<main>
    <h2>Add New Event</h2>
    <form method="POST" action="/Prohetweb/event/store">
        <label for="title">Title:</label>
        <input type="text" name="title" required><br><br>

        <label for="description">Description:</label>
        <textarea name="description" required></textarea><br><br>

        <label for="temp">Temperature:</label>
        <input type="text" name="temp" required><br><br>

        <label for="date">Date:</label>
        <input type="date" name="date" required><br><br>

        <label for="location">Location:</label>
        <input type="text" name="location" required><br><br>

        <label for="capacity">Capacity:</label>
        <input type="number" name="capacity" required><br><br>

        <button type="submit">Add Event</button>
    </form>
</main>

<?php include __DIR__ . '/layout/footer.php'; ?>
