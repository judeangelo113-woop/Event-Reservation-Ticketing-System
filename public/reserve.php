<?php

require_once __DIR__ . '/../classes/Event.php';
require_once __DIR__ . '/../classes/Reservation.php';

$eventModel = new Event();

$eventId = (int) ($_GET['event_id'] ?? 0);

if ($eventId <= 0) {
    die("Invalid event.");
}

$event = $eventModel->getEventById($eventId);

if ($event === null) {
    die("Event not found.");
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $quantity = (int) ($_POST['quantity'] ?? 0);

    if ($firstName === '') {

        $error = "First name is required.";

    } elseif ($lastName === '') {

        $error = "Last name is required.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } elseif ($quantity <= 0) {

        $error = "Quantity must be at least 1.";

    } elseif ($quantity > (int) $event['available_slots']) {

        $error = "The requested quantity exceeds the available slots.";

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
                "Location: reservation-success.php?id=" . $reservationId
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

    <title>Make Reservation</title>

</head>

<body>

    <h1>Make Reservation</h1>

    <h2>
        <?php echo htmlspecialchars($event['title']); ?>
    </h2>

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
        <strong>Available Slots:</strong>
        <?php echo $event['available_slots']; ?>
    </p>

    <?php if ($error !== ''): ?>

        <p>
            <strong>Error:</strong>
            <?php echo htmlspecialchars($error); ?>
        </p>

    <?php endif; ?>

    <form method="POST" action="">

        <div>

            <label for="first_name">
                First Name:
            </label>

            <br>

            <input
                type="text"
                id="first_name"
                name="first_name"
                value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>"
                required
            >

        </div>

        <br>

        <div>

            <label for="last_name">
                Last Name:
            </label>

            <br>

            <input
                type="text"
                id="last_name"
                name="last_name"
                value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>"
                required
            >

        </div>

        <br>

        <div>

            <label for="email">
                Email:
            </label>

            <br>

            <input
                type="email"
                id="email"
                name="email"
                value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                required
            >

        </div>

        <br>

        <div>

            <label for="phone">
                Phone:
            </label>

            <br>

            <input
                type="text"
                id="phone"
                name="phone"
                value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
            >

        </div>

        <br>

        <div>

            <label for="quantity">
                Number of Slots:
            </label>

            <br>

            <input
                type="number"
                id="quantity"
                name="quantity"
                min="1"
                max="<?php echo $event['available_slots']; ?>"
                value="<?php echo htmlspecialchars($_POST['quantity'] ?? '1'); ?>"
                required
            >

        </div>

        <br>

        <button type="submit">
            Reserve Now
        </button>

    </form>

    <br>

    <a href="events.php">
        Back to Events
    </a>

</body>

</html>