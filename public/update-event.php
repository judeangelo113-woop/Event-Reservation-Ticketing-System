<?php

require_once __DIR__ . '/../includes/require_admin.php';
require_once __DIR__ . '/../classes/Event.php';

$errors = [];

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id === false || $id === null || $id <= 0) {
    die('Invalid event ID.');
}

$eventModel = new Event();

$event = $eventModel->getEventById($id);

if ($event === null) {
    die('Event not found.');
}

// Existing event information
$title = $event['title'];
$description = $event['description'];
$eventDate = $event['event_date'];
$eventTime = $event['event_time'];
$venue = $event['venue'];
$capacity = $event['capacity'];
$status = $event['status'];

// PROCESS UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!verifyCSRFToken($_POST['csrf_token'] ?? null)) {
        die('Invalid CSRF token. Please refresh the page and try again.');
    }

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $eventDate = trim($_POST['event_date'] ?? '');
    $eventTime = trim($_POST['event_time'] ?? '');
    $venue = trim($_POST['venue'] ?? '');
    $capacity = trim($_POST['capacity'] ?? '');
    $status = trim($_POST['status'] ?? 'OPEN');

    // VALIDATION

    if ($title === '') {
        $errors[] = 'Event title is required.';
    }

    if ($eventDate === '') {
        $errors[] = 'Event date is required.';
    }

    if ($eventTime === '') {
        $errors[] = 'Event time is required.';
    }

    if ($venue === '') {
        $errors[] = 'Venue is required.';
    }

    if ($capacity === '') {

        $errors[] = 'Capacity is required.';

    } elseif (filter_var($capacity, FILTER_VALIDATE_INT) === false) {

        $errors[] = 'Capacity must be a whole number.';

    } elseif ((int) $capacity <= 0) {

        $errors[] = 'Capacity must be greater than zero.';

    }

    $allowedStatuses = ['OPEN', 'CLOSED', 'CANCELLED'];

    if (!in_array($status, $allowedStatuses, true)) {
        $errors[] = 'Invalid event status.';
    }

    // UPDATE EVENT
    if (empty($errors)) {

        try {

            $eventModel = new Event(
                $title,
                $description,
                $eventDate,
                $eventTime,
                $venue,
                (int) $capacity,
                (int) $event['available_slots'],
                $status
            );

            $eventModel->setId($id);

            if ($eventModel->update()) {

                header('Location: events.php');
                exit;

            } else {

                $errors[] = 'Failed to update the event.';

            }

        } catch (Exception $e) {

            $errors[] = 'An error occurred while updating the event.';

        }

    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../assets/css/style.css">

    <title>Edit Event | EventlyPH</title>

</head>

<body>

<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="container event-form-page">

    <!-- PAGE HEADER -->
    <section class="event-form-heading">

        <div>

            <span class="event-form-eyebrow">EVENT MANAGEMENT</span>

            <h1>Edit Event</h1>

            <p>
                Update the event information, schedule, venue, capacity, and status.
            </p>

        </div>

        <a href="events.php" class="event-back-button">
            ← Back to Events
        </a>

    </section>

    <!-- FORM CARD -->
    <section class="event-form-card">

        <div class="event-form-card-heading">

            <div class="event-form-icon">✎</div>

            <div>
                <h2>Update Event Information</h2>
                <p>
                    Editing Event #<?= (int) $id ?>:
                    <?= htmlspecialchars($event['title']) ?>
                </p>
            </div>

        </div>

        <!-- VALIDATION ERRORS -->
        <?php if (!empty($errors)): ?>

            <div class="event-form-errors">

                <strong>Please check the following:</strong>

                <ul>

                    <?php foreach ($errors as $error): ?>

                        <li><?= htmlspecialchars($error) ?></li>

                    <?php endforeach; ?>

                </ul>

            </div>

        <?php endif; ?>

        <form method="POST" action="" class="event-modern-form">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars(generateCSRFToken()) ?>"
            >

            <!-- EVENT TITLE -->
            <div class="event-form-group event-form-full">

                <label for="title">
                    Event Title <span>*</span>
                </label>

                <input
                    type="text"
                    name="title"
                    id="title"
                    placeholder="Enter event title"
                    value="<?= htmlspecialchars($title) ?>"
                    required
                >

            </div>

            <!-- DESCRIPTION -->
            <div class="event-form-group event-form-full">

                <label for="description">
                    Description
                </label>

                <textarea
                    name="description"
                    id="description"
                    rows="5"
                    placeholder="Enter a short description of the event..."
                ><?= htmlspecialchars($description) ?></textarea>

            </div>

            <!-- EVENT DATE -->
            <div class="event-form-group">

                <label for="event_date">
                    Event Date <span>*</span>
                </label>

                <input
                    type="date"
                    name="event_date"
                    id="event_date"
                    value="<?= htmlspecialchars($eventDate) ?>"
                    required
                >

            </div>

            <!-- EVENT TIME -->
            <div class="event-form-group">

                <label for="event_time">
                    Event Time <span>*</span>
                </label>

                <input
                    type="time"
                    name="event_time"
                    id="event_time"
                    value="<?= htmlspecialchars($eventTime) ?>"
                    required
                >

            </div>

            <!-- VENUE -->
            <div class="event-form-group event-form-full">

                <label for="venue">
                    Venue <span>*</span>
                </label>

                <input
                    type="text"
                    name="venue"
                    id="venue"
                    placeholder="Enter event location or venue"
                    value="<?= htmlspecialchars($venue) ?>"
                    required
                >

            </div>

            <!-- CAPACITY -->
            <div class="event-form-group">

                <label for="capacity">
                    Event Capacity <span>*</span>
                </label>

                <input
                    type="number"
                    name="capacity"
                    id="capacity"
                    min="1"
                    placeholder="Enter maximum attendees"
                    value="<?= htmlspecialchars((string) $capacity) ?>"
                    required
                >

                <small>Maximum number of attendees allowed.</small>

            </div>

            <!-- STATUS -->
            <div class="event-form-group">

                <label for="status">
                    Event Status <span>*</span>
                </label>

                <select id="status" name="status" required>

                    <option value="OPEN"
                        <?= $status === 'OPEN' ? 'selected' : '' ?>>
                        OPEN
                    </option>

                    <option value="CLOSED"
                        <?= $status === 'CLOSED' ? 'selected' : '' ?>>
                        CLOSED
                    </option>

                    <option value="CANCELLED"
                        <?= $status === 'CANCELLED' ? 'selected' : '' ?>>
                        CANCELLED
                    </option>

                </select>

                <small>Choose the current availability of the event.</small>

            </div>

            <!-- FORM ACTIONS -->
            <div class="event-form-actions event-form-full">

                <a href="events.php" class="event-cancel-button">
                    Cancel
                </a>

                <button type="submit" class="event-submit-button">
                    Save Changes
                </button>

            </div>

        </form>

    </section>

</main>

<script src="../assets/js/navigation.js"></script>

</body>
</html>