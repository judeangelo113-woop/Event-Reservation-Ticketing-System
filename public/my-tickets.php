
<?php

require_once __DIR__ . '/../includes/require_login.php';
require_once __DIR__ . '/../config/Database.php';

if (!isAttendee()) {
    header('Location: dashboard.php');
    exit;
}

$reservationId = (int) ($_GET['reservation_id'] ?? 0);
$attendeeId = (int) $_SESSION['attendee_id'];

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

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>My Tickets</title>
</head>

<body>

<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<div class="container">

    <div class="form-container">

        <h1>My Tickets</h1>

        <p class="page-description">
            View all your event tickets.
        </p>

        <?php if (empty($tickets)): ?>

            <p>You don't have any tickets yet.</p>

            <a href="browse-events.php" class="btn btn-primary">
                Browse Events
            </a>

        <?php else: ?>

            <?php foreach ($tickets as $ticket): ?>

                <div class="success-section">

                    <h2><?= htmlspecialchars($ticket['event_title']) ?></h2>

                    <div class="details-list">

                        <p>
                            <strong>Ticket Number:</strong>
                            <?= htmlspecialchars($ticket['ticket_number']) ?>
                        </p>

                        <p>
                            <strong>Reservation ID:</strong>
                            <?= (int) $ticket['reservation_id'] ?>
                        </p>

                        <p>
                            <strong>Event Date:</strong>
                            <?= htmlspecialchars($ticket['event_date']) ?>
                        </p>

                        <p>
                            <strong>Event Time:</strong>
                            <?= htmlspecialchars($ticket['event_time']) ?>
                        </p>

                        <p>
                            <strong>Venue:</strong>
                            <?= htmlspecialchars($ticket['venue']) ?>
                        </p>

                        <p>
                            <strong>Ticket Status:</strong>
                            <?= htmlspecialchars($ticket['ticket_status']) ?>
                        </p>

                        <p>
                            <strong>Issued At:</strong>
                            <?= htmlspecialchars($ticket['issued_at']) ?>
                        </p>

                    </div>

                    <div class="success-actions">

                        <a
                            href="my-reservations.php?id=<?= (int) $ticket['reservation_id'] ?>"
                            class="btn btn-secondary">
                            View Reservation
                        </a>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>

</div>

<script src="../assets/js/navigation.js"></script>

</body>
</html>
