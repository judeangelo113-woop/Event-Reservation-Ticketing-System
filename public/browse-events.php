
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

    <title>Browse Events - EventlyPH</title>

    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<div class="browse-events-container">

    <!-- Page Header -->
    <div class="browse-events-header">

        <div>
            <span class="browse-events-label">DISCOVER EXPERIENCES</span>

            <h1>Explore Available Events</h1>

            <p>
                Find exciting events, discover new experiences,
                and reserve your tickets in just a few clicks.
            </p>
        </div>

        <div class="browse-events-header-icon">
            <span>🎟</span>
        </div>

    </div>

    <!-- Event Count -->
    <?php if ($error === '' && !empty($events)): ?>

        <div class="browse-events-summary">
            <div>
                <strong><?= count($events) ?></strong>
                <span>Available Events</span>
            </div>
        </div>

    <?php endif; ?>

    <!-- Error Message -->
    <?php if ($error !== ''): ?>

        <div class="browse-events-message error-message">
            <span>!</span>
            <div>
                <strong>Something went wrong</strong>
                <p><?= htmlspecialchars($error) ?></p>
            </div>
        </div>

    <!-- Empty State -->
    <?php elseif (empty($events)): ?>

        <div class="browse-events-empty">

            <div class="empty-events-icon">🎫</div>

            <h2>No Events Available</h2>

            <p>
                There are currently no open events available for reservation.
                Please check again later for upcoming events.
            </p>

            <a href="attendee-dashboard.php" class="browse-back-button">
                Back to Dashboard
            </a>

        </div>

    <!-- Events Grid -->
    <?php else: ?>

        <div class="events-grid">

            <?php foreach ($events as $event): ?>

                <?php
                    $availableSlots = (int) $event['available_slots'];
                    $isAvailable = $availableSlots > 0;

                    $eventDate = !empty($event['event_date'])
                        ? strtotime($event['event_date'])
                        : false;

                    $eventTime = !empty($event['event_time'])
                        ? strtotime($event['event_time'])
                        : false;
                ?>

                <div class="event-card">

                    <!-- Event Card Header -->
                    <div class="event-card-header">

                        <div class="event-card-icon">
                            <span>🎉</span>
                        </div>

                        <span class="event-status <?= $isAvailable ? 'status-available' : 'status-sold-out' ?>">
                            <?= $isAvailable ? 'Available' : 'Sold Out' ?>
                        </span>

                    </div>

                    <!-- Event Information -->
                    <div class="event-card-content">

                        <h2>
                            <?= htmlspecialchars($event['title']) ?>
                        </h2>

                        <p class="event-description">
                            <?= htmlspecialchars($event['description']) ?>
                        </p>

                        <div class="event-details">

                            <div class="event-detail-item">
                                <span class="event-detail-icon">📅</span>

                                <div>
                                    <small>Event Date</small>
                                    <strong>
                                        <?= $eventDate !== false
                                            ? date('F d, Y', $eventDate)
                                            : 'Not specified' ?>
                                    </strong>
                                </div>
                            </div>

                            <div class="event-detail-item">
                                <span class="event-detail-icon">🕒</span>

                                <div>
                                    <small>Event Time</small>
                                    <strong>
                                        <?= $eventTime !== false
                                            ? date('h:i A', $eventTime)
                                            : 'Not specified' ?>
                                    </strong>
                                </div>
                            </div>

                            <div class="event-detail-item">
                                <span class="event-detail-icon">📍</span>

                                <div>
                                    <small>Venue</small>
                                    <strong>
                                        <?= htmlspecialchars($event['venue']) ?>
                                    </strong>
                                </div>
                            </div>

                        </div>

                    </div>

                    <!-- Event Card Footer -->
                    <div class="event-card-footer">

                        <div class="event-slots">

                            <span class="slots-icon">🎟</span>

                            <div>
                                <strong><?= $availableSlots ?></strong>
                                <small>Available Slots</small>
                            </div>

                        </div>

                        <?php if ($isAvailable): ?>

                            <a
                                href="reserve.php?event_id=<?= (int) $event['id'] ?>"
                                class="reserve-button">
                                Reserve Now
                                <span>→</span>
                            </a>

                        <?php else: ?>

                            <button class="sold-out-button" disabled>
                                Sold Out
                            </button>

                        <?php endif; ?>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>

<script src="../assets/js/navigation.js"></script>

</body>
</html>
