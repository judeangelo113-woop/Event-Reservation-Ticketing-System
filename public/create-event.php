<?php

require_once __DIR__ . '/../classes/Event.php';

$errors = [];

$title = '';
$description = '';
$eventDate = '';
$eventTime = '';
$venue = '';
$capacity = '';
$status = 'OPEN';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $eventDate = trim($_POST['event_date'] ?? '');
    $eventTime = trim($_POST['event_time'] ?? '');
    $venue = trim($_POST['venue'] ?? '');
    $capacity = trim($_POST['capacity'] ?? '');
    $status = trim($_POST['status'] ?? 'OPEN');



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

    

    if (empty($errors)) {

        try {

            $event = new Event(
                $title,
                $description,
                $eventDate,
                $eventTime,
                $venue,
                (int)$capacity,
                (int)$capacity,
                $status
            );

            if ($event->create()) {

                header('Location: events.php');
                exit;

            } else {

                $errors[] = 'Failed to create the event.';
            }

        } catch (Exception $e) {

            $errors[] = 'An error occurred while creating the event.';
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

    <link rel="stylesheet" href="../assets/css/style.css">

    <title>Create Event</title>

</head>

<body>

    <?php
        require_once __DIR__ . '/../includes/navbar.php'; 
    ?>

    <div class="container">

        <div class="form-header">
                <h1>Create New Event</h1>
        </div>

    <div class="form-container">
    
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

            <div class="form-group">

            <label for="title">
                Event Title
            </label>

            <input
                type="text"
                name="title"
                id="title"
                value="<?= htmlspecialchars($title) ?>"
                required
            >

            </div>
        
        <br>
            <div class="form-group">

                <label for="description">
                    Description
                </label>

                <textarea
                    name="description"
                    id="description"
                >

                <?= htmlspecialchars($description) ?>
            
                </textarea>

            </div>

            <br>


            <div class="form-group">

                <label for="capacity">
                    Capacity
                </label>

                <input
                    type="number"
                    name="capacity"
                    id="capacity"
                    min="1"
                    required
                >

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

            <button type="submit" class="btn btn-primary">
                Create Event
            </button>

            <a href="events.php" class="btn btn-secondary">
                Cancel
            </a>

        </form>

    </div>

</div>

    <script src="../assets/js/navigation.js"></script>

</body>

</html>