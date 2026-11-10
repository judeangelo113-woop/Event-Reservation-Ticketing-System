
<?php

require_once __DIR__ . '/../includes/require_login.php';
require_once __DIR__ . '/../classes/Event.php';

// Only attendees can browse events.
if (!isAttendee()) {
    header('Location: dashboard.php');
    exit;
}

$events = [];
$error = '';

try {

    $eventModel = new Event();

    // Retrieve only open events.
    $events = $eventModel->getAllEvents('', 'OPEN', 'date_asc');

} catch (Exception $e) {

    $error = 'Unable to load events. Please try again later.';

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Browse Events - Event Ticketing System</title>

    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<div class="browse-events-container">

    <h2>Available Events</h2>

    <p class="browse-events-subtitle">
        Explore upcoming events and reserve your tickets.
    </p>

    <?php if ($error !== ''): ?>

        <p class="browse-events-error">
            <?= htmlspecialchars($error) ?>
        </p>

    <?php elseif (empty($events)): ?>

        <p class="no-events">
            No available events at the moment.
        </p>

    <?php else: ?>

        <div class="events-grid">

            <?php foreach ($events as $event): ?>

                <div class="event-card">

                    <h3>
                        <?= htmlspecialchars($event['title']) ?>
                    </h3>

                    <p>
                        <?= htmlspecialchars($event['description']) ?>
                    </p>

                    <div class="event-details">

                        <p>
                            <strong>Date:</strong>
                            <?= date('F d, Y', strtotime($event['event_date'])) ?>
                        </p>

                        <p>
                            <strong>Time:</strong>
                            <?= date('h:i A', strtotime($event['event_time'])) ?>
                        </p>

                        <p>
                            <strong>Venue:</strong>
                            <?= htmlspecialchars($event['venue']) ?>
                        </p>

                        <p>
                            <strong>Available Slots:</strong>
                            <?= (int) $event['available_slots'] ?>
                        </p>

                    </div>

                    <?php if ((int) $event['available_slots'] > 0): ?>

                        <a
                            href="reserve.php?event_id=<?= (int) $event['id'] ?>"
                            class="reserve-button">
                            Reserve Now
                        </a>

                    <?php else: ?>

                        <button class="sold-out-button" disabled>
                            Sold Out
                        </button>

                    <?php endif; ?>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>

<script src="../assets/js/navigation.js"></script>

</body>
</html>
