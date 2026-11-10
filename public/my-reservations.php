
<?php

require_once __DIR__ . '/../includes/require_login.php';
require_once __DIR__ . '/../config/Database.php';

if (!isAttendee()) {
    header('Location: dashboard.php');
    exit;
}

$reservationId = (int) ($_GET['id'] ?? 0);
$attendeeId = (int) $_SESSION['attendee_id'];

try {

    $database = new Database();
    $db = $database->connect();

    $sql = "
        SELECT
            r.id,
            r.reservation_date,
            r.quantity,
            r.status,
            e.title AS event_title,
            e.event_date,
            e.event_time,
            e.venue
        FROM reservations r
        INNER JOIN events e ON r.event_id = e.id
        WHERE r.attendee_id = :attendee_id
    ";

    $params = [
        ':attendee_id' => $attendeeId
    ];

    if ($reservationId > 0) {
        $sql .= " AND r.id = :reservation_id";
        $params[':reservation_id'] = $reservationId;
    }

    $sql .= " ORDER BY r.id DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);

    $reservations = $stmt->fetchAll();

} catch (Exception $e) {
    die('Unable to load reservations.');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>My Reservations</title>
</head>

<body>

<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<div class="container">

    <div class="form-container">

        <h1>My Reservations</h1>

        <p class="page-description">
            View all your event reservations.
        </p>

        <?php if (empty($reservations)): ?>

            <p>You don't have any reservations yet.</p>

            <a href="browse-events.php" class="btn btn-primary">
                Browse Events
            </a>

        <?php else: ?>

            <?php foreach ($reservations as $reservation): ?>

                <div class="success-section">

                    <h2>
                        <?= htmlspecialchars($reservation['event_title']) ?>
                    </h2>

                    <div class="details-list">

                        <p>
                            <strong>Reservation ID:</strong>
                            <?= (int) $reservation['id'] ?>
                        </p>

                        <p>
                            <strong>Event Date:</strong>
                            <?= htmlspecialchars($reservation['event_date']) ?>
                        </p>

                        <p>
                            <strong>Event Time:</strong>
                            <?= htmlspecialchars($reservation['event_time']) ?>
                        </p>

                        <p>
                            <strong>Venue:</strong>
                            <?= htmlspecialchars($reservation['venue']) ?>
                        </p>

                        <p>
                            <strong>Quantity:</strong>
                            <?= (int) $reservation['quantity'] ?>
                        </p>

                        <p>
                            <strong>Status:</strong>
                            <?= htmlspecialchars($reservation['status']) ?>
                        </p>

                        <p>
                            <strong>Reservation Date:</strong>
                            <?= htmlspecialchars($reservation['reservation_date']) ?>
                        </p>

                    </div>

                    <div class="success-actions">

                        <a
                            href="my-tickets.php?reservation_id=<?= (int) $reservation['id'] ?>"
                            class="btn btn-primary">
                            View Tickets
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
