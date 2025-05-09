<?php include 'layout/header.php'; ?>

<h2>Select Event Title to Update</h2>

<form action="index.php?controller=event&action=showUpdateFormByTitle" method="POST">
    <label for="title_event">Title:</label>
    <input type="text" name="title_event" required>
    <button type="submit">Edit</button>
</form>

<a href="index.php?controller=event&action=listEvents">← Back to Events</a>

<?php include 'layout/footer.php'; ?>
