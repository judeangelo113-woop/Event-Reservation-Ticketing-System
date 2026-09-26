<?php

require_once __DIR__ . '/../classes/Reservation.php';
require_once __DIR__ . '/../classes/Event.php';
require_once __DIR__ . '/../classes/Attendee.php';

$reservationModel = new Reservation();
$eventModel = new Event();
$attendeeModel = new Attendee();

$reservationId = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

if ($reservationId <= 0) {
    die('Invalid reservation.');
}

$reservation = $reservationModel->getReservationById($reservationId);

if ($reservation === null) {
    die('Reservation not found.');
}

$events = $eventModel->getAllEvents();
$attendees = $attendeeModel->getAllAttendees();

$error = '';

$eventId = (int) $reservation['event_id'];
$attendeeId = (int) $reservation['attendee_id'];
$quantity = (int) $reservation['quantity'];
$status = $reservation['status'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $eventId = (int) ($_POST['event_id'] ?? 0);
    $attendeeId = (int) ($_POST['attendee_id'] ?? 0);
    $quantity = (int) ($_POST['quantity'] ?? 0);
    $status = trim($_POST['status'] ?? '');

    if ($eventId <= 0) {

        $error = 'Please select an event.';

    } elseif ($attendeeId <= 0) {

        $error = 'Please select an attendee.';

    } elseif ($quantity <= 0) {

        $error = 'Quantity must be greater than zero.';

    } elseif (!in_array($status, ['CONFIRMED', 'CANCELLED'], true)) {

        $error = 'Invalid reservation status.';

    } else {

        try {

            $reservationModel->setId($reservationId);
            $reservationModel->setEventId($eventId);
            $reservationModel->setAttendeeId($attendeeId);
            $reservationModel->setQuantity($quantity);
            $reservationModel->setStatus($status);

            $reservationModel->update();

            header(
                'Location: reservations.php?message=' .
                urlencode('Reservation updated successfully')
            );

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

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Update Reservation</title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

</head>

<body>

<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="container">

    <div class="form-header">

        <h1>Update Reservation</h1>

        <p>
            Update the event, attendee, quantity, or reservation status.
        </p>

    </div>

    <a
        href="reservations.php"
        class="btn btn-secondary back-button"
    >
        ← Back to Reservation Management
    </a>

    <?php if ($error !== ''): ?>

        <div class="error">

            <?= htmlspecialchars($error) ?>

        </div>

    <?php endif; ?>

    <div class="form-container">

        <form method="POST" action="">

            <input
                type="hidden"
                name="id"
                value="<?= (int) $reservationId ?>"
            >

            <div class="form-group">

                <label for="event_id">
                    Event
                </label>

                <select
                    name="event_id"
                    id="event_id"
                    required
                >

                    <option value="">
                        Select Event
                    </option>

                    <?php foreach ($events as $event): ?>

                        <option
                            value="<?= (int) $event['id'] ?>"
                            <?= $eventId === (int) $event['id']
                                ? 'selected'
                                : '' ?>
                        >

                            <?= htmlspecialchars($event['title']) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <div class="form-group">

                <label for="attendee_id">
                    Attendee
                </label>

                <select
                    name="attendee_id"
                    id="attendee_id"
                    required
                >

                    <option value="">
                        Select Attendee
                    </option>

                    <?php foreach ($attendees as $attendee): ?>

                        <option
                            value="<?= (int) $attendee['id'] ?>"
                            <?= $attendeeId === (int) $attendee['id']
                                ? 'selected'
                                : '' ?>
                        >

                            <?= htmlspecialchars(
                                $attendee['first_name'] .
                                ' ' .
                                $attendee['last_name']
                            ) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <div class="form-group">

                <label for="quantity">
                    Quantity
                </label>

                <input
                    type="number"
                    name="quantity"
                    id="quantity"
                    min="1"
                    value="<?= (int) $quantity ?>"
                    required
                >

            </div>

            <div class="form-group">

                <label for="status">
                    Status
                </label>

                <select
                    name="status"
                    id="status"
                    required
                >

                    <option
                        value="CONFIRMED"
                        <?= $status === 'CONFIRMED'
                            ? 'selected'
                            : '' ?>
                    >
                        CONFIRMED
                    </option>

                    <option
                        value="CANCELLED"
                        <?= $status === 'CANCELLED'
                            ? 'selected'
                            : '' ?>
                    >
                        CANCELLED
                    </option>

                </select>

            </div>

            <button
                type="submit"
                class="btn btn-primary"
            >
                Update Reservation
            </button>

            <a
                href="reservations.php"
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