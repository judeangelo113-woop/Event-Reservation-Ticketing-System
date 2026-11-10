<?php

require_once __DIR__ . '/../includes/require_login.php';
require_once __DIR__ . '/../classes/Event.php';
require_once __DIR__ . '/../classes/Reservation.php';
require_once __DIR__ . '/../config/Database.php';

// Only attendees can reserve tickets.
if (!isAttendee()) {
    header('Location: dashboard.php');
    exit;
}

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
$attendee = null;

try {
    $database = new Database();
    $pdo = $database->connect();

    $stmt = $pdo->prepare(
        "SELECT first_name, last_name, email, phone
         FROM attendees
         WHERE id = :attendee_id
         LIMIT 1"
    );

    $stmt->execute([
        ':attendee_id' => $_SESSION['attendee_id']
    ]);

    $attendee = $stmt->fetch();

    if (!$attendee) {
        die('Attendee account not found.');
    }

} catch (PDOException $e) {
    die('Unable to load attendee information.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!verifyCSRFToken($_POST['csrf_token'] ?? null)) {

        $error = 'Invalid request. Please try again.';

    } else {

        $quantity = (int) ($_POST['quantity'] ?? 0);

        if ($quantity <= 0) {

            $error = 'Please enter a valid ticket quantity.';

        } elseif ($quantity > (int) $event['available_slots']) {

            $error = 'Not enough available slots.';

        } else {

            try {

                $reservation = new Reservation(
                    $eventId,
                    (int) $_SESSION['attendee_id'],
                    $quantity,
                    '',
                    'PENDING'
                );

                $reservationId = $reservation->createReservationTransaction(
                    $attendee['first_name'],
                    $attendee['last_name'],
                    $attendee['email'],
                    $attendee['phone'] ?? '',
                    (int) $_SESSION['attendee_id']
                );

                header('Location: reservation-success.php?id=' . $reservationId);
                exit;

            } catch (Exception $e) {

                $error = $e->getMessage();

            }
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

        <input type="hidden" name="csrf_token"
            value="<?= htmlspecialchars(generateCSRFToken()) ?>">

            <div class="form-group">

                <label for="first_name">
                    First Name
                </label>


            </div>

            <div class="form-group">

                <label for="last_name">
                    Last Name
                </label>


            </div>

            <div class="form-group">

                <label for="email">
                    Email
                </label>

            </div>

            <div class="form-group">

                <label for="phone">
                    Phone
                </label>

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