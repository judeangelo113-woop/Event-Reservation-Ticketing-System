<?php

require_once __DIR__ . '/../classes/Reservation.php';

$reservationModel = new Reservation();

$reservationId = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

if ($reservationId <= 0) {
    die("Invalid reservation.");
}

$reservation = $reservationModel->getReservationById($reservationId);

if ($reservation === null) {
    die("Reservation not found.");
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {

        $reservationModel->setId($reservationId);

        $reservationModel->delete();

        header(
            "Location: reservations.php?message=Reservation+deleted+successfully"
        );

        exit;

    } catch (Exception $e) {

        $error = $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Delete Reservation</title>
</head>

<body>

    <h1>Delete Reservation</h1>

    <?php if ($error !== ''): ?>

        <p>
            <?= htmlspecialchars($error) ?>
        </p>

    <?php endif; ?>

    <p>
        Are you sure you want to delete this reservation?
    </p>

    <p>
        <strong>Event:</strong>
        <?= htmlspecialchars($reservation['event_title']) ?>
    </p>

    <p>
        <strong>Attendee:</strong>
        <?= htmlspecialchars($reservation['attendee_name']) ?>
    </p>

    <p>
        <strong>Quantity:</strong>
        <?= (int) $reservation['quantity'] ?>
    </p>

    <p>
        <strong>Status:</strong>
        <?= htmlspecialchars($reservation['status']) ?>
    </p>

    <form method="POST">

        <input
            type="hidden"
            name="id"
            value="<?= (int) $reservationId ?>"
        >

        <button type="submit">
            Yes, Delete Reservation
        </button>

        <a href="reservations.php">
            Cancel
        </a>

    </form>

</body>

</html>