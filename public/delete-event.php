
<?php

require_once __DIR__ . '/../includes/require_admin.php';
require_once __DIR__ . '/../classes/Event.php';

$errors = [];

$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

if ($id <= 0) {
    die('Invalid event ID.');
}

try {

    $eventModel = new Event();

    // Retrieve event details
    $event = $eventModel->getEventById($id);

    if ($event === null) {
        die('Event not found.');
    }

    // Process deletion
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $postedId = (int) ($_POST['id'] ?? 0);

        if ($postedId !== $id) {

            $errors[] = 'Invalid event ID.';

        } elseif (!verifyCSRFToken($_POST['csrf_token'] ?? null)) {

            $errors[] = 'Invalid or expired request. Please refresh the page and try again.';

        } else {

            $eventModel->setId($id);

            if ($eventModel->delete()) {

                header(
                    'Location: ../public/events.php?message=' .
                    urlencode('Event deleted successfully')
                );

                exit;

            } else {

                $errors[] = 'Unable to delete the event. It may have existing reservations or related records.';
            }
        }
    }

} catch (Exception $e) {

    $errors[] = 'An error occurred while deleting the event. Please check its related records and try again.';

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Delete Event | EventlyPH</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="container event-delete-page">

    <div class="event-delete-header">

        <span class="event-delete-label">
            EVENT MANAGEMENT
        </span>

        <h1>Delete Event</h1>

        <p>
            Review the event information before confirming deletion.
        </p>

    </div>

    <a href="../public/events.php" class="event-delete-back">
        &larr; Back to Event Management
    </a>

    <?php if (!empty($errors)): ?>

        <div class="event-delete-error">

            <strong>Unable to delete event:</strong>

            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>

        </div>

    <?php endif; ?>

    <div class="event-delete-card">

        <div class="event-delete-icon">!</div>

        <h2>Confirm Event Deletion</h2>

        <p class="event-delete-description">
            Are you sure you want to permanently delete this event?
            Please review its details before proceeding.
        </p>

        <div class="event-delete-details">

            <div class="event-delete-detail">
                <span>Event ID</span>
                <strong>#<?= (int) $event['id'] ?></strong>
            </div>

            <div class="event-delete-detail">
                <span>Event Title</span>
                <strong><?= htmlspecialchars($event['title']) ?></strong>
            </div>

            <div class="event-delete-detail">
                <span>Date</span>
                <strong><?= htmlspecialchars($event['event_date']) ?></strong>
            </div>

            <div class="event-delete-detail">
                <span>Time</span>
                <strong><?= htmlspecialchars($event['event_time']) ?></strong>
            </div>

            <div class="event-delete-detail">
                <span>Venue</span>
                <strong><?= htmlspecialchars($event['venue']) ?></strong>
            </div>

            <div class="event-delete-detail">
                <span>Total Capacity</span>
                <strong><?= (int) $event['capacity'] ?></strong>
            </div>

            <div class="event-delete-detail">
                <span>Available Slots</span>
                <strong><?= (int) $event['available_slots'] ?></strong>
            </div>

            <div class="event-delete-detail">
                <span>Event Status</span>

                <strong>
                    <span class="event-delete-status">
                        <?= htmlspecialchars($event['status']) ?>
                    </span>
                </strong>
            </div>

        </div>

        <div class="event-delete-warning">

            <strong>Important Warning</strong>

            <p>
                Deleting this event may also affect its associated
                reservations and tickets, depending on your database
                relationships. This action cannot be undone.
            </p>

        </div>

        <form method="POST" action="">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars(generateCSRFToken()) ?>"
            >

            <input
                type="hidden"
                name="id"
                value="<?= (int) $event['id'] ?>"
            >

            <div class="event-delete-actions">

                <a href="../public/events.php" class="event-delete-cancel">
                    Cancel
                </a>

                <button type="submit" class="event-delete-confirm">
                    Yes, Delete Event
                </button>

            </div>

        </form>

    </div>

</main>

<script src="../assets/js/navigation.js"></script>

</body>
</html>
