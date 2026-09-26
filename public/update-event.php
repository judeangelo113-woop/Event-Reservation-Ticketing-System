<?php

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

// Form 
$title = $event['title'];
$description = $event['description'];
$eventDate = $event['event_date'];
$eventTime = $event['event_time'];
$venue = $event['venue'];
$capacity = $event['capacity'];
$status = $event['status'];


// PROCESS UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $eventDate = trim($_POST['event_date'] ?? '');
    $eventTime = trim($_POST['event_time'] ?? '');
    $venue = trim($_POST['venue'] ?? '');
    $capacity = trim($_POST['capacity'] ?? '');
    $status = trim($_POST['status'] ?? 'OPEN');


    
    // SERVER-SIDE VALIDATION
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

    } elseif (!filter_var($capacity, FILTER_VALIDATE_INT)) {

        $errors[] = 'Capacity must be a whole number.';

    } elseif ((int)$capacity <= 0) {

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
                (int)$capacity,
                (int)$event['available_slots'],
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

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Edit Event</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="container">

    <div class="form-header">

        <h1>Edit Event</h1>

        <p>
            Update the event information, schedule, venue, capacity, and status.
        </p>

    </div>


    <a href="events.php" class="btn btn-secondary back-button">
        ← Back to Event Management
    </a>


    <?php if (!empty($errors)): ?>

        <div class="error">

            <strong>Please fix the following errors:</strong>

            <ul>

                <?php foreach ($errors as $error): ?>

                    <li>
                        <?= htmlspecialchars($error) ?>
                    </li>

                <?php endforeach; ?>

            </ul>

        </div>

    <?php endif; ?>


    <div class="form-container">

        <form method="POST" action="">

            <div class="form-group">

                <label for="title">
                    Event Title
                </label>

                <input
                    type="text"
                    id="title"
                    name="title"
                    value="<?= htmlspecialchars($title) ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label for="description">
                    Description
                </label>

                <textarea
                    id="description"
                    name="description"
                    rows="5"
                ><?= htmlspecialchars($description) ?></textarea>

            </div>


            <div class="form-group">

                <label for="event_date">
                    Event Date
                </label>

                <input
                    type="date"
                    id="event_date"
                    name="event_date"
                    value="<?= htmlspecialchars($eventDate) ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label for="event_time">
                    Event Time
                </label>

                <input
                    type="time"
                    id="event_time"
                    name="event_time"
                    value="<?= htmlspecialchars($eventTime) ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label for="venue">
                    Venue
                </label>

                <input
                    type="text"
                    id="venue"
                    name="venue"
                    value="<?= htmlspecialchars($venue) ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label for="capacity">
                    Capacity
                </label>

                <input
                    type="number"
                    id="capacity"
                    name="capacity"
                    min="1"
                    value="<?= htmlspecialchars($capacity) ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label for="status">
                    Status
                </label>

                <select id="status" name="status">

                    <option
                        value="OPEN"
                        <?= $status === 'OPEN' ? 'selected' : '' ?>
                    >
                        OPEN
                    </option>

                    <option
                        value="CLOSED"
                        <?= $status === 'CLOSED' ? 'selected' : '' ?>
                    >
                        CLOSED
                    </option>

                    <option
                        value="CANCELLED"
                        <?= $status === 'CANCELLED' ? 'selected' : '' ?>
                    >
                        CANCELLED
                    </option>

                </select>

            </div>


            <button type="submit" class="btn btn-primary">
                Update Event
            </button>

            <a href="events.php" class="btn btn-secondary">
                Cancel
            </a>

        </form>

    </div>

</main>

<script src="../assets/js/navigation.js"></script>

</body>

</html>