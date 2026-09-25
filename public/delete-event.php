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

</head>

<body>

    <h1>Delete Event</h1>

    <a href="events.php">← Back to Event Management</a>

    <br><br>


    <?php if (!empty($errors)): ?>

        <div>

            <strong>Error:</strong>

            <ul>

                <?php foreach ($errors as $error): ?>

                    <li>
                        <?php echo htmlspecialchars($error); ?>
                    </li>

                <?php endforeach; ?>

            </ul>

        </div>

        <br>

    <?php endif; ?>


    <h2>Are you sure you want to delete this event?</h2>

    <p>
        <strong>Event ID:</strong>
        <?php echo $event['id']; ?>
    </p>

    <p>
        <strong>Title:</strong>
        <?php echo htmlspecialchars($event['title']); ?>
    </p>

    <p>
        <strong>Date:</strong>
        <?php echo htmlspecialchars($event['event_date']); ?>
    </p>

    <p>
        <strong>Time:</strong>
        <?php echo htmlspecialchars($event['event_time']); ?>
    </p>

    <p>
        <strong>Venue:</strong>
        <?php echo htmlspecialchars($event['venue']); ?>
    </p>

    <p>
        <strong>Capacity:</strong>
        <?php echo $event['capacity']; ?>
    </p>

    <p>
        <strong>Available Slots:</strong>
        <?php echo $event['available_slots']; ?>
    </p>

    <p>
        <strong>Status:</strong>
        <?php echo htmlspecialchars($event['status']); ?>
    </p>

    <p>
        <strong>Warning:</strong>
        Deleting this event may also delete its related reservations
        and tickets.
    </p>


    <form method="POST" action="">

        <input
            type="hidden"
            name="id"
            value="<?php echo $event['id']; ?>"
        >

        <button type="submit">
            Yes, Delete Event
        </button>

        <a href="events.php">
            Cancel
        </a>

    </form>

</body>

</html>