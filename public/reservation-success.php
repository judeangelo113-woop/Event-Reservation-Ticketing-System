
<?php

require_once __DIR__ . '/../classes/Reservation.php';
require_once __DIR__ . '/../config/Database.php';

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

    // Get all tickets belonging to this reservation.
    $database = new Database();
    $db = $database->connect();

    $sql = "SELECT ticket_number, status, issued_at
            FROM tickets
            WHERE reservation_id = :reservation_id
            ORDER BY id ASC";

    $stmt = $db->prepare($sql);

    $stmt->execute([
        ':reservation_id' => $reservationId
    ]);

    $tickets = $stmt->fetchAll();

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

            <?php if (!empty($tickets)): ?>

                <?php foreach ($tickets as $index => $ticket): ?>

                    <div class="details-list">

                        <h3>Ticket <?= $index + 1 ?></h3>

                        <p>
                            <strong>Ticket Number:</strong>
                            <?= htmlspecialchars($ticket['ticket_number']) ?>
                        </p>

                        <p>
                            <strong>Ticket Status:</strong>
                            <span class="status-badge status-open">
                                <?= htmlspecialchars($ticket['status']) ?>
                            </span>
                        </p>

                        <p>
                            <strong>Issued At:</strong>
                            <?= htmlspecialchars($ticket['issued_at']) ?>
                        </p>

                    </div>

                    <?php if ($index < count($tickets) - 1): ?>
                        <hr>
                    <?php endif; ?>

                <?php endforeach; ?>

            <?php else: ?>

                <p>No tickets found for this reservation.</p>

            <?php endif; ?>

        </div>

        <div class="success-actions">

            <a href="browse-events.php" class="btn btn-secondary">
                Back to Events
            </a>

            <a href="my-reservations.php?id=<?= (int) $reservation['id'] ?>" class="btn btn-primary">
                View Reservation
            </a>

            <a href="my-tickets.php?reservation_id=<?= (int) $reservation['id'] ?>" class="btn btn-primary">
                View Tickets
            </a>

        </div>

    </div>

</div>

<script src="../assets/js/navigation.js"></script>

</body>

</html>
