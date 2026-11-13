
<?php

require_once __DIR__ . '/../includes/require_login.php';
require_once __DIR__ . '/../config/Database.php';

if (!isAttendee()) {
    header('Location: dashboard.php');
    exit;
}

$reservationId = (int) ($_GET['reservation_id'] ?? 0);
$attendeeId = (int) $_SESSION['attendee_id'];

$tickets = [];

try {

    $database = new Database();
    $db = $database->connect();

    $sql = "
        SELECT
            t.id,
            t.ticket_number,
            t.issued_at,
            t.status AS ticket_status,
            r.id AS reservation_id,
            r.quantity,
            e.title AS event_title,
            e.event_date,
            e.event_time,
            e.venue
        FROM tickets t
        INNER JOIN reservations r
            ON t.reservation_id = r.id
        INNER JOIN events e
            ON r.event_id = e.id
        WHERE r.attendee_id = :attendee_id
    ";

    $params = [
        ':attendee_id' => $attendeeId
    ];

    if ($reservationId > 0) {
        $sql .= " AND r.id = :reservation_id";
        $params[':reservation_id'] = $reservationId;
    }

    $sql .= " ORDER BY t.id DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);

    $tickets = $stmt->fetchAll();

} catch (Exception $e) {

    die('Unable to load tickets.');

}

$ticketCount = count($tickets);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Tickets - EventlyPH</title>

    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<div class="my-tickets-container">

    <!-- Page Header -->
    <div class="my-tickets-header">

        <div>
            <span class="my-tickets-label">YOUR EVENT PASSES</span>

            <h1>My Tickets</h1>

            <p>
                Access your event tickets and keep your ticket
                information ready for event day.
            </p>
        </div>

        <div class="my-tickets-header-icon">
            🎟
        </div>

    </div>

    <!-- Ticket Summary -->
    <?php if (!empty($tickets)): ?>

        <div class="my-tickets-summary">

            <div class="my-tickets-summary-icon">
                🎫
            </div>

            <div>
                <strong><?= $ticketCount ?></strong>

                <span>
                    <?= $ticketCount === 1 ? 'Ticket Available' : 'Tickets Available' ?>
                </span>
            </div>

            <button
                type="button"
                class="print-all-tickets-button"
                onclick="window.print()">
                🖨 Print Tickets
            </button>

        </div>

    <?php endif; ?>

    <!-- Empty State -->
    <?php if (empty($tickets)): ?>

        <div class="my-tickets-empty">

            <div class="my-tickets-empty-icon">
                🎟
            </div>

            <h2>No Tickets Found</h2>

            <p>
                <?php if ($reservationId > 0): ?>
                    We couldn't find any tickets for this reservation.
                <?php else: ?>
                    You don't have any tickets yet. Make a reservation
                    for an available event to get your tickets.
                <?php endif; ?>
            </p>

            <a href="browse-events.php" class="my-tickets-browse-button">
                Browse Events
                <span>→</span>
            </a>

        </div>

    <?php else: ?>

        <!-- Tickets List -->
        <div class="my-tickets-list">

            <?php foreach ($tickets as $ticket): ?>

                <?php

                    $status = strtoupper(trim($ticket['ticket_status'] ?? 'ISSUED'));

                    $statusClass = match ($status) {
                        'ISSUED', 'VALID', 'ACTIVE' => 'ticket-status-valid',
                        'USED', 'CHECKED_IN' => 'ticket-status-used',
                        'CANCELLED', 'VOID' => 'ticket-status-cancelled',
                        default => 'ticket-status-default'
                    };

                    $eventDate = !empty($ticket['event_date'])
                        ? strtotime($ticket['event_date'])
                        : false;

                    $eventTime = !empty($ticket['event_time'])
                        ? strtotime($ticket['event_time'])
                        : false;

                    $issuedDate = !empty($ticket['issued_at'])
                        ? strtotime($ticket['issued_at'])
                        : false;

                ?>

                <div class="digital-ticket">

                    <!-- Main Ticket Section -->
                    <div class="digital-ticket-main">

                        <div class="digital-ticket-top">

                            <span class="digital-ticket-brand">
                                EVENTLYPH
                            </span>

                            <span class="digital-ticket-type">
                                EVENT TICKET
                            </span>

                        </div>

                        <div class="digital-ticket-event">

                            <span class="digital-ticket-label">
                                YOU'RE GOING TO
                            </span>

                            <h2>
                                <?= htmlspecialchars($ticket['event_title']) ?>
                            </h2>

                        </div>

                        <div class="digital-ticket-details">

                            <div>
                                <small>DATE</small>

                                <strong>
                                    <?= $eventDate !== false
                                        ? date('M d, Y', $eventDate)
                                        : 'Not specified' ?>
                                </strong>
                            </div>

                            <div>
                                <small>TIME</small>

                                <strong>
                                    <?= $eventTime !== false
                                        ? date('h:i A', $eventTime)
                                        : 'Not specified' ?>
                                </strong>
                            </div>

                            <div class="digital-ticket-venue">
                                <small>VENUE</small>

                                <strong>
                                    <?= htmlspecialchars($ticket['venue']) ?>
                                </strong>
                            </div>

                        </div>

                        <div class="digital-ticket-number-section">

                            <div>
                                <small>TICKET NUMBER</small>

                                <strong>
                                    <?= htmlspecialchars($ticket['ticket_number']) ?>
                                </strong>
                            </div>

                            <span class="ticket-status <?= $statusClass ?>">
                                <?= htmlspecialchars($status) ?>
                            </span>

                        </div>

                    </div>

                    <!-- Ticket Stub -->
                    <div class="digital-ticket-stub">

                        <div class="ticket-stub-decoration">
                            🎟
                        </div>

                        <span class="ticket-stub-label">
                            RESERVATION
                        </span>

                        <strong class="ticket-stub-id">
                            #<?= (int) $ticket['reservation_id'] ?>
                        </strong>

                        <div class="ticket-stub-divider"></div>

                        <span class="ticket-stub-label">
                            TICKET ID
                        </span>

                        <strong class="ticket-stub-id">
                            #<?= (int) $ticket['id'] ?>
                        </strong>

                        <div class="ticket-stub-issued">

                            <small>ISSUED ON</small>

                            <strong>
                                <?= $issuedDate !== false
                                    ? date('M d, Y', $issuedDate)
                                    : 'Not available' ?>
                            </strong>

                        </div>

                    </div>

                    <!-- Ticket Actions -->
                    <div class="digital-ticket-actions">

                        <a
                            href="my-reservations.php?id=<?= (int) $ticket['reservation_id'] ?>"
                            class="ticket-reservation-button">
                            View Reservation
                        </a>

                        <button
                            type="button"
                            class="ticket-print-button"
                            onclick="window.print()">
                            🖨 Print
                        </button>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

</div>

<script src="../assets/js/navigation.js"></script>

</body>
</html>
