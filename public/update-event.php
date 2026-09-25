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

// Form <values></values>
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

</head>

<body>

    <h1>Edit Event</h1>

    <a href="events.php">← Back to Event Management</a>

    <br><br>


    <?php if (!empty($errors)): ?>

        <div>

            <strong>Please fix the following errors:</strong>

            <ul>

                <?php foreach ($errors as $error): ?>

                    <li>
                        <?php echo htmlspecialchars($error); ?>
                    </li>

                <?php endforeach; ?>

            </ul>

        </div>

    <?php endif; ?>


    <form method="POST" action="">

        <div>

            <label for="title">
                Event Title:
            </label>

            <br>

            <input
                type="text"
                id="title"
                name="title"
                value="<?php echo htmlspecialchars($title); ?>"
                required
            >

        </div>

        <br>


        <div>

            <label for="description">
                Description:
            </label>

            <br>

            <textarea
                id="description"
                name="description"
                rows="5"
                cols="40"
            ><?php echo htmlspecialchars($description); ?></textarea>

        </div>

        <br>


        <div>

            <label for="event_date">
                Event Date:
            </label>

            <br>

            <input
                type="date"
                id="event_date"
                name="event_date"
                value="<?php echo htmlspecialchars($eventDate); ?>"
                required
            >

        </div>

        <br>


        <div>

            <label for="event_time">
                Event Time:
            </label>

            <br>

            <input
                type="time"
                id="event_time"
                name="event_time"
                value="<?php echo htmlspecialchars($eventTime); ?>"
                required
            >

        </div>

        <br>


        <div>

            <label for="venue">
                Venue:
            </label>

            <br>

            <input
                type="text"
                id="venue"
                name="venue"
                value="<?php echo htmlspecialchars($venue); ?>"
                required
            >

        </div>

        <br>


        <div>

            <label for="capacity">
                Capacity:
            </label>

            <br>

            <input
                type="number"
                id="capacity"
                name="capacity"
                min="1"
                value="<?php echo htmlspecialchars($capacity); ?>"
                required
            >

        </div>

        <br>


        <div>

            <label for="status">
                Status:
            </label>

            <br>

            <select
                id="status"
                name="status"
            >

                <option
                    value="OPEN"
                    <?php echo $status === 'OPEN' ? 'selected' : ''; ?>
                >
                    OPEN
                </option>

                <option
                    value="CLOSED"
                    <?php echo $status === 'CLOSED' ? 'selected' : ''; ?>
                >
                    CLOSED
                </option>

                <option
                    value="CANCELLED"
                    <?php echo $status === 'CANCELLED' ? 'selected' : ''; ?>
                >
                    CANCELLED
                </option>

            </select>

        </div>

        <br>


        <button type="submit">
            Update Event
        </button>

    </form>

</body>

</html>