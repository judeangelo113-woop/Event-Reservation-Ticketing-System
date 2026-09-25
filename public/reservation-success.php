<?php

require_once __DIR__ . '/../classes/Reservation.php';

$reservationId = (int) ($_GET['id'] ?? 0);

if ($reservationId <= 0) {
    die("Invalid reservation.");
}

try {

    $reservationModel = new Reservation();

    $reservation = $reservationModel->getReservationById($reservationId);

    if ($reservation === null) {
        die("Reservation not found.");
    }

} catch (Exception $e) {

    die("Unable to load reservation.");

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

    <title>Reservation Successful</title>

</head>

<body>

    <h1>Reservation Successful!</h1>

    <p>
        Your reservation has been successfully created.
    </p>

    <hr>

    <h2>Reservation Details</h2>

    <p>
        <strong>Reservation ID:</strong>
        <?php echo $reservation['id']; ?>
    </p>

    <p>
        <strong>Event:</strong>
        <?php echo htmlspecialchars($reservation['event_title']); ?>
    </p>

    <p>
        <strong>Attendee:</strong>
        <?php echo htmlspecialchars($reservation['attendee_name']); ?>
    </p>

    <p>
        <strong>Email:</strong>
        <?php echo htmlspecialchars($reservation['attendee_email']); ?>
    </p>

    <p>
        <strong>Quantity:</strong>
        <?php echo $reservation['quantity']; ?>
    </p>

    <p>
        <strong>Status:</strong>
        <?php echo htmlspecialchars($reservation['status']); ?>
    </p>

    <p>
        <strong>Reservation Date:</strong>
        <?php echo htmlspecialchars($reservation['reservation_date']); ?>
    </p>

    <hr>

<h2>Ticket Details</h2>

<p>
    <strong>Ticket Number:</strong>
    <?php echo htmlspecialchars($reservation['ticket_number']); ?>
</p>

<p>
    <strong>Ticket Status:</strong>
    <?php echo htmlspecialchars($reservation['ticket_status']); ?>
</p>

<p>
    <strong>Issued At:</strong>
    <?php echo htmlspecialchars($reservation['ticket_issued_at']); ?>
</p>

    <br>

    <a href="events.php">
        Back to Events
    </a>

</body>

</html>