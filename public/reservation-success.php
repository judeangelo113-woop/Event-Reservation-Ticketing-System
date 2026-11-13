
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

// Format reservation date.
$reservationDate = !empty($reservation['reservation_date'])
    ? strtotime($reservation['reservation_date'])
    : false;

// Status badge helper.
function getReservationStatusClass($status)
{
    $status = strtoupper(trim($status));

    switch ($status) {
        case 'CONFIRMED':
        case 'APPROVED':
            return 'success-status-confirmed';

        case 'PENDING':
            return 'success-status-pending';

        case 'CANCELLED':
        case 'REJECTED':
            return 'success-status-cancelled';

        default:
            return 'success-status-default';
    }
}

function getTicketStatusClass($status)
{
    $status = strtoupper(trim($status));

    switch ($status) {
        case 'ISSUED':
        case 'VALID':
        case 'ACTIVE':
            return 'success-status-confirmed';

        case 'USED':
        case 'CHECKED_IN':
            return 'success-status-used';

        case 'CANCELLED':
        case 'VOID':
            return 'success-status-cancelled';

        default:
            return 'success-status-default';
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Reservation Successful - EventlyPH</title>

    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<div class="reservation-success-page">

    <!-- Success Header -->
    <section class="reservation-success-hero">

        <div class="reservation-success-check">
            ✓
        </div>

        <span class="reservation-success-label">
            RESERVATION RECEIVED
        </span>

        <h1>Reservation Successful!</h1>

        <p>
            Your reservation has been successfully created.
            You can review your reservation and ticket information below.
        </p>

        <div class="reservation-success-reference">
            <span>RESERVATION REFERENCE</span>
            <strong>#<?= (int) $reservation['id'] ?></strong>
        </div>

    </section>

    <!-- Main Content -->
    <div class="reservation-success-content">

        <!-- Reservation Summary -->
        <section class="reservation-success-card">

            <div class="reservation-success-card-header">

                <div>
                    <span class="reservation-success-section-label">
                        RESERVATION SUMMARY
                    </span>

                    <h2>Reservation Details</h2>
                </div>

                <span class="reservation-success-card-icon">
                    📋
                </span>

            </div>

            <div class="reservation-success-event">

                <div class="reservation-success-event-icon">
                    🎉
                </div>

                <div>
                    <small>EVENT</small>

                    <h3>
                        <?= htmlspecialchars($reservation['event_title']) ?>
                    </h3>
                </div>

            </div>

            <div class="reservation-success-info-grid">

                <div class="reservation-success-info-item">
                    <span>Reservation ID</span>
                    <strong>#<?= (int) $reservation['id'] ?></strong>
                </div>

                <div class="reservation-success-info-item">
                    <span>Attendee</span>
                    <strong>
                        <?= htmlspecialchars($reservation['attendee_name']) ?>
                    </strong>
                </div>

                <div class="reservation-success-info-item">
                    <span>Email Address</span>
                    <strong>
                        <?= htmlspecialchars($reservation['attendee_email']) ?>
                    </strong>
                </div>

                <div class="reservation-success-info-item">
                    <span>Number of Slots</span>
                    <strong>
                        <?= (int) $reservation['quantity'] ?>
                        <?= (int) $reservation['quantity'] === 1 ? 'Ticket' : 'Tickets' ?>
                    </strong>
                </div>

                <div class="reservation-success-info-item">
                    <span>Reservation Date</span>
                    <strong>
                        <?= $reservationDate !== false
                            ? date('F d, Y h:i A', $reservationDate)
                            : htmlspecialchars($reservation['reservation_date']) ?>
                    </strong>
                </div>

                <div class="reservation-success-info-item">
                    <span>Reservation Status</span>

                    <strong>
                        <span class="reservation-success-status <?= getReservationStatusClass($reservation['status']) ?>">
                            <?= htmlspecialchars($reservation['status']) ?>
                        </span>
                    </strong>
                </div>

            </div>

        </section>

        <!-- Ticket Details -->
        <section class="reservation-success-card">

            <div class="reservation-success-card-header">

                <div>
                    <span class="reservation-success-section-label">
                        YOUR ADMISSION
                    </span>

                    <h2>Ticket Details</h2>

                    <p>
                        Your generated tickets are listed below.
                    </p>
                </div>

                <span class="reservation-success-card-icon">
                    🎟️
                </span>

            </div>

            <?php if (!empty($tickets)): ?>

                <div class="reservation-success-ticket-list">

                    <?php foreach ($tickets as $index => $ticket): ?>

                        <div class="reservation-success-ticket">

                            <div class="reservation-success-ticket-top">

                                <div class="reservation-success-ticket-number">
                                    <span>TICKET</span>
                                    <strong>
                                        <?= str_pad($index + 1, 2, '0', STR_PAD_LEFT) ?>
                                    </strong>
                                </div>

                                <span class="reservation-success-status <?= getTicketStatusClass($ticket['status']) ?>">
                                    <?= htmlspecialchars($ticket['status']) ?>
                                </span>

                            </div>

                            <div class="reservation-success-ticket-divider"></div>

                            <div class="reservation-success-ticket-details">

                                <div>
                                    <span>Ticket Number</span>

                                    <strong>
                                        <?= htmlspecialchars($ticket['ticket_number']) ?>
                                    </strong>
                                </div>

                                <div>
                                    <span>Issued Date</span>

                                    <strong>
                                        <?php
                                        $issuedDate = !empty($ticket['issued_at'])
                                            ? strtotime($ticket['issued_at'])
                                            : false;
                                        ?>

                                        <?= $issuedDate !== false
                                            ? date('M d, Y h:i A', $issuedDate)
                                            : 'Not available' ?>
                                    </strong>
                                </div>

                            </div>

                            <div class="reservation-success-ticket-footer">
                                <span>EVENTLYPH</span>
                                <span>OFFICIAL EVENT TICKET</span>
                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            <?php else: ?>

                <div class="reservation-success-empty-tickets">

                    <span>🎟️</span>

                    <h3>No Tickets Found</h3>

                    <p>
                        No tickets are currently associated with this reservation.
                    </p>

                </div>

            <?php endif; ?>

        </section>

        <!-- Important Notice -->
        <div class="reservation-success-notice">

            <span>ℹ️</span>

            <div>
                <strong>Important Reminder</strong>

                <p>
                    Keep your ticket number for reference. Your reservation
                    status is displayed above, so check it before attending
                    the event.
                </p>
            </div>

        </div>

        <!-- Action Buttons -->
        <div class="reservation-success-actions">

            <a href="browse-events.php" class="reservation-success-back">
                ← Browse More Events
            </a>

            <a
                href="my-reservations.php?id=<?= (int) $reservation['id'] ?>"
                class="reservation-success-view-reservation"
            >
                View Reservation
            </a>

            <a
                href="my-tickets.php?reservation_id=<?= (int) $reservation['id'] ?>"
                class="reservation-success-view-tickets"
            >
                View My Tickets →
            </a>

        </div>

    </div>

</div>

<script src="../assets/js/navigation.js"></script>

</body>
</html>
