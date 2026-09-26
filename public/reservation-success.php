<?php

require_once __DIR__ . '/../classes/Reservation.php';

$reservationId = (int) ($_GET['id'] ?? 0);

if ($reservationId <= 0) {
    die('Invalid reservation.');
}

try {
    $reservationModel = new Reservation();

    $reservation = $reservationModel->getReservationById($reservationId);

    if ($reservation === null) {
        die('Reservation not found.');
    }
} catch (Exception $e) {
    die('Unable to load reservation.');
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

    <link rel="stylesheet" href="../assets/css/style.css">

    <title>Reservation Successful</title>

</head>

<body>

<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<div class="container">

    <div class="form-container reservation-success">

        <div class="success-header">

            <div class="success-icon">
                ✓
            </div>

            <h1>Reservation Successful!</h1>

            <p class="page-description">
                Your reservation has been successfully created.
            </p>

        </div>

        <div class="success-section">

            <h2>Reservation Details</h2>

            <div class="details-list">

                <p>
                    <strong>Reservation ID:</strong>
                    <?= (int) $reservation['id'] ?>
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
                    <strong>Email:</strong>
                    <?= htmlspecialchars($reservation['attendee_email']) ?>
                </p>

                <p>
                    <strong>Quantity:</strong>
                    <?= (int) $reservation['quantity'] ?>
                </p>

                <p>
                    <strong>Status:</strong>
                    <span class="status-badge status-open">
                        <?= htmlspecialchars($reservation['status']) ?>
                    </span>
                </p>

                <p>
                    <strong>Reservation Date:</strong>
                    <?= htmlspecialchars($reservation['reservation_date']) ?>
                </p>

            </div>

        </div>

        <div class="success-section">

            <h2>Ticket Details</h2>

            <div class="details-list">

                <p>
                    <strong>Ticket Number:</strong>
                    <?= htmlspecialchars($reservation['ticket_number']) ?>
                </p>

                <p>
                    <strong>Ticket Status:</strong>
                    <span class="status-badge status-open">
                        <?= htmlspecialchars($reservation['ticket_status']) ?>
                    </span>
                </p>

                <p>
                    <strong>Issued At:</strong>
                    <?= htmlspecialchars($reservation['ticket_issued_at']) ?>
                </p>

            </div>

        </div>

        <div class="success-actions">

            <a href="events.php" class="btn btn-secondary">
                Back to Events
            </a>

            <a href="reservations.php" class="btn btn-primary">
                View Reservations
            </a>

            <a href="tickets.php" class="btn btn-primary">
                View Tickets
            </a>

        </div>

    </div>

</div>

<script src="../assets/js/navigation.js"></script>

</body>

</html>