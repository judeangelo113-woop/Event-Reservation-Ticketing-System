
<?php

require_once __DIR__ . '/../includes/require_admin.php';
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

$error = '';

$eventId = 0;
$attendeeId = 0;
$quantity = 1;
$status = 'CONFIRMED';

try {

    $reservation = $reservationModel->getReservationById($reservationId);

    if ($reservation === null) {
        die('Reservation not found.');
    }

    $events = $eventModel->getAllEvents();
    $attendees = $attendeeModel->getAllAttendees();

    // INITIAL VALUES

    $eventId = (int) $reservation['event_id'];
    $attendeeId = (int) $reservation['attendee_id'];
    $quantity = (int) $reservation['quantity'];
    $status = $reservation['status'];

    // PROCESS FORM

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        if (!verifyCSRFToken($_POST['csrf_token'] ?? null)) {

            $error = 'Invalid or expired request. Please refresh the page and try again.';

        } else {

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

                $reservationModel->setId($reservationId);
                $reservationModel->setEventId($eventId);
                $reservationModel->setAttendeeId($attendeeId);
                $reservationModel->setQuantity($quantity);
                $reservationModel->setStatus($status);

                if ($reservationModel->update()) {

                    header(
                        'Location: ../public/reservations.php?message=' .
                        urlencode('Reservation updated successfully')
                    );

                    exit;

                } else {

                    $error = 'Failed to update the reservation.';
                }
            }
        }
    }

} catch (Exception $e) {

    $error = $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Update Reservation | EventlyPH</title>

    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="container reservation-form-page">

    <div class="reservation-form-header">
        <div>
            <span class="reservation-form-label">
                RESERVATION MANAGEMENT
            </span>

            <h1>Update Reservation</h1>

            <p>
                Modify the event, attendee, ticket quantity, or reservation status.
            </p>
        </div>
    </div>

    <a href="../public/reservations.php"
       class="reservation-form-back">
        &larr; Back to Reservations
    </a>

    <?php if ($error !== ''): ?>

        <div class="reservation-form-error">
            <strong>Unable to update reservation:</strong>
            <p><?= htmlspecialchars($error) ?></p>
        </div>

    <?php endif; ?>

    <div class="reservation-form-card">

        <div class="reservation-form-card-header">

            <div class="reservation-form-icon">&#9998;</div>

            <div>
                <h2>Reservation Information</h2>
                <p>Update the reservation details below.</p>
            </div>

        </div>

        <form method="POST" action="">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars(generateCSRFToken()) ?>"
            >

            <input
                type="hidden"
                name="id"
                value="<?= (int) $reservationId ?>"
            >

            <div class="reservation-form-grid">

                <div class="reservation-form-group">
                    <label for="event_id">Event <span>*</span></label>

                    <select name="event_id" id="event_id" required>

                        <option value="">Select Event</option>

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

                <div class="reservation-form-group">
                    <label for="attendee_id">Attendee <span>*</span></label>

                    <select name="attendee_id" id="attendee_id" required>

                        <option value="">Select Attendee</option>

                        <?php foreach ($attendees as $attendee): ?>

                            <option
                                value="<?= (int) $attendee['id'] ?>"
                                <?= $attendeeId === (int) $attendee['id']
                                    ? 'selected'
                                    : '' ?>
                            >
                                <?= htmlspecialchars(
                                    $attendee['first_name'] . ' ' .
                                    $attendee['last_name']
                                ) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>
                </div>

                <div class="reservation-form-group">
                    <label for="quantity">Ticket Quantity <span>*</span></label>

                    <input
                        type="number"
                        name="quantity"
                        id="quantity"
                        min="1"
                        value="<?= (int) $quantity ?>"
                        required
                    >
                </div>

                <div class="reservation-form-group">
                    <label for="status">Reservation Status <span>*</span></label>

                    <select name="status" id="status" required>

                        <option
                            value="CONFIRMED"
                            <?= $status === 'CONFIRMED' ? 'selected' : '' ?>
                        >
                            CONFIRMED
                        </option>

                        <option
                            value="CANCELLED"
                            <?= $status === 'CANCELLED' ? 'selected' : '' ?>
                        >
                            CANCELLED
                        </option>

                    </select>
                </div>

            </div>

            <p class="reservation-form-note">
                Fields marked with <span>*</span> are required.
            </p>

            <div class="reservation-form-actions">

                <a href="../public/reservations.php"
                   class="reservation-form-cancel">
                    Cancel
                </a>

                <button type="submit" class="reservation-form-submit">
                    Update Reservation
                </button>

            </div>

        </form>

    </div>

</main>

<script src="../assets/js/navigation.js"></script>

</body>
</html>
