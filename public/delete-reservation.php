
<?php

require_once __DIR__ . '/../includes/require_admin.php';
require_once __DIR__ . '/../classes/Reservation.php';

$reservationModel = new Reservation();

$reservationId = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);

if ($reservationId <= 0) {
    die('Invalid reservation.');
}

$error = '';

try {

    $reservation = $reservationModel->getReservationById($reservationId);

    if ($reservation === null) {
        die('Reservation not found.');
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // VERIFY RESERVATION ID

        $postedId = (int) ($_POST['id'] ?? 0);

        if ($postedId !== $reservationId) {

            $error = 'Invalid reservation ID.';

        // VERIFY CSRF TOKEN

        } elseif (!verifyCSRFToken($_POST['csrf_token'] ?? null)) {

            $error = 'Invalid or expired request. Please refresh the page and try again.';

        } else {

            $reservationModel->setId($reservationId);

            if ($reservationModel->delete()) {

                header(
                    'Location: ../public/reservations.php?message=' .
                    urlencode('Reservation deleted successfully')
                );

                exit;

            } else {

                $error = 'Failed to delete the reservation.';
            }
        }
    }

} catch (Exception $e) {

    $error = $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Delete Reservation | EventlyPH</title>

    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<?php require_once __DIR__ . '/../includes/navbar.php'; ?>

<main class="container reservation-delete-page">

    <div class="reservation-delete-header">

        <span class="reservation-delete-label">
            RESERVATION MANAGEMENT
        </span>

        <h1>Delete Reservation</h1>

        <p>
            Review the reservation details before confirming deletion.
        </p>

    </div>

    <a href="../public/reservations.php"
       class="reservation-delete-back">
        &larr; Back to Reservations
    </a>

    <?php if ($error !== ''): ?>

        <div class="reservation-delete-error">
            <strong>Unable to delete reservation:</strong>
            <p><?= htmlspecialchars($error) ?></p>
        </div>

    <?php endif; ?>

    <div class="reservation-delete-card">

        <div class="reservation-delete-icon">!</div>

        <h2>Confirm Reservation Deletion</h2>

        <p class="reservation-delete-description">
            You are about to delete the following reservation.
            Please review the information before proceeding.
        </p>

        <div class="reservation-delete-details">

            <div class="reservation-delete-detail">
                <span>Reservation ID</span>
                <strong>#<?= (int) $reservationId ?></strong>
            </div>

            <div class="reservation-delete-detail">
                <span>Event</span>
                <strong>
                    <?= htmlspecialchars($reservation['event_title']) ?>
                </strong>
            </div>

            <div class="reservation-delete-detail">
                <span>Attendee</span>
                <strong>
                    <?= htmlspecialchars($reservation['attendee_name']) ?>
                </strong>
            </div>

            <div class="reservation-delete-detail">
                <span>Ticket Quantity</span>
                <strong>
                    <?= (int) $reservation['quantity'] ?>
                </strong>
            </div>

            <div class="reservation-delete-detail">
                <span>Reservation Status</span>
                <strong>
                    <span class="reservation-delete-status <?=
                        $reservation['status'] === 'CONFIRMED'
                            ? 'is-confirmed'
                            : 'is-cancelled'
                    ?>">
                        <?= htmlspecialchars($reservation['status']) ?>
                    </span>
                </strong>
            </div>

        </div>

        <div class="reservation-delete-warning">
            <strong>Warning:</strong>
            Deleting this reservation may also affect its associated tickets
            and the event's available slots. This action cannot be undone.
        </div>

        <form method="POST" action="">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= htmlspecialchars(generateCSRFToken()) ?>"
            >

            <input
                type="hidden"
                name="id"
                value="<?= (int) $reservationId ?>"
            >

            <div class="reservation-delete-actions">

                <a href="../public/reservations.php"
                class="reservation-delete-cancel">
                    Cancel
                </a>

                <button type="submit" class="reservation-delete-confirm">
                    Yes, Delete Reservation
                </button>

            </div>

        </form>

    </div>

</main>

<script src="../assets/js/navigation.js"></script>

</body>
</html>
