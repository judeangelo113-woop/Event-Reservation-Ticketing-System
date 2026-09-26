<?php

require_once __DIR__ . '/../classes/Event.php';
require_once __DIR__ . '/../classes/Reservation.php';

$eventModel = new Event();

$eventId = (int) ($_GET['event_id'] ?? 0);

if ($eventId <= 0) {
    die('Invalid event.');
}

$event = $eventModel->getEventById($eventId);

if ($event === null) {
    die('Event not found.');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $quantity = (int) ($_POST['quantity'] ?? 0);

    if ($firstName === '') {
        $error = 'First name is required.';
    } elseif ($lastName === '') {
        $error = 'Last name is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif ($quantity <= 0) {
        $error = 'Quantity must be at least 1.';
    } elseif ($quantity > (int) $event['available_slots']) {
        $error = 'The requested quantity exceeds the available slots.';
    } else {
        try {
            $reservation = new Reservation(
                $eventId,
                0,
                $quantity,
                '',
                'PENDING'
            );

            $reservationId = $reservation->createReservationTransaction(
                $firstName,
                $lastName,
                $email,
                $phone
            );

            header(
                'Location: reservation-success.php?id=' . $reservationId
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>Make Reservation</title>
</head>

<body>

<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

    <div class="container">

        <div class="form-container reservation-form">

            <div class="reservation-header">

                <h1>Make Reservation</h1>

                <p class="page-description">
                    Reserve your slots for the selected event.
                </p>

            </div>

    <div class="form-container">

        <div class="event-summary">

            <h2 class="reservation-event-title">
                <?= htmlspecialchars($event['title']) ?>
            </h2>

            <div class="event-details">

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
                    <strong>Available Slots:</strong>
                    <?= (int) $event['available_slots'] ?>
                </p>

            </div>

        </div>

        <?php if ($error !== ''): ?>

            <div class="error">
                <?= htmlspecialchars($error) ?>
            </div>

        <?php endif; ?>

        <form method="POST" action="">

            <div class="form-group">

                <label for="first_name">
                    First Name
                </label>

                <input
                    type="text"
                    id="first_name"
                    name="first_name"
                    value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>"
                    required
                >

            </div>

            <div class="form-group">

                <label for="last_name">
                    Last Name
                </label>

                <input
                    type="text"
                    id="last_name"
                    name="last_name"
                    value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>"
                    required
                >

            </div>

            <div class="form-group">

                <label for="email">
                    Email
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                    required
                >

            </div>

            <div class="form-group">

                <label for="phone">
                    Phone
                </label>

                <input
                    type="text"
                    id="phone"
                    name="phone"
                    value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
                >

            </div>

            <div class="form-group">

                <label for="quantity">
                    Number of Slots
                </label>

                <input
                    type="number"
                    id="quantity"
                    name="quantity"
                    min="1"
                    max="<?= (int) $event['available_slots'] ?>"
                    value="<?= htmlspecialchars($_POST['quantity'] ?? '1') ?>"
                    required
                >

            </div>

            <div class="form-actions">

                <a href="events.php" class="btn btn-secondary">
                    Back to Events
                </a>

                <button type="submit" class="btn btn-primary">
                    Reserve Now
                </button>

            </div>

        </form>

    </div>

</div>

<script src="../assets/js/navigation.js"></script>

</body>

</html>