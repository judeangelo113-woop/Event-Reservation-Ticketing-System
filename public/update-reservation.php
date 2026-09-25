<?php

require_once __DIR__ . '/../classes/Reservation.php';
require_once __DIR__ . '/../classes/Event.php';
require_once __DIR__ . '/../classes/Attendee.php';

$reservationModel = new Reservation();
$eventModel = new Event();
$attendeeModel = new Attendee();

$reservationId = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

if ($reservationId <= 0) {
    die("Invalid reservation.");
}

$reservation = $reservationModel->getReservationById($reservationId);

if ($reservation === null) {
    die("Reservation not found.");
}

$events = $eventModel->getAllEvents();
$attendees = $attendeeModel->getAllAttendees();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $eventId = (int) ($_POST['event_id'] ?? 0);
    $attendeeId = (int) ($_POST['attendee_id'] ?? 0);
    $quantity = (int) ($_POST['quantity'] ?? 0);
    $status = trim($_POST['status'] ?? '');

    if ($eventId <= 0) {
        $error = "Please select an event.";
    } elseif ($attendeeId <= 0) {
        $error = "Please select an attendee.";
    } elseif ($quantity <= 0) {
        $error = "Quantity must be greater than zero.";
    } elseif (!in_array($status, ['CONFIRMED', 'CANCELLED'], true)) {
        $error = "Invalid reservation status.";
    } else {

        try {

            $reservationModel->setId($reservationId);
            $reservationModel->setEventId($eventId);
            $reservationModel->setAttendeeId($attendeeId);
            $reservationModel->setQuantity($quantity);
            $reservationModel->setStatus($status);

            $reservationModel->update();

            header("Location: reservations.php?message=Reservation+updated+successfully");
            exit;

        } catch (Exception $e) {

            $error = $e->getMessage();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Update Reservation</title>
</head>

<body>

    <h1>Update Reservation</h1>

    <?php if ($error !== ''): ?>

        <p>
            <?= htmlspecialchars($error) ?>
        </p>

    <?php endif; ?>

    <form method="POST">

        <input
            type="hidden"
            name="id"
            value="<?= (int) $reservationId ?>"
        >

        <div>

            <label for="event_id">
                Event:
            </label>

            <select name="event_id" id="event_id" required>

                <?php foreach ($events as $event): ?>

                    <option
                        value="<?= (int) $event['id'] ?>"
                        <?= (int) $reservation['event_id'] === (int) $event['id'] ? 'selected' : '' ?>
                    >
                        <?= htmlspecialchars($event['title']) ?>
                    </option>

                <?php endforeach; ?>

            </select>

        </div>

        <br>

        <div>

            <label for="attendee_id">
                Attendee:
            </label>

            <select name="attendee_id" id="attendee_id" required>

                <?php foreach ($attendees as $attendee): ?>

                    <option
                        value="<?= (int) $attendee['id'] ?>"
                        <?= (int) $reservation['attendee_id'] === (int) $attendee['id'] ? 'selected' : '' ?>
                    >
                        <?= htmlspecialchars(
                            $attendee['first_name'] . ' ' . $attendee['last_name']
                        ) ?>
                    </option>

                <?php endforeach; ?>

            </select>

        </div>

        <br>

        <div>

            <label for="quantity">
                Quantity:
            </label>

            <input
                type="number"
                name="quantity"
                id="quantity"
                min="1"
                value="<?= (int) $reservation['quantity'] ?>"
                required
            >

        </div>

        <br>

        <div>

            <label for="status">
                Status:
            </label>

            <select name="status" id="status" required>

                <option
                    value="CONFIRMED"
                    <?= $reservation['status'] === 'CONFIRMED' ? 'selected' : '' ?>
                >
                    CONFIRMED
                </option>

                <option
                    value="CANCELLED"
                    <?= $reservation['status'] === 'CANCELLED' ? 'selected' : '' ?>
                >
                    CANCELLED
                </option>

            </select>

        </div>

        <br>

        <button type="submit">
            Update Reservation
        </button>

        <a href="reservations.php">
            Cancel
        </a>

    </form>

</body>

</html>