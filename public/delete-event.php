<?php

require_once __DIR__ . '/../classes/Event.php';

$errors = [];


$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id === false || $id === null || $id <= 0) {
    die('Invalid event ID.');
}

try {

    $eventModel = new Event();

    
    //check event if exists
    $event = $eventModel->getEventById($id);

    if ($event === null) {
        die('Event not found.');
    }

    
    //process of delete
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        
        $postedId = filter_input(
            INPUT_POST,
            'id',
            FILTER_VALIDATE_INT
        );

        if ($postedId === false || $postedId === null || $postedId !== $id) {

            $errors[] = 'Invalid event ID.';

        } else {

            $eventModel->setId($id);

            if ($eventModel->delete()) {

                header('Location: events.php');
                exit;

            } else {

                $errors[] = 'Failed to delete the event.';
            }
        }
    }

} catch (Exception $e) {

    $errors[] = 'An error occurred while deleting the event.';
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

    <title>Delete Event</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="container">

    <div class="form-header">

        <h1>Delete Event</h1>

        <p>
            Review the event information before permanently deleting it.
        </p>

    </div>


    <a href="events.php" class="btn btn-secondary back-button">
        ← Back to Event Management
    </a>


    <?php if (!empty($errors)): ?>

        <div class="error">

            <strong>Error:</strong>

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

        <h2>Are you sure you want to delete this event?</h2>

        <p>
            This action cannot be undone.
        </p>


        <div class="delete-details">

            <p>
                <strong>Event ID:</strong>
                <?= (int) $event['id'] ?>
            </p>

            <p>
                <strong>Title:</strong>
                <?= htmlspecialchars($event['title']) ?>
            </p>

            <p>
                <strong>Date:</strong>
                <?= htmlspecialchars($event['event_date']) ?>
            </p>

            <p>
                <strong>Time:</strong>
                <?= htmlspecialchars($event['event_time']) ?>
            </p>

            <p>
                <strong>Venue:</strong>
                <?= htmlspecialchars($event['venue']) ?>
            </p>

            <p>
                <strong>Capacity:</strong>
                <?= (int) $event['capacity'] ?>
            </p>

            <p>
                <strong>Available Slots:</strong>
                <?= (int) $event['available_slots'] ?>
            </p>

            <p>
                <strong>Status:</strong>
                <?= htmlspecialchars($event['status']) ?>
            </p>

        </div>


        <div class="error">

            <strong>Warning:</strong>

            Deleting this event may also delete its related
            reservations and tickets.

        </div>


        <br>


        <form method="POST" action="">

            <input
                type="hidden"
                name="id"
                value="<?= (int) $event['id'] ?>"
            >

            <button
                type="submit"
                class="btn btn-danger"
            >
                Yes, Delete Event
            </button>

            <a
                href="events.php"
                class="btn btn-secondary"
            >
                Cancel
            </a>

        </form>

    </div>

</main>

<script src="../assets/js/navigation.js"></script>

</body>

</html>